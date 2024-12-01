<?php
class Logger {
    private static $logFile;
    private static $initialized = false;
    private static $maxLogSize = 10485760; // 10MB
    private static $maxLogFiles = 5;

    /**
     * Initialize logger
     */
    public static function init() {
        if (self::$initialized) {
            return;
        }

        self::$logFile = APPROOT . '/../logs/app.log';
        $logDir = dirname(self::$logFile);

        // Create logs directory if it doesn't exist
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Check log rotation
        self::rotateLogIfNeeded();

        self::$initialized = true;
    }

    /**
     * Log an error message
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }

    /**
     * Log an info message
     * 
     * @param string $message Info message
     * @param array $context Additional context
     */
    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }

    /**
     * Log a warning message
     * 
     * @param string $message Warning message
     * @param array $context Additional context
     */
    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }

    /**
     * Log a debug message
     * 
     * @param string $message Debug message
     * @param array $context Additional context
     */
    public static function debug($message, $context = []) {
        if (ENVIRONMENT === 'development') {
            self::log('DEBUG', $message, $context);
        }
    }

    /**
     * Internal logging method
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context
     */
    private static function log($level, $message, $context = []) {
        self::init();

        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($trace[1]) ? basename($trace[1]['file']) . ':' . $trace[1]['line'] : 'unknown';
        
        $logMessage = sprintf(
            "[%s] [%s] [%s] %s%s\n",
            $timestamp,
            $level,
            $caller,
            $message,
            $contextStr
        );

        error_log($logMessage, 3, self::$logFile);
    }

    /**
     * Rotate log file if it exceeds max size
     */
    private static function rotateLogIfNeeded() {
        if (!file_exists(self::$logFile)) {
            return;
        }

        if (filesize(self::$logFile) < self::$maxLogSize) {
            return;
        }

        // Rotate existing backup files
        for ($i = self::$maxLogFiles - 1; $i >= 1; $i--) {
            $oldFile = self::$logFile . '.' . $i;
            $newFile = self::$logFile . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                rename($oldFile, $newFile);
            }
        }

        // Move current log to .1
        rename(self::$logFile, self::$logFile . '.1');

        // Create new empty log file
        touch(self::$logFile);
        chmod(self::$logFile, 0666);
    }

    /**
     * Clear all log files
     */
    public static function clear() {
        self::init();

        // Remove main log file
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }

        // Remove rotated log files
        for ($i = 1; $i <= self::$maxLogFiles; $i++) {
            $rotatedFile = self::$logFile . '.' . $i;
            if (file_exists($rotatedFile)) {
                unlink($rotatedFile);
            }
        }

        // Create new empty log file
        touch(self::$logFile);
        chmod(self::$logFile, 0666);
    }
}

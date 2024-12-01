<?php
class Logger {
    private static $logDir = APPROOT . '/../logs/';
    private static $logLevels = ['ERROR', 'WARNING', 'INFO', 'DEBUG'];
    
    /**
     * Initialize logger
     */
    public static function init() {
        if (!file_exists(self::$logDir)) {
            mkdir(self::$logDir, 0777, true);
        }
    }
    
    /**
     * Log an error message
     * 
     * @param string $message Error message
     * @param array $context Additional context
     */
    public static function error($message, array $context = []) {
        self::log('ERROR', $message, $context);
    }
    
    /**
     * Log a warning message
     * 
     * @param string $message Warning message
     * @param array $context Additional context
     */
    public static function warning($message, array $context = []) {
        self::log('WARNING', $message, $context);
    }
    
    /**
     * Log an info message
     * 
     * @param string $message Info message
     * @param array $context Additional context
     */
    public static function info($message, array $context = []) {
        self::log('INFO', $message, $context);
    }
    
    /**
     * Log a debug message
     * 
     * @param string $message Debug message
     * @param array $context Additional context
     */
    public static function debug($message, array $context = []) {
        self::log('DEBUG', $message, $context);
    }
    
    /**
     * Write log message to file
     * 
     * @param string $level Log level
     * @param string $message Log message
     * @param array $context Additional context
     */
    private static function log($level, $message, array $context = []) {
        if (!in_array($level, self::$logLevels)) {
            throw new Exception('Invalid log level: ' . $level);
        }
        
        $logFile = self::$logDir . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        
        // Format context data
        $contextStr = empty($context) ? '' : ' ' . json_encode($context);
        
        // Format log message
        $logMessage = sprintf(
            "[%s] %s: %s%s%s",
            $timestamp,
            $level,
            $message,
            $contextStr,
            PHP_EOL
        );
        
        // Write to log file
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Get logs for a specific date
     * 
     * @param string $date Date in Y-m-d format
     * @param string $level Optional log level filter
     * @return array Log entries
     */
    public static function getLogs($date, $level = null) {
        $logFile = self::$logDir . $date . '.log';
        if (!file_exists($logFile)) {
            return [];
        }
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($level === null) {
            return $logs;
        }
        
        return array_filter($logs, function($log) use ($level) {
            return strpos($log, "[$level]") !== false;
        });
    }
    
    /**
     * Clean old log files
     * 
     * @param int $days Number of days to keep logs
     */
    public static function clean($days = 30) {
        $files = glob(self::$logDir . '*.log');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                }
            }
        }
    }
}

<?php
require_once __DIR__ . '/../libraries/Logger.php';

class ErrorHandler {
    /**
     * Initialize error handler
     */
    public static function init() {
        // Set error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Set shutdown function
        register_shutdown_function([self::class, 'handleShutdown']);
        
        // Initialize logger
        Logger::init();
    }
    
    /**
     * Handle PHP errors
     * 
     * @param int $errno Error number
     * @param string $errstr Error message
     * @param string $errfile File where error occurred
     * @param int $errline Line number where error occurred
     * @return bool
     */
    public static function handleError($errno, $errstr, $errfile, $errline) {
        if (!(error_reporting() & $errno)) {
            return false;
        }
        
        $context = [
            'file' => $errfile,
            'line' => $errline,
            'type' => self::getErrorType($errno)
        ];
        
        switch ($errno) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
                Logger::error($errstr, $context);
                self::displayError('Fatal Error', $errstr, $context);
                exit(1);
                
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                Logger::warning($errstr, $context);
                break;
                
            case E_NOTICE:
            case E_USER_NOTICE:
                Logger::info($errstr, $context);
                break;
                
            default:
                Logger::debug($errstr, $context);
        }
        
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     * 
     * @param Throwable $exception
     */
    public static function handleException($exception) {
        $context = [
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];
        
        Logger::error($exception->getMessage(), $context);
        
        self::displayError(
            get_class($exception),
            $exception->getMessage(),
            $context
        );
    }
    
    /**
     * Handle fatal errors
     */
    public static function handleShutdown() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE,
            E_RECOVERABLE_ERROR,
            E_USER_ERROR
        ])) {
            $context = [
                'file' => $error['file'],
                'line' => $error['line'],
                'type' => self::getErrorType($error['type'])
            ];
            
            Logger::error($error['message'], $context);
            
            self::displayError('Fatal Error', $error['message'], $context);
        }
    }
    
    /**
     * Display error page
     * 
     * @param string $type Error type
     * @param string $message Error message
     * @param array $context Error context
     */
    private static function displayError($type, $message, $context) {
        if (ob_get_level() > 0) {
            ob_clean();
        }
        
        http_response_code(500);
        
        // Include appropriate error view based on environment
        $errorView = ENVIRONMENT === 'development' 
            ? APPROOT . '/views/errors/development.php'
            : APPROOT . '/views/errors/production.php';
            
        include $errorView;
        exit;
    }
    
    /**
     * Get error type string
     * 
     * @param int $type Error type number
     * @return string Error type string
     */
    private static function getErrorType($type) {
        switch($type) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            default:
                return 'UNKNOWN';
        }
    }
}

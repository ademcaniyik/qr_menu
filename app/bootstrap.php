<?php
// Load Config
require_once 'config/config.php';

// Load Helpers
require_once 'helpers/url_helper.php';
require_once 'helpers/session_helper.php';
require_once 'helpers/ErrorHandler.php';
require_once 'helpers/role_helper.php';

// Initialize Error Handler
ErrorHandler::init();

// Autoload Core Libraries
spl_autoload_register(function($className) {
    $file = 'libraries/' . $className . '.php';
    if (file_exists(__DIR__ . '/' . $file)) {
        require_once $file;
    }
});

// Autoload Models
spl_autoload_register(function($className) {
    $file = 'models/' . $className . '.php';
    if (file_exists(__DIR__ . '/' . $file)) {
        require_once $file;
    }
});

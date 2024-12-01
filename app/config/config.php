<?php
// Environment
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'development'); // Can be 'development', 'testing', or 'production'
}

// DB Params
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'qr_menu_system');

// App Root
if (!defined('APPROOT')) define('APPROOT', dirname(dirname(__FILE__)));
// URL Root
if (!defined('URLROOT')) define('URLROOT', 'http://localhost/qr_menu_system');
if (!defined('URL_ROOT')) define('URL_ROOT', 'http://localhost/qr_menu_system');
// Site Name
if (!defined('SITENAME')) define('SITENAME', 'QR Menu System');

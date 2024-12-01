<?php
require_once dirname(dirname(__FILE__)) . '/app/config/config.php';
require_once dirname(dirname(__FILE__)) . '/vendor/autoload.php';

// Initialize Core Library
require_once APPROOT . '/bootstrap.php';

// Init Core Controller
$init = new Core;

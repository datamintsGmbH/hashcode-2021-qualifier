<?php

// Set default timezone.
date_default_timezone_set('UTC');

// Disable time limit.
set_time_limit(0);

// Define the base path of the application.
define('APP_BASE_PATH', Phar::running() ? Phar::running() : dirname(__FILE__));

// Include Composer's autoloader.
$loader = require_once APP_BASE_PATH . '/vendor/autoload.php';

// Use and start the application.
$app = new \Datamints\HashCode\Qualifier2021\Application(
    'datamints HashCode 2021 Qualifier',
    '@VERSION@'
);
$app->run();

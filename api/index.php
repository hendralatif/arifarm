<?php

// Enable full error reporting to see what's failing
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Check vendor directory exists
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo 'ERROR: vendor/autoload.php not found. Composer install may have failed.';
    exit;
}

// Check if bootstrap/app.php exists
if (!file_exists(__DIR__ . '/../bootstrap/app.php')) {
    http_response_code(500);
    echo 'ERROR: bootstrap/app.php not found.';
    exit;
}

// Forward Vercel Serverless requests to the Laravel bootstrap file
require __DIR__ . '/../public/index.php';

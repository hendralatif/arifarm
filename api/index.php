<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// ============================================================
// SERVERLESS FIX: Create writable storage dirs in /tmp
// /var/task/user/storage is READ-ONLY on Vercel serverless
// ============================================================
$tmpStorage = '/tmp/laravel-storage';
foreach ([
    'logs',
    'framework/sessions',
    'framework/views',
    'framework/cache/data',
    'app/public',
] as $dir) {
    @mkdir($tmpStorage . '/' . $dir, 0775, true);
}

// Set env var so bootstrap/app.php can detect and use /tmp
putenv('VERCEL_STORAGE_PATH=' . $tmpStorage);
$_ENV['VERCEL_STORAGE_PATH'] = $tmpStorage;

// ============================================================
// ERROR CATCHING: Catch fatal errors that PHP normally hides
// ============================================================
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<pre style="background:#1a1a1a;color:#ff6b6b;padding:20px;font-size:14px;">';
        echo '<strong>FATAL ERROR:</strong> ' . htmlspecialchars($error['message']);
        echo "\nFile: " . $error['file'] . ' Line: ' . $error['line'];
        echo '</pre>';
    }
});

// Sanity check
if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo 'ERROR: vendor/autoload.php not found. Composer install may have failed.';
    exit;
}

// ============================================================
// Bootstrap Laravel with full error catching
// ============================================================
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo '<pre style="background:#1a1a1a;color:#ff6b6b;padding:20px;font-size:14px;">';
    echo '<strong>Exception:</strong> ' . htmlspecialchars($e->getMessage());
    echo "\nFile: " . $e->getFile() . ':' . $e->getLine();
    echo "\n\nTrace:\n" . htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}

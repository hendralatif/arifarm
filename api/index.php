<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Register shutdown function to catch fatal errors
register_shutdown_function(function () {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }
        echo '<pre style="background:#1a1a1a;color:#ff6b6b;padding:20px;">';
        echo 'FATAL: ' . htmlspecialchars($error['message']);
        echo "\n" . $error['file'] . ':' . $error['line'];
        echo '</pre>';
    }
});

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    http_response_code(500);
    echo 'ERROR: vendor/autoload.php not found.';
    exit;
}

try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }
    echo '<pre style="background:#1a1a1a;color:#ff6b6b;padding:20px;font-size:13px;">';
    echo '<strong>Exception:</strong> ' . htmlspecialchars($e->getMessage());
    echo "\nFile: " . $e->getFile() . ':' . $e->getLine();
    echo "\n\nTrace:\n" . htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}

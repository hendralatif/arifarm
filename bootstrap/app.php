<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// On Vercel serverless, /var/task/user/storage is READ-ONLY.
// We MUST use /tmp for all writable storage and cache.
// Detect if running on Vercel (or any read-only serverless environment)
$isServerless = !is_writable(dirname(__DIR__) . '/storage/logs');
$storagePath = $isServerless
    ? '/tmp/laravel-storage'
    : dirname(__DIR__) . '/storage';

// Pre-create all required directories in /tmp
if ($isServerless) {
    foreach ([
        'logs',
        'framework/sessions',
        'framework/views',
        'framework/cache/data',
        'app/public',
        'bootstrap/cache',
    ] as $dir) {
        @mkdir($storagePath . '/' . $dir, 0775, true);
    }
    
    // Set environment variables to move package & services cache to /tmp
    putenv('APP_SERVICES_CACHE=' . $storagePath . '/bootstrap/cache/services.php');
    putenv('APP_PACKAGES_CACHE=' . $storagePath . '/bootstrap/cache/packages.php');
    $_ENV['APP_SERVICES_CACHE'] = $storagePath . '/bootstrap/cache/services.php';
    $_ENV['APP_PACKAGES_CACHE'] = $storagePath . '/bootstrap/cache/packages.php';
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            '/payment/midtrans/notification'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Use Symfony Response directly to avoid depending on 'view' service
        // This ensures errors are visible even if ViewServiceProvider hasn't booted
        $exceptions->render(function (\Throwable $e, $request) {
            $content = '<pre style="background:#111;color:#f88;padding:20px;font-family:monospace;white-space:pre-wrap;">';
            $content .= '<b>ORIGINAL ERROR: ' . get_class($e) . '</b>' . "\n\n";
            $content .= htmlspecialchars($e->getMessage()) . "\n\n";
            $content .= 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n\n";
            $content .= 'Trace:' . "\n" . htmlspecialchars($e->getTraceAsString());
            $content .= '</pre>';
            return new \Symfony\Component\HttpFoundation\Response(
                $content, 500, ['Content-Type' => 'text/html; charset=utf-8']
            );
        });
    })->create();

// Apply the writable storage path BEFORE handleRequest is called
$app->useStoragePath($storagePath);

return $app;

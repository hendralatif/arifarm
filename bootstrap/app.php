<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

// On Vercel serverless, /var/task/user/storage is READ-ONLY.
// We MUST use /tmp for all writable storage.
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
    ] as $dir) {
        @mkdir($storagePath . '/' . $dir, 0775, true);
    }
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
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();

// Apply the writable storage path BEFORE handleRequest is called
$app->useStoragePath($storagePath);

return $app;

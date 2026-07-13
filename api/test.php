<?php
// PHP Diagnostic tool
echo "<h2>Ari Farm - Laravel Boot Diagnostic</h2>";

try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Boot the application kernel to load all configurations
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $kernel->bootstrap();

    echo "<h3>Laravel Configuration Values:</h3><ul>";
    echo "<li>database.default = " . config('database.default') . "</li>";
    echo "<li>mysql.host = " . config('database.connections.mysql.host') . "</li>";
    echo "<li>mysql.port = " . config('database.connections.mysql.port') . "</li>";
    echo "<li>mysql.database = " . config('database.connections.mysql.database') . "</li>";
    echo "<li>mysql.username = " . config('database.connections.mysql.username') . "</li>";
    echo "</ul>";
} catch (\Throwable $e) {
    echo "<p style='color:red;'>Laravel Boot Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

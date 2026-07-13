<?php
// PHP Diagnostic tool
echo "<h2>Ari Farm - PHP Diagnostic</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

echo "<h3>Env Variables:</h3><ul>";
$keys = ['APP_KEY','APP_ENV','DB_CONNECTION','DB_HOST','DB_PORT','DB_DATABASE','DB_USERNAME'];
foreach ($keys as $k) {
    $val = getenv($k) ?: ($_ENV[$k] ?? 'NOT SET');
    if (str_contains($k, 'KEY') || str_contains($k, 'PASS')) $val = '***hidden***';
    echo "<li>$k = $val</li>";
}
echo "</ul>";

echo "<h3>Config values from Laravel:</h3>";
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    // bootstrap the application config only
    $app->bootstrapWith([
        \Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
        \Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    ]);
    
    echo "<ul>";
    echo "<li>config('database.default') = " . config('database.default') . "</li>";
    echo "<li>config('database.connections.mysql.host') = " . config('database.connections.mysql.host') . "</li>";
    echo "<li>config('database.connections.mysql.database') = " . config('database.connections.mysql.database') . "</li>";
    echo "<li>config('database.connections.mysql.username') = " . config('database.connections.mysql.username') . "</li>";
    echo "</ul>";
} catch (\Throwable $e) {
    echo "<p style='color:red;'>Failed to load configuration: " . $e->getMessage() . "</p>";
}

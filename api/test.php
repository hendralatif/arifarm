<?php
// Ultra-minimal diagnostic file for Vercel PHP debugging
echo "<h2>Ari Farm - PHP Diagnostic</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'unknown') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'unknown') . "</p>";
echo "<p>Script: " . __FILE__ . "</p>";
echo "<p>Dir: " . __DIR__ . "</p>";

echo "<h3>Directory Contents:</h3><ul>";
$files = scandir(__DIR__ . '/..');
foreach ($files as $f) {
    echo "<li>$f</li>";
}
echo "</ul>";

echo "<h3>Vendor exists:</h3>";
echo file_exists(__DIR__ . '/../vendor/autoload.php') ? '✅ YES' : '❌ NO';

echo "<h3>Env Variables:</h3><ul>";
$keys = ['APP_KEY','APP_ENV','DB_CONNECTION','DB_HOST'];
foreach ($keys as $k) {
    $val = getenv($k) ?: ($_ENV[$k] ?? 'NOT SET');
    if (str_contains($k, 'KEY') || str_contains($k, 'PASS')) $val = '***hidden***';
    echo "<li>$k = $val</li>";
}
echo "</ul>";

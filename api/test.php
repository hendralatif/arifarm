<?php
// PHP Diagnostic tool
echo "<h2>Ari Farm - PHP Diagnostic</h2>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

echo "<h3>Vercel environment check:</h3>";
echo "getenv('VERCEL') = '" . getenv('VERCEL') . "'<br>";

echo "<h3>Raw config/database.php values:</h3>";
try {
    // Load composer autoloader to resolve Laravel classes (like Str)
    require __DIR__ . '/../vendor/autoload.php';

    // Mock the env() helper since Laravel application isn't booted
    if (!function_exists('env')) {
        function env($key, $default = null) {
            $val = getenv($key);
            return $val !== false ? $val : $default;
        }
    }
    
    // Mock database_path() helper
    if (!function_exists('database_path')) {
        function database_path($path = '') {
            return '/dummy/database/' . $path;
        }
    }
    
    $dbConfig = include __DIR__ . '/../config/database.php';
    echo "<pre>";
    // Hide password for safety
    if (isset($dbConfig['connections']['mysql']['password'])) {
        $dbConfig['connections']['mysql']['password'] = '***hidden***';
    }
    print_r($dbConfig['connections']['mysql']);
    echo "</pre>";
} catch (\Throwable $e) {
    echo "<p style='color:red;'>Failed to load database config: " . $e->getMessage() . "</p>";
}

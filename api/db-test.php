<?php
// DB Connection Test - HAPUS FILE INI SETELAH SELESAI DEBUG
header('Content-Type: text/html; charset=utf-8');

echo '<pre style="background:#111;color:#0f0;padding:20px;font-family:monospace;">';
echo "=== DATABASE CONNECTION TEST ===\n\n";

// Show env vars (masked)
$host = getenv('DB_HOST') ?: 'NOT SET';
$port = getenv('DB_PORT') ?: 'NOT SET';
$dbname = getenv('DB_DATABASE') ?: 'NOT SET';
$user = getenv('DB_USERNAME') ?: 'NOT SET';
$pass = getenv('DB_PASSWORD') ?: 'NOT SET';
$conn = getenv('DB_CONNECTION') ?: 'NOT SET';

echo "DB_CONNECTION: $conn\n";
echo "DB_HOST: $host\n";
echo "DB_PORT: $port\n";
echo "DB_DATABASE: $dbname\n";
echo "DB_USERNAME: $user\n";
echo "DB_PASSWORD: " . (strlen($pass) > 4 ? substr($pass, 0, 4) . '****' . substr($pass, -4) : $pass) . "\n";
echo "VERCEL env: " . (getenv('VERCEL') ?: 'NOT SET') . "\n\n";

// Try connecting
echo "--- Attempting MySQL connection ---\n";

if ($host === 'NOT SET' || $host === '127.0.0.1') {
    echo "ERROR: DB_HOST not configured for production!\n";
} else {
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 10,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];
        
        echo "Connecting to: $dsn\n";
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "\n✅ CONNECTION SUCCESSFUL!\n";
        
        // Try a simple query
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "\nTables in database:\n";
        if (empty($tables)) {
            echo "  (no tables yet - need to run migrations)\n";
        } else {
            foreach ($tables as $table) {
                echo "  - $table\n";
            }
        }
    } catch (PDOException $e) {
        echo "\n❌ CONNECTION FAILED!\n";
        echo "Error: " . $e->getMessage() . "\n";
        echo "Code: " . $e->getCode() . "\n";
        
        // Diagnose common issues
        if (strpos($e->getMessage(), '1045') !== false) {
            echo "\n⚠️  Access Denied - Check credentials or IP whitelist\n";
        } elseif (strpos($e->getMessage(), '2002') !== false || strpos($e->getMessage(), 'Connection refused') !== false) {
            echo "\n⚠️  Cannot reach host - Check DB_HOST and DB_PORT\n";
        } elseif (strpos($e->getMessage(), '2003') !== false) {
            echo "\n⚠️  Host not reachable - Firewall or IP whitelist issue\n";
        }
    }
}

echo "\n=== END TEST ===";
echo '</pre>';

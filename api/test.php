<?php
// Dump contents of bootstrap/cache on Vercel
echo "<h2>bootstrap/cache files on Vercel</h2>";
$files = scandir(__DIR__ . '/../bootstrap/cache');
echo "<ul>";
foreach ($files as $f) {
    echo "<li>$f</li>";
}
echo "</ul>";

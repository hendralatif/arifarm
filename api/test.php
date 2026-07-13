<?php
// Dump all $_SERVER and $_ENV keys to see what matches Vercel
echo "<h2>All Server & Env Keys</h2>";

echo "<h3>$_SERVER keys:</h3><ul>";
foreach (array_keys($_SERVER) as $k) {
    echo "<li>$k = " . (str_contains($k, 'KEY') || str_contains($k, 'PASS') ? '***' : $_SERVER[$k]) . "</li>";
}
echo "</ul>";

echo "<h3>getenv() keys:</h3><ul>";
foreach (array_keys(getenv()) as $k) {
    echo "<li>$k = " . (str_contains($k, 'KEY') || str_contains($k, 'PASS') ? '***' : getenv($k)) . "</li>";
}
echo "</ul>";

#!/opt/render/project/src/.bin/php
<?php
// PHP build step script for Render hosting.
// It runs during deployment to compile assets, database configuration and migration.

echo "--- STARTING LARAVEL PRODUCTION BUILD ---" . PHP_EOL;

// 1. Install npm dependencies and build frontend assets
echo "Installing Node dependencies..." . PHP_EOL;
shell_exec('npm install');

echo "Building production assets (Vite)..." . PHP_EOL;
shell_exec('npm run build');

// 2. Clear caches to ensure configuration changes apply
echo "Clearing optimization caches..." . PHP_EOL;
shell_exec('php artisan config:clear');
shell_exec('php artisan view:clear');
shell_exec('php artisan route:clear');

echo "Optimizing framework files..." . PHP_EOL;
shell_exec('php artisan config:cache');
shell_exec('php artisan route:cache');
shell_exec('php artisan view:cache');

echo "--- BUILD COMPLETED SUCCESSFULLY ---" . PHP_EOL;

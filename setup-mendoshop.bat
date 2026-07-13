@echo off
echo ==========================================
echo   Ari Farm - Setup Script
echo ==========================================
echo.

REM Step 1: Start MySQL from XAMPP
echo [1/5] Starting MySQL (XAMPP)...
IF EXIST "C:\xampp\mysql\bin\mysqld.exe" (
    start /B C:\xampp\mysql\bin\mysqld.exe --standalone
    timeout /t 4 /nobreak > nul
    echo       MySQL started!
) ELSE (
    echo       XAMPP MySQL not found. Please start MySQL manually.
    pause
    exit /b 1
)

REM Step 2: Create Database
echo [2/5] Creating database 'Ari Farm'...
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS Ari Farm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
IF %ERRORLEVEL% EQU 0 (
    echo       Database created successfully!
) ELSE (
    echo       Warning: Could not create DB. It may already exist or MySQL is still starting.
    timeout /t 3 /nobreak > nul
    C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS Ari Farm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
)

REM Step 3: Run Migrations
echo [3/5] Running database migrations...
php artisan migrate --force
IF %ERRORLEVEL% NEQ 0 (
    echo       Migration failed! Check MySQL connection.
    pause
    exit /b 1
)
echo       Migrations completed!

REM Step 4: Run Seeders
echo [4/5] Seeding database with demo data...
php artisan db:seed --force
IF %ERRORLEVEL% NEQ 0 (
    echo       Seeding failed!
    pause
    exit /b 1
)
echo       Database seeded successfully!

REM Step 5: Create storage link
echo [5/5] Creating storage link...
php artisan storage:link

echo.
echo ==========================================
echo   Setup Complete! 
echo ==========================================
echo.
echo   Login Credentials:
echo   Admin  : admin@arifarm.com  / password
echo   User   : user@arifarm.com   / password
echo.
echo   Run "php artisan serve" to start the server
echo   Then open: http://localhost:8000
echo.
pause

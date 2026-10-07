@echo off
echo ============================================================
echo COLLEGE EVENT MANAGEMENT SYSTEM - AUTO DEPLOY SCRIPT
echo ============================================================

set SOURCE=%~dp0
set TARGET=C:\xampp\htdocs\college_event_management

if not exist "C:\xampp" (
    echo [ERROR] XAMPP installation not found at C:\xampp.
    echo Please install XAMPP first and start Apache and MySQL.
    pause
    exit /b 1
)

echo [*] Deploying project to %TARGET% ...
if not exist "%TARGET%" mkdir "%TARGET%"
xcopy "%SOURCE%*" "%TARGET%\" /E /I /Y /Q

echo [+] Files deployed successfully to C:\xampp\htdocs\college_event_management\

if exist "C:\xampp\mysql\bin\mysql.exe" (
    echo [*] Checking MySQL service to auto-import database...
    "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS college_event_management;" 2>nul
    if %ERRORLEVEL% EQU 0 (
        "C:\xampp\mysql\bin\mysql.exe" -u root college_event_management < "%SOURCE%database.sql"
        echo [+] Database imported successfully!
    ) else (
        echo [!] MySQL service is not running yet.
        echo Start MySQL from XAMPP Control Panel, then import database.sql via phpMyAdmin.
    )
)

echo.
echo ============================================================
echo DEPLOYMENT COMPLETE!
echo Open in browser: http://localhost/college_event_management/
echo ============================================================
pause

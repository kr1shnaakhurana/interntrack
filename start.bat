@echo off
title InternTrack - Local Server
cd /d "%~dp0"

:: Ensure PHP from XAMPP and PostgreSQL are available in PATH
set "PATH=C:\xampp\php;C:\Program Files\PostgreSQL\18\bin;%PATH%"

echo ===================================================
echo             InternTrack Career Platform
echo ===================================================
echo.
echo Database: PostgreSQL (interntrack)
echo Server:   http://127.0.0.1:8000
echo.
echo Demo Accounts (Password for all: password):
echo   - Admin:   admin@interntrack.dev
echo   - Company: company@interntrack.dev
echo   - Student: student@interntrack.dev
echo.
echo ===================================================
echo Starting Laravel Server...
echo Press Ctrl+C in this window anytime to stop.
echo.

:: Launch the browser after 2 seconds in background
start "" cmd /c "timeout /t 2 /nobreak >nul & start http://127.0.0.1:8000"

:: Run the Laravel server
php artisan serve --host=127.0.0.1 --port=8000

pause

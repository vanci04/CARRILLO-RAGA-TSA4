@echo off
setlocal
cd /d "%~dp0"
set "EMBER_PHP=php"
if exist "%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe" set "EMBER_PHP=%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.4_Microsoft.Winget.Source_8wekyb3d8bbwe\php.exe"
"%EMBER_PHP%" -v >nul 2>&1
if errorlevel 1 (
 echo PHP 8.2 or newer is required. See README.md.
 pause
 exit /b 1
)
if not exist "vendor\autoload.php" (
 echo Run composer install in this folder first. See README.md.
 pause
 exit /b 1
)
if not exist ".env" copy ".env.example" ".env" >nul
findstr /r /c:"^APP_KEY=base64:" .env >nul
if errorlevel 1 "%EMBER_PHP%" artisan key:generate --no-interaction
set "EMBER_FIRST_RUN=0"
if not exist "database\database.sqlite" type nul > "database\database.sqlite"
for %%I in ("database\database.sqlite") do if %%~zI==0 set "EMBER_FIRST_RUN=1"
"%EMBER_PHP%" artisan config:clear
"%EMBER_PHP%" artisan migrate --no-interaction
if errorlevel 1 (
 pause
 exit /b 1
)
if "%EMBER_FIRST_RUN%"=="1" (
 "%EMBER_PHP%" artisan db:seed --no-interaction
 if errorlevel 1 (
  pause
  exit /b 1
 )
)
echo.
echo TSA4 - EMBER DAILY
echo Open http://127.0.0.1:8004 in your browser.
echo Keep this window open. Press Ctrl+C to stop the app.
"%EMBER_PHP%" artisan serve --host=127.0.0.1 --port=8004 --no-reload
pause

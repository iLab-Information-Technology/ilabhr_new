@echo off
setlocal enabledelayedexpansion

set PHP=C:\xampp\php\php.exe
set HOST=127.0.0.1
set PORT=8000

cd /d %~dp0
echo Starting Laravel with: %PHP%
"%PHP%" -v

set APP_ENV=local
"%PHP%" artisan serve --host %HOST% --port %PORT%

endlocal
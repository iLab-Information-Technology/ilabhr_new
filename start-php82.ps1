param(
  [int]$Port = 8000,
  [string]$Host = "127.0.0.1",
  [string]$Php = "C:\xampp\php\php.exe"
)

$ErrorActionPreference = 'Stop'
$here = Split-Path -Parent $MyInvocation.MyCommand.Path
Set-Location $here

Write-Host "Starting Laravel with PHP 8.2+: $Php" -ForegroundColor Cyan
& $Php -v

$env:APP_ENV = "local"

& $Php artisan serve --host $Host --port $Port

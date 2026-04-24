param(
  [string]$BackendHost = '0.0.0.0',
  [int]$BackendPort = 8000
)

$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
$backendPath = Join-Path $projectRoot 'backend-laravel'
$phpExe = 'C:\xampp\php\php.exe'
$mysqlAdminExe = 'C:\xampp\mysql\bin\mysqladmin.exe'
$mysqlExe = 'C:\xampp\mysql\bin\mysql.exe'
$mysqlStartBat = 'C:\xampp\mysql_start.bat'
$sqliteDbPath = Join-Path $backendPath 'database\database.sqlite'
$databaseMode = 'mysql'
$mysqlDatabase = 'darna_laravel'
$healthHost = if ($BackendHost -eq '0.0.0.0') { '127.0.0.1' } else { $BackendHost }
$lanHost = $null

if ($BackendHost -eq '0.0.0.0') {
  try {
    $lanHost = Get-NetIPAddress -AddressFamily IPv4 |
      Where-Object { $_.IPAddress -notlike '127.*' -and $_.IPAddress -notlike '169.254*' } |
      Select-Object -First 1 -ExpandProperty IPAddress
  } catch {
    $lanHost = $null
  }
}

function Test-HttpEndpoint {
  param([string]$Url)

  try {
    $response = Invoke-WebRequest -UseBasicParsing $Url -TimeoutSec 4
    return $response.StatusCode -ge 200 -and $response.StatusCode -lt 500
  } catch {
    return $false
  }
}

function Test-MySqlAlive {
  if (-not (Test-Path $mysqlAdminExe)) {
    return $false
  }

  try {
    & $mysqlAdminExe -u root ping *> $null
    return $LASTEXITCODE -eq 0
  } catch {
    return $false
  }
}

if (-not (Test-Path $phpExe)) {
  throw "PHP introuvable: $phpExe"
}

if (-not (Test-Path $backendPath)) {
  throw "Dossier backend introuvable: $backendPath"
}

Write-Host 'Checking database...'

$mysqlAlive = $false
$mysqlAlive = Test-MySqlAlive

if (-not $mysqlAlive) {
  if (Test-Path $mysqlStartBat) {
    Write-Host 'Starting MySQL from XAMPP...'
    Start-Process -FilePath $mysqlStartBat | Out-Null
    Start-Sleep -Seconds 6
    $mysqlAlive = Test-MySqlAlive
  }
}

if ($mysqlAlive) {
  if (Test-Path $mysqlExe) {
    & $mysqlExe -u root -e "CREATE DATABASE IF NOT EXISTS $mysqlDatabase CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" | Out-Null
  }

  Push-Location $backendPath
  try {
    & $phpExe artisan migrate --force | Out-Null
    & $phpExe artisan db:seed --force | Out-Null
  } finally {
    Pop-Location
  }
}

if (-not $mysqlAlive) {
  $databaseMode = 'sqlite'
  Write-Host "MySQL unavailable. Falling back to SQLite at $sqliteDbPath"

  if (-not (Test-Path $sqliteDbPath)) {
    New-Item -ItemType File -Path $sqliteDbPath -Force | Out-Null
  }

  Push-Location $backendPath
  try {
    $env:DB_CONNECTION = 'sqlite'
    $env:DB_DATABASE = $sqliteDbPath

    & $phpExe artisan migrate:fresh --seed --force --no-interaction | Out-Null
  } finally {
    Pop-Location
    Remove-Item Env:DB_CONNECTION -ErrorAction SilentlyContinue
    Remove-Item Env:DB_DATABASE -ErrorAction SilentlyContinue
  }
}

$backendUrl = "http://$healthHost`:$BackendPort/api/home"
$siteUrl = "http://$healthHost`:$BackendPort/"

if (-not (Test-HttpEndpoint $backendUrl)) {
  Write-Host 'Starting Laravel app...'
  if ($databaseMode -eq 'sqlite') {
    $serveCommand = @"
`$env:DB_CONNECTION='sqlite'
`$env:DB_DATABASE='$sqliteDbPath'
Set-Location '$backendPath'
& '$phpExe' artisan serve --host=$BackendHost --port=$BackendPort
"@

    Start-Process -FilePath 'powershell.exe' -ArgumentList @(
      '-NoProfile',
      '-ExecutionPolicy', 'Bypass',
      '-Command', $serveCommand
    ) | Out-Null
  } else {
    Start-Process -FilePath $phpExe -ArgumentList @(
      'artisan',
      'serve',
      "--host=$BackendHost",
      "--port=$BackendPort"
    ) -WorkingDirectory $backendPath | Out-Null
  }
  Start-Sleep -Seconds 4
} else {
  Write-Host 'Laravel app already running.'
}

Write-Host ''
Write-Host "Home:     $siteUrl"
if ($lanHost) {
  Write-Host "LAN:      http://$lanHost`:$BackendPort/"
}
Write-Host "API:      http://$BackendHost`:$BackendPort/api/home"
Write-Host "Property: http://$BackendHost`:$BackendPort/stays/riad-al-baraka"
Write-Host "Booking:  http://$BackendHost`:$BackendPort/reservation/riad-al-baraka"
Write-Host "Database: $databaseMode"

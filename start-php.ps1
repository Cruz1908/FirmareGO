param(
  [string]$PhpPath
)
$ErrorActionPreference = 'SilentlyContinue'

function Find-PHP {
  param([string[]]$Candidates)
  foreach ($c in $Candidates) {
    if ($c -and (Test-Path $c)) { return $c }
  }
  return $null
}

# Si viene por parámetro y existe, úsalo
if ($PhpPath -and (Test-Path $PhpPath)) {
  Write-Host "Usando PHP en: $PhpPath" -ForegroundColor Green
  & $PhpPath -S localhost:8000 index.php
  exit $LASTEXITCODE
}

# Buscar en PATH
$cmdPhp = Get-Command php
$pathPhp = $null
if ($cmdPhp) { $pathPhp = $cmdPhp.Source }

# Candidatos conocidos
$candidates = @(
  $pathPhp,
  'C:\xampp\php\php.exe',
  'C:\Program Files\xampp\php\php.exe',
  'C:\Program Files (x86)\xampp\php\php.exe',
  'C:\php\php.exe',
  'C:\Program Files\php\php.exe'
)

# WAMP
if (Test-Path 'C:\wamp64\bin\php') {
  $wamp = Get-ChildItem 'C:\wamp64\bin\php' -Directory | Sort-Object Name -Descending | ForEach-Object { Join-Path $_.FullName 'php.exe' }
  $candidates += $wamp
}

# Laragon
if (Test-Path 'C:\laragon\bin\php') {
  $laragon = Get-ChildItem 'C:\laragon\bin\php' -Directory | Sort-Object Name -Descending | ForEach-Object { Join-Path $_.FullName 'php.exe' }
  $candidates += $laragon
}

# OpenServer / Bitnami / UwAmp / Scoop / Zend / Chocolatey
$moreBases = @(
  'C:\OpenServer\modules\php',
  'C:\Bitnami',
  'C:\UwAmp\bin\php',
  'C:\Scoop\apps\php\current',
  'C:\Program Files\Zend\ZendServer\bin',
  'C:\Chocolatey\lib\php\tools'
)
foreach ($base in $moreBases) {
  if (Test-Path $base) {
    $candidates += (Get-ChildItem $base -Recurse -Filter 'php.exe' | Select-Object -ExpandProperty FullName)
  }
}

$phpExe = Find-PHP -Candidates $candidates
if ($phpExe) {
  Write-Host "Usando PHP en: $phpExe" -ForegroundColor Green
  & $phpExe -S localhost:8000 index.php
  exit $LASTEXITCODE
}

Write-Host 'PHP no encontrado en rutas comunes (XAMPP/WAMP/Laragon/OpenServer/Bitnami/UwAmp/Scoop/Zend/Chocolatey) ni en PATH.' -ForegroundColor Yellow
Write-Host 'Indica la ruta a php.exe, por ejemplo:'
Write-Host "  .\\start-php.ps1 -PhpPath 'C:\\xampp\\php\\php.exe'" -ForegroundColor Cyan
exit 1
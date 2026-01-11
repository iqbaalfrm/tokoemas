
# Script Simulasi Lokal Multi-Tenant (PowerShell)
# Pastikan XAMPP/MySQL sudah jalan!

# 1. SETUP ENV VARS
$env:DB_CONNECTION = "mysql"
$env:DB_HOST = "127.0.0.1"
$env:DB_PORT = "3306"
$env:DB_USERNAME = "root"
$env:DB_PASSWORD = "" # Ubah jika ada password

# 2. DETEKSI MYSQL & BUAT DATABASE
$mysqlCmd = "mysql"
if (!(Get-Command "mysql" -ErrorAction SilentlyContinue)) {
    # Coba path default XAMPP/Laragon
    if (Test-Path "C:\xampp\mysql\bin\mysql.exe") { 
        $mysqlCmd = "C:\xampp\mysql\bin\mysql.exe" 
    }
    elseif (Test-Path "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe") {
        $mysqlCmd = "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe" 
    }
    else {
        Write-Error "MySQL tidak ditemukan di PATH atau folder standar. Pastikan XAMPP jalan!"
        exit
    }
}

Write-Host "Creating Databases using $mysqlCmd..." -ForegroundColor Cyan
& $mysqlCmd -u root -e "CREATE DATABASE IF NOT EXISTS harm7631_watesutama;"
& $mysqlCmd -u root -e "CREATE DATABASE IF NOT EXISTS harm7631_sentoloutama;"
& $mysqlCmd -u root -e "CREATE DATABASE IF NOT EXISTS harm7631_members;"

# 3. MIGRATE & SEED (Wates)
Write-Host "Migrating Wates..." -ForegroundColor Green
cd sites/wates
cp .env.harto .env
php ../../core/artisan migrate:fresh --seed --force
cd ../..

# 4. MIGRATE & SEED (Sentolo)
Write-Host "Migrating Sentolo..." -ForegroundColor Green
cd sites/sentolo
cp .env.harto .env
php ../../core/artisan migrate:fresh --seed --force
cd ../..

# 5. START SERVERS (Parallel)
Write-Host "Starting Servers..." -ForegroundColor Yellow
Write-Host "Wates Harto: http://localhost:8000"
Write-Host "Wates Ibnu:  http://localhost:8001"
Write-Host "Sentolo Hrt: http://localhost:8002"
Write-Host "Sentolo Ibu: http://localhost:8003"
Write-Host "Press Ctrl+C to stop all."

# Jalankan 4 server PHP Built-in secara parallel
Start-Process php -ArgumentList "-S localhost:8000 -t sites/wates/public" -NoNewWindow
Start-Process php -ArgumentList "-S localhost:8001 -t sites/wates/public" -NoNewWindow
Start-Process php -ArgumentList "-S localhost:8002 -t sites/sentolo/public" -NoNewWindow
Start-Process php -ArgumentList "-S localhost:8003 -t sites/sentolo/public" -NoNewWindow

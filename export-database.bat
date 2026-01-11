@echo off
REM =====================================================
REM SCRIPT EXPORT DATABASE UNTUK UPLOAD
REM =====================================================
REM Jalankan script ini untuk export database lokal ke SQL
REM yang bisa diimport via phpMyAdmin di hosting

echo =====================================================
echo    EXPORT DATABASE TOKO EMAS
echo =====================================================
echo.

set MYSQL_PATH=C:\xampp\mysql\bin\mysqldump.exe
set OUTPUT_DIR=%~dp0database\exports

REM Buat folder output jika belum ada
if not exist "%OUTPUT_DIR%" mkdir "%OUTPUT_DIR%"

echo.
echo Masukkan credentials database lokal:
echo.

set /p DB_USER=Username MySQL (default: root): 
if "%DB_USER%"=="" set DB_USER=root

set /p DB_PASS=Password MySQL (kosong jika tidak ada): 

set /p DB_NAME=Nama database lokal: 

echo.
echo [1/2] Mengexport struktur database...

if "%DB_PASS%"=="" (
    "%MYSQL_PATH%" -u %DB_USER% --no-data %DB_NAME% > "%OUTPUT_DIR%\schema.sql"
) else (
    "%MYSQL_PATH%" -u %DB_USER% -p%DB_PASS% --no-data %DB_NAME% > "%OUTPUT_DIR%\schema.sql"
)

echo [2/2] Mengexport data (tanpa struktur)...

if "%DB_PASS%"=="" (
    "%MYSQL_PATH%" -u %DB_USER% --no-create-info %DB_NAME% > "%OUTPUT_DIR%\data.sql"
) else (
    "%MYSQL_PATH%" -u %DB_USER% -p%DB_PASS% --no-create-info %DB_NAME% > "%OUTPUT_DIR%\data.sql"
)

echo.
echo =====================================================
echo    EXPORT SELESAI!
echo =====================================================
echo.
echo File tersimpan di:
echo - %OUTPUT_DIR%\schema.sql (struktur tabel)
echo - %OUTPUT_DIR%\data.sql (data)
echo.
echo UNTUK IMPORT DI HOSTING:
echo 1. Buka phpMyAdmin
echo 2. Pilih database tujuan
echo 3. Klik tab Import
echo 4. Upload schema.sql terlebih dahulu
echo 5. Lalu upload data.sql
echo.
echo CATATAN:
echo - Import schema.sql ke: DB Wates, DB Sentolo, DB Member
echo - Import data.sql sesuai kebutuhan
echo.
pause

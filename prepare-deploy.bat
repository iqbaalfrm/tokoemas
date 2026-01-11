@echo off
REM =====================================================
REM SCRIPT PERSIAPAN DEPLOYMENT TOKO EMAS
REM =====================================================
REM Jalankan script ini di lokal sebelum upload ke hosting
REM Script ini akan menyiapkan folder tokoemas-deploy/

echo =====================================================
echo    PERSIAPAN DEPLOYMENT TOKO EMAS MULTI-DOMAIN
echo =====================================================
echo.

REM Set variabel
set SOURCE_DIR=%~dp0
set DEPLOY_DIR=%SOURCE_DIR%tokoemas-deploy

REM Hapus folder deploy lama jika ada
if exist "%DEPLOY_DIR%" (
    echo [1/8] Menghapus folder deploy lama...
    rmdir /s /q "%DEPLOY_DIR%"
)

REM Buat struktur folder
echo [2/8] Membuat struktur folder deployment...
mkdir "%DEPLOY_DIR%"
mkdir "%DEPLOY_DIR%\core"
mkdir "%DEPLOY_DIR%\sites"

REM Copy core Laravel
echo [3/8] Menyalin core Laravel (ini memakan waktu)...
xcopy /E /I /Q "%SOURCE_DIR%app" "%DEPLOY_DIR%\core\app"
xcopy /E /I /Q "%SOURCE_DIR%bootstrap" "%DEPLOY_DIR%\core\bootstrap"
xcopy /E /I /Q "%SOURCE_DIR%config" "%DEPLOY_DIR%\core\config"
xcopy /E /I /Q "%SOURCE_DIR%database" "%DEPLOY_DIR%\core\database"
xcopy /E /I /Q "%SOURCE_DIR%lang" "%DEPLOY_DIR%\core\lang"
xcopy /E /I /Q "%SOURCE_DIR%public" "%DEPLOY_DIR%\core\public"
xcopy /E /I /Q "%SOURCE_DIR%resources" "%DEPLOY_DIR%\core\resources"
xcopy /E /I /Q "%SOURCE_DIR%routes" "%DEPLOY_DIR%\core\routes"
xcopy /E /I /Q "%SOURCE_DIR%storage" "%DEPLOY_DIR%\core\storage"
xcopy /E /I /Q "%SOURCE_DIR%vendor" "%DEPLOY_DIR%\core\vendor"
copy "%SOURCE_DIR%artisan" "%DEPLOY_DIR%\core\"
copy "%SOURCE_DIR%composer.json" "%DEPLOY_DIR%\core\"
copy "%SOURCE_DIR%composer.lock" "%DEPLOY_DIR%\core\"

REM Copy sites
echo [4/8] Menyalin folder sites...
xcopy /E /I /Q "%SOURCE_DIR%sites" "%DEPLOY_DIR%\sites"

REM Buat folder storage untuk setiap site
echo [5/8] Menyiapkan storage untuk setiap site...
for %%S in (wates wates1 sentolo sentolo1) do (
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\app\public\%%S\products" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\app\public\%%S\receipts" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\app\public\%%S\ktp" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\framework\cache\data" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\framework\sessions" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\framework\views" 2>nul
    mkdir "%DEPLOY_DIR%\sites\%%S\storage\logs" 2>nul
    
    REM Buat folder storage di public (pengganti symlink)
    mkdir "%DEPLOY_DIR%\sites\%%S\public\storage" 2>nul
    
    REM Copy .gitkeep
    echo. > "%DEPLOY_DIR%\sites\%%S\storage\logs\.gitkeep"
)

REM Copy assets ke setiap site public
echo [6/8] Menyalin assets ke setiap site...
for %%S in (wates wates1 sentolo sentolo1) do (
    if exist "%SOURCE_DIR%public\build" xcopy /E /I /Q "%SOURCE_DIR%public\build" "%DEPLOY_DIR%\sites\%%S\public\build"
    if exist "%SOURCE_DIR%public\css" xcopy /E /I /Q "%SOURCE_DIR%public\css" "%DEPLOY_DIR%\sites\%%S\public\css"
    if exist "%SOURCE_DIR%public\js" xcopy /E /I /Q "%SOURCE_DIR%public\js" "%DEPLOY_DIR%\sites\%%S\public\js"
    if exist "%SOURCE_DIR%public\images" xcopy /E /I /Q "%SOURCE_DIR%public\images" "%DEPLOY_DIR%\sites\%%S\public\images"
    if exist "%SOURCE_DIR%public\vendor" xcopy /E /I /Q "%SOURCE_DIR%public\vendor" "%DEPLOY_DIR%\sites\%%S\public\vendor"
    copy "%SOURCE_DIR%public\favicon.ico" "%DEPLOY_DIR%\sites\%%S\public\" 2>nul
    copy "%SOURCE_DIR%public\robots.txt" "%DEPLOY_DIR%\sites\%%S\public\" 2>nul
)

REM Buat bootstrap/cache writable
echo [7/8] Menyiapkan bootstrap cache...
mkdir "%DEPLOY_DIR%\core\bootstrap\cache" 2>nul
echo. > "%DEPLOY_DIR%\core\bootstrap\cache\.gitkeep"

REM Copy dokumentasi
echo [8/8] Menyalin dokumentasi...
copy "%SOURCE_DIR%DEPLOYMENT.md" "%DEPLOY_DIR%\"
copy "%SOURCE_DIR%PANDUAN-UPLOAD-HOSTING.md" "%DEPLOY_DIR%\"
copy "%SOURCE_DIR%PANDUAN-DEPLOYMENT-LENGKAP.md" "%DEPLOY_DIR%\"
copy "%SOURCE_DIR%CHECKLIST-VALIDASI.md" "%DEPLOY_DIR%\"

echo.
echo =====================================================
echo    DEPLOYMENT PACKAGE SIAP!
echo =====================================================
echo.
echo Folder siap upload: %DEPLOY_DIR%
echo.
echo LANGKAH SELANJUTNYA:
echo 1. Buka folder tokoemas-deploy
echo 2. Rename .env.example menjadi .env di setiap site
echo 3. Edit .env dan isi credentials database
echo 4. Upload seluruh folder ke hosting via FTP
echo 5. Ikuti PANDUAN-UPLOAD-HOSTING.md
echo.
pause

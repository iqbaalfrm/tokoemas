<?php
// DEBUG FILE - HAPUS SETELAH SELESAI TESTING

echo "<h1>🔍 Laravel Hosting Debug</h1>";

// 1. PHP Version
echo "<h2>1. PHP Version</h2>";
echo "<p>Version: <strong>" . phpversion() . "</strong></p>";
if (version_compare(PHP_VERSION, '8.2.0', '>=')) {
    echo "<p style='color:green'>✅ PHP Version OK (Laravel 11 butuh 8.2+)</p>";
} else {
    echo "<p style='color:red'>❌ PHP TERLALU LAMA! Laravel 11 butuh PHP 8.2 atau lebih baru.</p>";
    echo "<p>👉 Solusi: Buka cPanel -> Select PHP Version -> Pilih PHP 8.2 atau 8.3</p>";
}

// 2. Required Extensions
echo "<h2>2. PHP Extensions</h2>";
$required = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'tokenizer', 'xml', 'ctype', 'json', 'bcmath', 'fileinfo'];
foreach ($required as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color:green'>✅ $ext</p>";
    } else {
        echo "<p style='color:red'>❌ $ext - TIDAK ADA!</p>";
    }
}

// 3. Storage Writable
echo "<h2>3. Storage Folder</h2>";
$storagePath = __DIR__ . '/../storage';
$logsPath = __DIR__ . '/../storage/logs';
$cachePath = __DIR__ . '/../storage/framework/cache';

if (is_dir($storagePath) && is_writable($storagePath)) {
    echo "<p style='color:green'>✅ storage/ writable</p>";
} else {
    echo "<p style='color:red'>❌ storage/ TIDAK WRITABLE atau tidak ada</p>";
    echo "<p>👉 Solusi: Set permission folder storage ke 755 atau 775</p>";
}

if (is_dir($logsPath) && is_writable($logsPath)) {
    echo "<p style='color:green'>✅ storage/logs/ writable</p>";
} else {
    echo "<p style='color:red'>❌ storage/logs/ TIDAK WRITABLE</p>";
}

// 4. .env file
echo "<h2>4. File .env</h2>";
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    echo "<p style='color:green'>✅ .env file ditemukan</p>";
    
    // Cek APP_KEY
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'APP_KEY=base64:') !== false) {
        echo "<p style='color:green'>✅ APP_KEY terisi</p>";
    } else {
        echo "<p style='color:red'>❌ APP_KEY kosong atau tidak valid!</p>";
    }
    
    // Cek APP_DEBUG
    if (strpos($envContent, 'APP_DEBUG=true') !== false) {
        echo "<p style='color:orange'>⚠️ APP_DEBUG=true (Aktifkan sementara untuk lihat error detail)</p>";
    } else {
        echo "<p>APP_DEBUG=false (Coba ubah ke true sementara untuk lihat error)</p>";
    }
} else {
    echo "<p style='color:red'>❌ .env file TIDAK ADA!</p>";
}

// 5. Vendor folder
echo "<h2>5. Vendor Folder</h2>";
$vendorPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorPath)) {
    echo "<p style='color:green'>✅ vendor/autoload.php ditemukan</p>";
} else {
    echo "<p style='color:red'>❌ vendor/ folder TIDAK ADA atau tidak lengkap!</p>";
    echo "<p>👉 Pastikan folder vendor ikut diupload (ukurannya besar ~50MB+)</p>";
}

echo "<hr><p><em>Hapus file debug.php ini setelah selesai testing!</em></p>";

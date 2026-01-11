<?php

/**
 * Multi-Tenant Entry Point - Toko Emas
 * =====================================
 * 
 * File ini adalah entry point untuk setiap site/subdomain.
 * Bootstrap Laravel dari folder core/ dan menggunakan storage/env dari site ini.
 * 
 * STRUKTUR DEPLOYMENT:
 * tokoemas-deploy/
 * ├── core/           <- Laravel core (shared)
 * └── sites/
 *     └── wates/
 *         ├── public/ <- folder ini (document root)
 *         ├── storage/
 *         └── .env
 */

// Path ke folder site ini (parent dari public/)
define('SITE_PATH', dirname(__DIR__));

// Path ke core Laravel (naik 3 level dari public, lalu ke core/)
// sites/wates/public -> sites/wates -> sites -> tokoemas-deploy -> tokoemas-deploy/core
define('CORE_PATH', dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'core');

// Set environment file path ke folder site
$_ENV['APP_ENV_PATH'] = SITE_PATH;
$_SERVER['APP_ENV_PATH'] = SITE_PATH;

// Set storage path ke folder site
$_ENV['APP_STORAGE_PATH'] = SITE_PATH . DIRECTORY_SEPARATOR . 'storage';
$_SERVER['APP_STORAGE_PATH'] = SITE_PATH . DIRECTORY_SEPARATOR . 'storage';

// Set public path
$_ENV['APP_PUBLIC_PATH'] = __DIR__;
$_SERVER['APP_PUBLIC_PATH'] = __DIR__;

use Illuminate\Http\Request;

// Cek apakah core path valid
if (!file_exists(CORE_PATH . '/vendor/autoload.php')) {
    die('ERROR: Core Laravel tidak ditemukan di: ' . CORE_PATH . '. Pastikan folder core/ sudah ada.');
}

// Register Autoloader dari core
require CORE_PATH . '/vendor/autoload.php';

// Load environment dari folder site
// Load environment dari folder site (default .env)
$dotenv = Dotenv\Dotenv::createImmutable(SITE_PATH);
$dotenv->safeLoad();

// Dynamic APP_URL from Request Host (Support Multi-Domain Mirroring)
if (isset($_SERVER['HTTP_HOST'])) {
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $dynamicUrl = $protocol . $_SERVER['HTTP_HOST'];
    putenv("APP_URL=$dynamicUrl");
    $_ENV['APP_URL'] = $dynamicUrl;
    $_SERVER['APP_URL'] = $dynamicUrl;
}

// --- MAGIC ENV LOADER (MULTI-DOMAIN SUPPORT) ---
// Deteksi domain untuk memilih file .env yang tepat
$host = $_SERVER['HTTP_HOST'] ?? '';
$envFile = '.env.harto'; // Default

if (strpos($host, 'tokoemasibnu.my.id') !== false) {
    $envFile = '.env.ibnu';
}

// Load .env Spesifik
if (file_exists(SITE_PATH . '/' . $envFile)) {
    $dotenv = Dotenv\Dotenv::createImmutable(SITE_PATH, $envFile);
    $dotenv->safeLoad();
}

// Bootstrap Laravel Application dari core
$app = require_once CORE_PATH . '/bootstrap/app.php';

// Override storage path ke folder site (Pakai realpath agar aman dari symlink hosting)
$storagePath = realpath(SITE_PATH . DIRECTORY_SEPARATOR . 'storage');
if ($storagePath) {
    $app->useStoragePath($storagePath);
} else {
    // Fallback jika realpath gagal (folder belum dibuat)
    $app->useStoragePath(SITE_PATH . DIRECTORY_SEPARATOR . 'storage');
}

// Handle Request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);

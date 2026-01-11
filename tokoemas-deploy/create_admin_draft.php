<?php
// Script Pembuat Super Admin Darurat
// Upload ke /public_html/tokoemas/sites/wates/public/create_admin.php
// Akses: https://wates.hartowiyono.my.id/create_admin.php?key=RAHASIA

$secretKey = 'RAHASIA';
if (($_GET['key'] ?? '') !== $secretKey) die('Access Denied');

// Boot Laravel
define('LARAVEL_START', microtime(true));
require __DIR__ . '/index.php'; // Load Laravel enviroment via index.php logic (without handling request)

// TAPI index.php di atas langsung handle request dan terminate. 
// Kita harus bypass. Cara di atas salah untuk script standalone.
// Kita pakai cara manual boot seperti runner.

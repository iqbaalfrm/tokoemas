<?php
// Script Pembuat Super Admin Darurat (Versi Standalone)
// Upload ke folder public site Anda (misal: sites/wates/public/create_admin.php)

$secretKey = 'RAHASIA';
if (($_GET['key'] ?? '') !== $secretKey) die('Access Denied');

// Deteksi Root Path (Sama seperti runner.php)
$rootPath = dirname(__DIR__, 3);
$corePath = $rootPath . '/core';
$sitePath = dirname(__DIR__); // Folder site (misal sites/wates)

require $corePath . '/vendor/autoload.php';

// Load Dotenv
if (file_exists($sitePath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($sitePath);
    $dotenv->safeLoad();
}

$app = require_once $corePath . '/bootstrap/app.php';
$app->useStoragePath($sitePath . '/storage');
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "<h1>Create Super Admin</h1>";

try {
    // Cek apakah user sudah ada
    $email = 'admin@admin.com';
    $user = User::where('email', $email)->first();

    if ($user) {
        $user->password = Hash::make('password');
        $user->save();
        echo "<p style='color:green'>User $email sudah ada. Password di-reset ke: <b>password</b></p>";
    } else {
        $user = new User();
        $user->name = 'Super Admin';
        $user->email = $email;
        $user->password = Hash::make('password');
        $user->email_verified_at = now();
        $user->save();
        echo "<p style='color:green'>User $email BERHASIL dibuat! Password: <b>password</b></p>";
    }

    // Assign Role Super Admin (Filament Shield)
    // Cek apakah role super_admin ada?
    try {
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $roleName = config('filament-shield.super_admin.name', 'super_admin');
            
            // Create role if not exists
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            
            $user->assignRole($roleName);
            echo "<p>Role <b>$roleName</b> berhasil diberikan!</p>";
        }
    } catch (\Throwable $e) {
        echo "<p style='color:orange'>Warning Role: " . $e->getMessage() . "</p>";
    }

} catch (\Throwable $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>

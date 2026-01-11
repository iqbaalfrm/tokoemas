<?php
/**
 * Shield Setup Script
 * Jalankan via browser untuk generate permissions Filament Shield
 * HAPUS SETELAH SELESAI!
 */

// Security
$password = 'shield2026';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $password) {
        die('❌ Password salah!');
    }
    
    // Bootstrap Laravel
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h1>🛡️ Shield Setup</h1>";
    echo "<pre>";
    
    try {
        // 1. Generate all permissions
        echo "🔄 Generating Shield permissions...\n";
        Artisan::call('shield:generate', [
            '--all' => true, 
            '--panel' => 'admin',  // Specify panel
        ]);
        echo Artisan::output();
        echo "✅ Permissions generated!\n\n";
        
        // 2. Create/Update super admin
        echo "🔄 Setting up Super Admin...\n";
        Artisan::call('shield:super-admin', ['--user' => 1]); // User ID 1
        echo Artisan::output();
        echo "✅ Super Admin configured!\n\n";
        
        // 3. Clear cache
        echo "🔄 Clearing cache...\n";
        Artisan::call('optimize:clear');
        echo Artisan::output();
        echo "✅ Cache cleared!\n\n";
        
        echo "</pre>";
        echo "<h2>🎉 Shield setup selesai!</h2>";
        echo "<p>Sekarang coba login dengan <strong>superadmin@gmail.com</strong></p>";
        echo "<p style='color:red'>⚠️ HAPUS file shield-setup.php ini setelah selesai!</p>";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString();
        echo "</pre>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shield Setup</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background: #10b981; color: white; border: none; cursor: pointer; }
        button:hover { background: #059669; }
    </style>
</head>
<body>
    <h1>🛡️ Filament Shield Setup</h1>
    <p>Generate permissions dan setup super admin.</p>
    <form method="POST">
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">🚀 Jalankan Shield Setup</button>
    </form>
</body>
</html>

<?php
/**
 * Reset Password Script
 * HAPUS SETELAH SELESAI!
 */

$password = 'reset2026';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] !== $password) {
        die('❌ Password salah!');
    }
    
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    echo "<h1>🔐 Reset Password</h1>";
    echo "<pre>";
    
    try {
        // Reset password untuk semua user ke "password"
        $newPassword = bcrypt('password');
        
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $user->password = $newPassword;
            $user->save();
            echo "✅ Reset password: {$user->email}\n";
        }
        
        echo "\n🎉 Semua password sudah direset ke: password\n";
        echo "</pre>";
        
        echo "<h2>Akun Login:</h2>";
        echo "<ul>";
        foreach ($users as $user) {
            echo "<li><strong>{$user->email}</strong> / password</li>";
        }
        echo "</ul>";
        echo "<p style='color:red'>⚠️ HAPUS file reset-password.php ini setelah selesai!</p>";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "</pre>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body { font-family: Arial; max-width: 500px; margin: 50px auto; padding: 20px; }
        input, button { padding: 10px; margin: 5px 0; width: 100%; box-sizing: border-box; }
        button { background: #ef4444; color: white; border: none; cursor: pointer; }
        button:hover { background: #dc2626; }
    </style>
</head>
<body>
    <h1>🔐 Reset Password User</h1>
    <p>Reset semua password user ke "password".</p>
    <form method="POST">
        <input type="password" name="password" placeholder="Password Script" required>
        <button type="submit">🔄 Reset Password</button>
    </form>
</body>
</html>

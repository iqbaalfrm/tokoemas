<?php
/**
 * =============================================================
 * LARAVEL ARTISAN WEB RUNNER
 * =============================================================
 * 
 * Script untuk menjalankan perintah Laravel Artisan melalui browser
 * tanpa akses terminal/SSH.
 * 
 * CARA PENGGUNAAN:
 * 1. Upload file ini ke folder public/ di hosting
 * 2. Akses via browser: https://domain-anda.com/artisan.php
 * 3. Pilih perintah yang ingin dijalankan
 * 4. HAPUS FILE INI SETELAH SELESAI
 * 
 * =============================================================
 */

// ========== KONFIGURASI ==========
$SECURITY_PASSWORD = 'artisan2026';

// ========== PROTEKSI AKSES ==========
session_start();
$is_authenticated = isset($_SESSION['artisan_auth']) && $_SESSION['artisan_auth'] === true;

if (isset($_POST['password'])) {
    if ($_POST['password'] === $SECURITY_PASSWORD) {
        $_SESSION['artisan_auth'] = true;
        $is_authenticated = true;
    } else {
        $error_message = 'Kata sandi salah!';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: artisan.php');
    exit;
}

// Helper function untuk menjalankan artisan command
function runArtisan($command) {
    // Cari path ke artisan
    $basePath = dirname(__DIR__);
    $artisanPath = $basePath . '/artisan';
    
    if (!file_exists($artisanPath)) {
        return ['success' => false, 'output' => 'File artisan tidak ditemukan di: ' . $artisanPath];
    }
    
    // Tentukan PHP binary
    $phpBinary = PHP_BINARY ?: 'php';
    
    // Jalankan command
    $fullCommand = escapeshellcmd($phpBinary) . ' ' . escapeshellarg($artisanPath) . ' ' . $command . ' 2>&1';
    
    $output = [];
    $returnCode = 0;
    exec($fullCommand, $output, $returnCode);
    
    return [
        'success' => $returnCode === 0,
        'output' => implode("\n", $output),
        'command' => $command,
        'returnCode' => $returnCode
    ];
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Artisan Web Runner</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }
        .container { max-width: 1000px; margin: 0 auto; }
        .card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h2 { font-size: 18px; margin-bottom: 15px; color: #f093fb; }
        .subtitle { color: rgba(255,255,255,0.7); margin-bottom: 20px; }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin: 5px;
        }
        .btn-primary {
            background: linear-gradient(90deg, #f093fb, #f5576c);
            color: white;
        }
        .btn-success {
            background: linear-gradient(90deg, #11998e, #38ef7d);
            color: white;
        }
        .btn-warning {
            background: linear-gradient(90deg, #f7971e, #ffd200);
            color: #333;
        }
        .btn-danger {
            background: linear-gradient(90deg, #eb3349, #f45c43);
            color: white;
        }
        .btn-info {
            background: linear-gradient(90deg, #4facfe, #00f2fe);
            color: white;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .btn-small { padding: 8px 16px; font-size: 13px; }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
        }
        input:focus { outline: none; border-color: #f093fb; }
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: rgba(17,153,142,0.3); border: 1px solid #38ef7d; color: #38ef7d; }
        .alert-error { background: rgba(235,51,73,0.3); border: 1px solid #eb3349; color: #f45c43; }
        .alert-warning { background: rgba(247,151,30,0.3); border: 1px solid #f7971e; color: #ffd200; }
        .alert-info { background: rgba(79,172,254,0.3); border: 1px solid #4facfe; color: #00f2fe; }
        .log-box {
            background: #0d1117;
            border-radius: 8px;
            padding: 20px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
            max-height: 500px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .command-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }
        .command-card {
            background: rgba(0,0,0,0.3);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }
        .command-card h3 { font-size: 16px; margin-bottom: 8px; }
        .command-card p { color: rgba(255,255,255,0.6); font-size: 12px; margin-bottom: 15px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
        .custom-command { display: flex; gap: 10px; align-items: center; }
        .custom-command input { flex: 1; margin-bottom: 0; }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_authenticated): ?>
        <!-- LOGIN -->
        <div class="card" style="max-width: 400px; margin: 100px auto;">
            <h1 style="text-align: center; margin-bottom: 20px;">⚡</h1>
            <h2 style="text-align: center;">Artisan Web Runner</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?= $error_message ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Masukkan kata sandi..." required autofocus>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk</button>
            </form>
        </div>
        
        <?php else: ?>
        <!-- HEADER -->
        <div class="card">
            <div class="header-actions">
                <div>
                    <h1>⚡ Laravel Artisan Web Runner</h1>
                    <p class="subtitle">Jalankan perintah Artisan tanpa terminal</p>
                </div>
                <a href="?logout=1" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </div>
        
        <?php
        // Proses command
        $result = null;
        if (isset($_POST['command'])) {
            $command = trim($_POST['command']);
            if (!empty($command)) {
                $result = runArtisan($command);
            }
        }
        
        // Quick commands
        $quickCommands = [
            'migrate' => ['run' => 'migrate --force', 'title' => 'Migrate', 'desc' => 'Jalankan semua migrasi database', 'btn' => 'btn-success'],
            'migrate:fresh' => ['run' => 'migrate:fresh --force', 'title' => 'Migrate Fresh', 'desc' => 'Hapus semua tabel & jalankan ulang migrasi', 'btn' => 'btn-warning'],
            'migrate:fresh:seed' => ['run' => 'migrate:fresh --seed --force', 'title' => 'Migrate Fresh + Seed', 'desc' => 'Fresh migrate + jalankan seeder', 'btn' => 'btn-danger'],
            'db:seed' => ['run' => 'db:seed --force', 'title' => 'Database Seed', 'desc' => 'Jalankan database seeder', 'btn' => 'btn-info'],
            'cache:clear' => ['run' => 'cache:clear', 'title' => 'Clear Cache', 'desc' => 'Hapus application cache', 'btn' => 'btn-primary'],
            'config:clear' => ['run' => 'config:clear', 'title' => 'Clear Config', 'desc' => 'Hapus configuration cache', 'btn' => 'btn-primary'],
            'route:clear' => ['run' => 'route:clear', 'title' => 'Clear Route', 'desc' => 'Hapus route cache', 'btn' => 'btn-primary'],
            'view:clear' => ['run' => 'view:clear', 'title' => 'Clear Views', 'desc' => 'Hapus compiled view files', 'btn' => 'btn-primary'],
            'optimize:clear' => ['run' => 'optimize:clear', 'title' => 'Optimize Clear', 'desc' => 'Hapus semua cache (all in one)', 'btn' => 'btn-warning'],
            'storage:link' => ['run' => 'storage:link', 'title' => 'Storage Link', 'desc' => 'Buat symbolic link storage', 'btn' => 'btn-success'],
            'config:cache' => ['run' => 'config:cache', 'title' => 'Cache Config', 'desc' => 'Cache configuration untuk produksi', 'btn' => 'btn-info'],
            'route:cache' => ['run' => 'route:cache', 'title' => 'Cache Route', 'desc' => 'Cache routes untuk produksi', 'btn' => 'btn-info'],
        ];
        
        if (isset($_POST['quick_command']) && isset($quickCommands[$_POST['quick_command']])) {
            $cmd = $quickCommands[$_POST['quick_command']]['run'];
            $result = runArtisan($cmd);
        }
        ?>
        
        <?php if ($result): ?>
        <!-- HASIL COMMAND -->
        <div class="card">
            <h2><?= $result['success'] ? '✅' : '❌' ?> Hasil: <?= htmlspecialchars($result['command']) ?></h2>
            <div class="log-box"><?= htmlspecialchars($result['output'] ?: '(tidak ada output)') ?></div>
            <?php if (!$result['success']): ?>
                <div class="alert alert-warning" style="margin-top: 15px;">
                    ⚠️ Return code: <?= $result['returnCode'] ?>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- MIGRATION COMMANDS -->
        <div class="card">
            <h2>🗄️ Database & Migration</h2>
            <div class="command-grid">
                <?php foreach (['migrate', 'migrate:fresh', 'migrate:fresh:seed', 'db:seed'] as $key): ?>
                    <?php $cmd = $quickCommands[$key]; ?>
                    <div class="command-card">
                        <h3><?= $cmd['title'] ?></h3>
                        <p><?= $cmd['desc'] ?></p>
                        <form method="POST" onsubmit="return confirm('Yakin ingin menjalankan: <?= $cmd['run'] ?>?');">
                            <input type="hidden" name="quick_command" value="<?= $key ?>">
                            <button type="submit" class="btn <?= $cmd['btn'] ?> btn-small"><?= $cmd['title'] ?></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- CACHE COMMANDS -->
        <div class="card">
            <h2>🧹 Cache Management</h2>
            <div class="command-grid">
                <?php foreach (['cache:clear', 'config:clear', 'route:clear', 'view:clear', 'optimize:clear'] as $key): ?>
                    <?php $cmd = $quickCommands[$key]; ?>
                    <div class="command-card">
                        <h3><?= $cmd['title'] ?></h3>
                        <p><?= $cmd['desc'] ?></p>
                        <form method="POST">
                            <input type="hidden" name="quick_command" value="<?= $key ?>">
                            <button type="submit" class="btn <?= $cmd['btn'] ?> btn-small"><?= $cmd['title'] ?></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- PRODUCTION COMMANDS -->
        <div class="card">
            <h2>🚀 Production & Storage</h2>
            <div class="command-grid">
                <?php foreach (['storage:link', 'config:cache', 'route:cache'] as $key): ?>
                    <?php $cmd = $quickCommands[$key]; ?>
                    <div class="command-card">
                        <h3><?= $cmd['title'] ?></h3>
                        <p><?= $cmd['desc'] ?></p>
                        <form method="POST">
                            <input type="hidden" name="quick_command" value="<?= $key ?>">
                            <button type="submit" class="btn <?= $cmd['btn'] ?> btn-small"><?= $cmd['title'] ?></button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- CUSTOM COMMAND -->
        <div class="card">
            <h2>⌨️ Custom Artisan Command</h2>
            <form method="POST" class="custom-command">
                <input type="text" name="command" placeholder="Contoh: migrate:status" required>
                <button type="submit" class="btn btn-primary">Jalankan</button>
            </form>
            <p style="margin-top: 15px; color: rgba(255,255,255,0.5); font-size: 12px;">
                💡 Tips: Ketik perintah tanpa "php artisan". Contoh: <code>migrate:status</code>, <code>tinker</code>, <code>route:list</code>
            </p>
        </div>
        
        <!-- WARNING -->
        <div class="card">
            <div class="alert alert-warning">
                ⚠️ <strong>PERINGATAN KEAMANAN:</strong> Hapus file <code>artisan.php</code> ini setelah selesai digunakan!
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>

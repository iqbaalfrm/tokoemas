<?php
// Script Runner untuk Shared Hosting (cPanel) tanpa Terminal
// Upload file ini ke folder utama (sejajar dengan core/ dan sites/)
// Akses via browser: https://domainanda.com/runner.php?key=RAHASIA

$secretKey = 'RAHASIA'; // Ganti dengan key yang aman
if (($_GET['key'] ?? '') !== $secretKey) {
    die('Akses Ditolak (Access Denied)');
}

// FIX: Deteksi Path 3 level ke atas (karena file ada di sites/wates/public/runner.php)
$rootPath = dirname(__DIR__, 3);
$corePath = $rootPath . '/core';
$sitesPath = $rootPath . '/sites';

// Handle Form Submission
$output = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $command = $_POST['command'] ?? '';
    $site = $_POST['site'] ?? 'wates';
    
    // Path definition
    $sitePath = $sitesPath . '/' . $site;
    
    if (!is_dir($sitePath)) {
        $output = "Direktori site tidak ditemukan: $sitePath";
    } else {
        // Prepare Environment
        putenv("APP_ENV_PATH=$sitePath");
        
        try {
            require_once $corePath . '/vendor/autoload.php';
            
            // Load Dotenv for Site
            if (file_exists($sitePath . '/.env')) {
                $dotenv = Dotenv\Dotenv::createImmutable($sitePath);
                $dotenv->safeLoad();
            }
            
            $app = require_once $corePath . '/bootstrap/app.php';
            $app->useStoragePath($sitePath . '/storage');
            
            $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

            // Capture output
            $stream = fopen('php://temp', 'w+');
            
            // Parse command string to array
            $params = explode(' ', $command);
            $cmd = array_shift($params);
            
            // Execution
            $status = $kernel->call($command);
            $output = $kernel->output();
            
        } catch (\Throwable $e) {
            $output = "Terjadi Kesalahan: " . $e->getMessage() . "\n" . $e->getTraceAsString();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Runner Perintah Laravel</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f0f0f0; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        textarea { width: 100%; height: 300px; background: #222; color: #0f0; padding: 10px; border: none; }
        input, select, button { padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>Eksekusi Perintah Laravel</h2>
    <form method="POST">
        <label>Pilih Tenant (Site):</label>
        <select name="site">
            <?php 
            foreach (glob($sitesPath . '/*', GLOB_ONLYDIR) as $dir) {
                $name = basename($dir);
                echo "<option value='$name'>$name</option>";
            }
            ?>
        </select>
        <br>
        <label>Perintah (contoh: migrate --force):</label>
        <input type="text" name="command" style="width: 70%" placeholder="migrate">
        <button type="submit">JALANKAN</button>
    </form>
    <hr>
    <h3>Output Terminal:</h3>
    <textarea readonly><?php echo htmlspecialchars($output); ?></textarea>
</div>
</body>
</html>

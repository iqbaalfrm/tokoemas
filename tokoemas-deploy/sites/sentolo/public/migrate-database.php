<?php
/**
 * =============================================================
 * SCRIPT MIGRASI DATABASE KASIR
 * =============================================================
 * 
 * CARA PENGGUNAAN:
 * 1. Upload file ini ke folder public/ di hosting Anda
 * 2. Akses via browser: https://domain-anda.com/migrate-database.php
 * 3. Ikuti instruksi yang muncul di layar
 * 4. HAPUS FILE INI SETELAH SELESAI MIGRASI
 * 
 * KEAMANAN:
 * - File ini dilindungi dengan kata sandi sederhana
 * - WAJIB dihapus setelah proses migrasi selesai
 * 
 * =============================================================
 */

// ========== KONFIGURASI ==========
// Ganti kata sandi ini sesuai keinginan Anda
$SECURITY_PASSWORD = 'migrate2026';

// ========== PROTEKSI AKSES ==========
session_start();

// Cek autentikasi
$is_authenticated = isset($_SESSION['migrate_auth']) && $_SESSION['migrate_auth'] === true;

// Proses login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $SECURITY_PASSWORD) {
        $_SESSION['migrate_auth'] = true;
        $is_authenticated = true;
    } else {
        $error_message = 'Kata sandi salah!';
    }
}

// Proses logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: migrate-database.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Migrasi Database - Kasir</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        h1 {
            font-size: 28px;
            margin-bottom: 10px;
            background: linear-gradient(90deg, #00d9ff, #00ff88);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        h2 {
            font-size: 20px;
            margin-bottom: 15px;
            color: #00d9ff;
        }
        .subtitle {
            color: rgba(255,255,255,0.7);
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(90deg, #00d9ff, #00ff88);
            color: #1a1a2e;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,217,255,0.3);
        }
        .btn-danger {
            background: linear-gradient(90deg, #ff4757, #ff6b81);
            color: white;
        }
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255,71,87,0.3);
        }
        .btn-small {
            padding: 8px 16px;
            font-size: 14px;
        }
        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 16px;
            margin-bottom: 15px;
            transition: border-color 0.3s ease;
        }
        input[type="password"]:focus, input[type="text"]:focus {
            outline: none;
            border-color: #00d9ff;
        }
        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: rgba(0,255,136,0.2);
            border: 1px solid #00ff88;
            color: #00ff88;
        }
        .alert-error {
            background: rgba(255,71,87,0.2);
            border: 1px solid #ff4757;
            color: #ff6b81;
        }
        .alert-warning {
            background: rgba(255,193,7,0.2);
            border: 1px solid #ffc107;
            color: #ffc107;
        }
        .alert-info {
            background: rgba(0,217,255,0.2);
            border: 1px solid #00d9ff;
            color: #00d9ff;
        }
        .log-box {
            background: #0d1117;
            border-radius: 8px;
            padding: 20px;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 13px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid rgba(255,255,255,0.1);
        }
        .log-success { color: #00ff88; }
        .log-error { color: #ff4757; }
        .log-info { color: #00d9ff; }
        .log-warning { color: #ffc107; }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
        }
        .step-number {
            width: 36px;
            height: 36px;
            background: linear-gradient(90deg, #00d9ff, #00ff88);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #1a1a2e;
            flex-shrink: 0;
        }
        .step-content h3 {
            font-size: 16px;
            margin-bottom: 5px;
        }
        .step-content p {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
        .db-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .db-info-item {
            background: rgba(0,0,0,0.3);
            padding: 15px;
            border-radius: 8px;
        }
        .db-info-label {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .db-info-value {
            font-size: 16px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!$is_authenticated): ?>
        <!-- ===== FORM LOGIN ===== -->
        <div class="card" style="max-width: 400px; margin: 100px auto;">
            <h1 style="text-align: center; margin-bottom: 20px;">🔐</h1>
            <h2 style="text-align: center;">Akses Migrasi Database</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?= $error_message ?></div>
            <?php endif; ?>
            <form method="POST">
                <input type="password" name="password" placeholder="Masukkan kata sandi..." required autofocus>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Masuk</button>
            </form>
        </div>
        
        <?php else: ?>
        <!-- ===== HALAMAN UTAMA ===== -->
        <div class="card">
            <div class="header-actions">
                <div>
                    <h1>🗄️ Migrasi Database Kasir</h1>
                    <p class="subtitle">Import struktur dan data database ke hosting Anda</p>
                </div>
                <a href="?logout=1" class="btn btn-danger btn-small">Keluar</a>
            </div>
        </div>
        
        <?php
        // ===== PROSES MIGRASI =====
        if (isset($_POST['run_migration'])) {
            echo '<div class="card"><h2>📋 Log Proses Migrasi</h2><div class="log-box">';
            
            // Ambil konfigurasi database dari Laravel
            $configPath = __DIR__ . '/../.env';
            if (!file_exists($configPath)) {
                echo '<p class="log-error">❌ File .env tidak ditemukan!</p>';
                echo '</div></div>';
            } else {
                // Parse .env file
                $env = [];
                $lines = file($configPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                        list($key, $value) = explode('=', $line, 2);
                        $env[trim($key)] = trim($value, '"\'');
                    }
                }
                
                $host = $env['DB_HOST'] ?? 'localhost';
                $port = $env['DB_PORT'] ?? '3306';
                $database = $env['DB_DATABASE'] ?? 'kasir';
                $username = $env['DB_USERNAME'] ?? 'root';
                $password = $env['DB_PASSWORD'] ?? '';
                
                echo '<p class="log-info">ℹ️ Host: ' . $host . ':' . $port . '</p>';
                echo '<p class="log-info">ℹ️ Database: ' . $database . '</p>';
                echo '<p class="log-info">ℹ️ Username: ' . $username . '</p>';
                echo '<br>';
                
                try {
                    // Koneksi ke MySQL tanpa memilih database
                    echo '<p class="log-info">🔄 Menghubungkan ke server MySQL...</p>';
                    $pdo = new PDO(
                        "mysql:host={$host};port={$port};charset=utf8mb4",
                        $username,
                        $password,
                        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                    );
                    echo '<p class="log-success">✅ Berhasil terhubung ke server MySQL!</p>';
                    
                    // Buat database jika belum ada
                    echo '<p class="log-info">🔄 Memeriksa database...</p>';
                    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    echo '<p class="log-success">✅ Database <strong>' . $database . '</strong> siap digunakan!</p>';
                    
                    // Pilih database
                    $pdo->exec("USE `{$database}`");
                    echo '<p class="log-info">🔄 Menggunakan database: ' . $database . '</p>';
                    echo '<br>';
                    
                    // Cari file SQL
                    $sqlFile = __DIR__ . '/kasir_schema.sql';
                    if (!file_exists($sqlFile)) {
                        $sqlFile = __DIR__ . '/../kasir_schema.sql';
                    }
                    if (!file_exists($sqlFile)) {
                        // Cari di root project
                        $sqlFile = dirname(__DIR__, 3) . '/kasir_schema.sql';
                    }
                    
                    if (!file_exists($sqlFile)) {
                        echo '<p class="log-error">❌ File kasir_schema.sql tidak ditemukan!</p>';
                        echo '<p class="log-warning">⚠️ Silakan upload file kasir_schema.sql ke salah satu lokasi berikut:</p>';
                        echo '<p class="log-info">   - public/kasir_schema.sql</p>';
                        echo '<p class="log-info">   - kasir_schema.sql (root folder)</p>';
                    } else {
                        echo '<p class="log-success">✅ File SQL ditemukan: ' . basename($sqlFile) . '</p>';
                        echo '<p class="log-info">🔄 Memproses file SQL...</p>';
                        
                        // Baca file SQL
                        $sql = file_get_contents($sqlFile);
                        
                        // Hilangkan komentar single line
                        $sql = preg_replace('/^--.*$/m', '', $sql);
                        
                        // Hilangkan komentar multi line
                        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
                        
                        // Nonaktifkan foreign key checks
                        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                        
                        // Split berdasarkan delimiter
                        $statements = [];
                        $currentStatement = '';
                        $lines = explode("\n", $sql);
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;
                            
                            $currentStatement .= ' ' . $line;
                            
                            // Cek apakah statement berakhir dengan semicolon
                            if (substr($line, -1) === ';') {
                                $statements[] = trim($currentStatement);
                                $currentStatement = '';
                            }
                        }
                        
                        $successCount = 0;
                        $errorCount = 0;
                        $skipCount = 0;
                        
                        foreach ($statements as $statement) {
                            $statement = trim($statement);
                            if (empty($statement)) continue;
                            
                            // Skip beberapa statement yang tidak perlu
                            if (preg_match('/^(SET|START|COMMIT|\/\*!)/i', $statement)) {
                                $skipCount++;
                                continue;
                            }
                            
                            try {
                                $pdo->exec($statement);
                                $successCount++;
                                
                                // Log beberapa operasi penting
                                if (preg_match('/CREATE TABLE[^`]*`([^`]+)`/i', $statement, $matches)) {
                                    echo '<p class="log-success">   ✓ Tabel <strong>' . $matches[1] . '</strong> dibuat</p>';
                                } elseif (preg_match('/INSERT INTO[^`]*`([^`]+)`/i', $statement, $matches)) {
                                    // Skip log untuk insert, terlalu banyak
                                }
                            } catch (PDOException $e) {
                                // Abaikan error duplicate key/table exists
                                if (strpos($e->getMessage(), 'already exists') !== false || 
                                    strpos($e->getMessage(), 'Duplicate') !== false) {
                                    $skipCount++;
                                } else {
                                    $errorCount++;
                                    if ($errorCount <= 5) {
                                        echo '<p class="log-error">   ✗ Error: ' . htmlspecialchars(substr($e->getMessage(), 0, 100)) . '</p>';
                                    }
                                }
                            }
                        }
                        
                        // Aktifkan kembali foreign key checks
                        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                        
                        echo '<br>';
                        echo '<p class="log-success">✅ Proses struktur selesai!</p>';
                        echo '<p class="log-info">   - Statement berhasil: ' . $successCount . '</p>';
                        echo '<p class="log-info">   - Statement dilewati: ' . $skipCount . '</p>';
                        if ($errorCount > 0) {
                            echo '<p class="log-warning">   - Statement error: ' . $errorCount . '</p>';
                        }
                        
                        // ===== IMPORT DATA AWAL =====
                        $dataFile = __DIR__ . '/kasir_data.sql';
                        if (file_exists($dataFile)) {
                            echo '<br>';
                            echo '<p class="log-info">🔄 Mengimport data awal...</p>';
                            
                            $dataSql = file_get_contents($dataFile);
                            $dataSql = preg_replace('/^--.*$/m', '', $dataSql);
                            $dataSql = preg_replace('/\/\*.*?\*\//s', '', $dataSql);
                            
                            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
                            
                            $dataStatements = [];
                            $currentStmt = '';
                            $dataLines = explode("\n", $dataSql);
                            
                            foreach ($dataLines as $line) {
                                $line = trim($line);
                                if (empty($line)) continue;
                                $currentStmt .= ' ' . $line;
                                if (substr($line, -1) === ';') {
                                    $dataStatements[] = trim($currentStmt);
                                    $currentStmt = '';
                                }
                            }
                            
                            $dataSuccess = 0;
                            $dataError = 0;
                            
                            foreach ($dataStatements as $stmt) {
                                $stmt = trim($stmt);
                                if (empty($stmt)) continue;
                                if (preg_match('/^(SET|START|COMMIT)/i', $stmt)) continue;
                                
                                try {
                                    $pdo->exec($stmt);
                                    $dataSuccess++;
                                    
                                    if (preg_match('/INSERT INTO[^`]*`([^`]+)`/i', $stmt, $matches)) {
                                        echo '<p class="log-success">   ✓ Data ' . $matches[1] . ' ditambahkan</p>';
                                    }
                                } catch (PDOException $e) {
                                    if (strpos($e->getMessage(), 'Duplicate') === false) {
                                        $dataError++;
                                    }
                                }
                            }
                            
                            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
                            
                            echo '<p class="log-success">✅ Import data selesai! (' . $dataSuccess . ' berhasil)</p>';
                        } else {
                            echo '<p class="log-warning">⚠️ File kasir_data.sql tidak ditemukan, database kosong tanpa data awal.</p>';
                        }
                        
                        echo '<br>';
                        echo '<p class="log-success">🎉 Migrasi database selesai!</p>';
                        echo '<p class="log-warning">⚠️ PENTING: Hapus file migrate-database.php, kasir_schema.sql, dan kasir_data.sql setelah selesai!</p>';
                    }
                    
                } catch (PDOException $e) {
                    echo '<p class="log-error">❌ Gagal terhubung ke database!</p>';
                    echo '<p class="log-error">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<br>';
                    echo '<p class="log-warning">💡 Pastikan konfigurasi di file .env sudah benar:</p>';
                    echo '<p class="log-info">   DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD</p>';
                }
                
                echo '</div></div>';
            }
        }
        
        // ===== CHECK DATABASE STATUS =====
        $dbStatus = null;
        $tables = [];
        
        $configPath = __DIR__ . '/../.env';
        if (file_exists($configPath)) {
            $env = [];
            $lines = file($configPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                    list($key, $value) = explode('=', $line, 2);
                    $env[trim($key)] = trim($value, '"\'');
                }
            }
            
            $host = $env['DB_HOST'] ?? 'localhost';
            $port = $env['DB_PORT'] ?? '3306';
            $database = $env['DB_DATABASE'] ?? 'kasir';
            $username = $env['DB_USERNAME'] ?? 'root';
            $password = $env['DB_PASSWORD'] ?? '';
            
            try {
                $pdo = new PDO(
                    "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4",
                    $username,
                    $password,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                $dbStatus = 'connected';
                
                // Get table list
                $stmt = $pdo->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Unknown database') !== false) {
                    $dbStatus = 'no_database';
                } else {
                    $dbStatus = 'error';
                    $dbError = $e->getMessage();
                }
            }
        }
        ?>
        
        <!-- ===== STATUS DATABASE ===== -->
        <div class="card">
            <h2>📊 Status Database</h2>
            
            <?php if (!file_exists($configPath)): ?>
                <div class="alert alert-error">
                    ❌ File .env tidak ditemukan. Pastikan file .env sudah dikonfigurasi dengan benar.
                </div>
            <?php elseif ($dbStatus === 'connected'): ?>
                <div class="alert alert-success">
                    ✅ Terhubung ke database <strong><?= htmlspecialchars($database) ?></strong>
                </div>
                
                <div class="db-info">
                    <div class="db-info-item">
                        <div class="db-info-label">Host</div>
                        <div class="db-info-value"><?= htmlspecialchars($host) ?>:<?= htmlspecialchars($port) ?></div>
                    </div>
                    <div class="db-info-item">
                        <div class="db-info-label">Database</div>
                        <div class="db-info-value"><?= htmlspecialchars($database) ?></div>
                    </div>
                    <div class="db-info-item">
                        <div class="db-info-label">Jumlah Tabel</div>
                        <div class="db-info-value"><?= count($tables) ?></div>
                    </div>
                    <div class="db-info-item">
                        <div class="db-info-label">Status</div>
                        <div class="db-info-value" style="color: #00ff88;">Aktif</div>
                    </div>
                </div>
                
                <?php if (count($tables) > 0): ?>
                <details style="margin-top: 15px;">
                    <summary style="cursor: pointer; color: #00d9ff;">Lihat daftar tabel (<?= count($tables) ?>)</summary>
                    <div style="margin-top: 10px; padding: 15px; background: rgba(0,0,0,0.3); border-radius: 8px;">
                        <?php foreach ($tables as $table): ?>
                            <span style="display: inline-block; padding: 4px 10px; margin: 3px; background: rgba(0,217,255,0.2); border-radius: 4px; font-size: 13px;"><?= htmlspecialchars($table) ?></span>
                        <?php endforeach; ?>
                    </div>
                </details>
                <?php endif; ?>
                
            <?php elseif ($dbStatus === 'no_database'): ?>
                <div class="alert alert-warning">
                    ⚠️ Database <strong><?= htmlspecialchars($database) ?></strong> belum ada. Jalankan migrasi untuk membuatnya.
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    ❌ Tidak dapat terhubung ke database: <?= htmlspecialchars($dbError ?? 'Unknown error') ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- ===== PANDUAN ===== -->
        <div class="card">
            <h2>📖 Panduan Penggunaan</h2>
            
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Upload File SQL</h3>
                    <p>Pastikan file <code>kasir.sql</code> sudah ada di folder <code>public/</code> atau root folder.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Konfigurasi .env</h3>
                    <p>Pastikan file <code>.env</code> sudah dikonfigurasi dengan kredensial database hosting Anda.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Jalankan Migrasi</h3>
                    <p>Klik tombol di bawah untuk memulai proses migrasi database.</p>
                </div>
            </div>
            
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h3>Hapus File Ini</h3>
                    <p><strong>WAJIB:</strong> Hapus file <code>migrate-database.php</code> setelah selesai demi keamanan.</p>
                </div>
            </div>
        </div>
        
        <!-- ===== TOMBOL MIGRASI ===== -->
        <div class="card" style="text-align: center;">
            <form method="POST" onsubmit="return confirm('Yakin ingin menjalankan migrasi database? Proses ini akan membuat/mengupdate struktur database.');">
                <input type="hidden" name="run_migration" value="1">
                <button type="submit" class="btn btn-primary" style="font-size: 18px; padding: 18px 40px;">
                    🚀 Jalankan Migrasi Database
                </button>
            </form>
            <p style="margin-top: 15px; color: rgba(255,255,255,0.6); font-size: 14px;">
                Proses ini akan membuat database dan semua tabel yang diperlukan.
            </p>
        </div>
        
        <!-- ===== TOOLS MAINTENANCE ===== -->
        <div class="card">
            <h2>🛠️ Tools Maintenance</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px;">
                <!-- Clear Cache -->
                <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 12px; text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 10px;">🗑️</div>
                    <h3 style="margin-bottom: 10px;">Clear Cache</h3>
                    <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin-bottom: 15px;">
                        Hapus semua cache Laravel (config, route, view, app)
                    </p>
                    <form method="POST">
                        <input type="hidden" name="clear_cache" value="1">
                        <button type="submit" class="btn btn-danger btn-small">Hapus Cache</button>
                    </form>
                </div>
                
                <!-- Storage Link -->
                <div style="background: rgba(0,0,0,0.3); padding: 20px; border-radius: 12px; text-align: center;">
                    <div style="font-size: 40px; margin-bottom: 10px;">🔗</div>
                    <h3 style="margin-bottom: 10px;">Storage Link</h3>
                    <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin-bottom: 15px;">
                        Buat symbolic link dari public/storage ke storage/app/public
                    </p>
                    <form method="POST">
                        <input type="hidden" name="storage_link" value="1">
                        <button type="submit" class="btn btn-primary btn-small">Buat Link</button>
                    </form>
                </div>
            </div>
            
            <?php
            // ===== PROSES CLEAR CACHE =====
            if (isset($_POST['clear_cache'])) {
                echo '<div class="log-box" style="margin-top: 20px;">';
                echo '<p class="log-info">🔄 Menghapus cache Laravel...</p>';
                
                $basePath = dirname(__DIR__);
                $cacheDirs = [
                    $basePath . '/bootstrap/cache',
                    $basePath . '/storage/framework/cache/data',
                    $basePath . '/storage/framework/sessions',
                    $basePath . '/storage/framework/views',
                ];
                
                $cleared = 0;
                $errors = 0;
                
                foreach ($cacheDirs as $dir) {
                    if (is_dir($dir)) {
                        $files = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                            RecursiveIteratorIterator::CHILD_FIRST
                        );
                        
                        foreach ($files as $file) {
                            $filename = $file->getFilename();
                            // Skip .gitignore files
                            if ($filename === '.gitignore' || $filename === '.gitkeep') {
                                continue;
                            }
                            
                            try {
                                if ($file->isDir()) {
                                    @rmdir($file->getRealPath());
                                } else {
                                    @unlink($file->getRealPath());
                                    $cleared++;
                                }
                            } catch (Exception $e) {
                                $errors++;
                            }
                        }
                        echo '<p class="log-success">   ✓ ' . basename(dirname($dir)) . '/' . basename($dir) . ' dibersihkan</p>';
                    }
                }
                
                // Clear config cache file
                $configCache = $basePath . '/bootstrap/cache/config.php';
                if (file_exists($configCache)) {
                    @unlink($configCache);
                    echo '<p class="log-success">   ✓ config.php dihapus</p>';
                }
                
                // Clear routes cache file
                $routesCache = $basePath . '/bootstrap/cache/routes-v7.php';
                if (file_exists($routesCache)) {
                    @unlink($routesCache);
                    echo '<p class="log-success">   ✓ routes cache dihapus</p>';
                }
                
                // Clear services cache
                $servicesCache = $basePath . '/bootstrap/cache/services.php';
                if (file_exists($servicesCache)) {
                    @unlink($servicesCache);
                    echo '<p class="log-success">   ✓ services.php dihapus</p>';
                }
                
                // Clear packages cache
                $packagesCache = $basePath . '/bootstrap/cache/packages.php';
                if (file_exists($packagesCache)) {
                    @unlink($packagesCache);
                    echo '<p class="log-success">   ✓ packages.php dihapus</p>';
                }
                
                echo '<br>';
                echo '<p class="log-success">✅ Cache berhasil dihapus! (' . $cleared . ' file)</p>';
                if ($errors > 0) {
                    echo '<p class="log-warning">⚠️ ' . $errors . ' file tidak dapat dihapus (mungkin sedang digunakan)</p>';
                }
                echo '</div>';
            }
            
            // ===== PROSES STORAGE LINK =====
            if (isset($_POST['storage_link'])) {
                echo '<div class="log-box" style="margin-top: 20px;">';
                echo '<p class="log-info">🔄 Membuat storage link...</p>';
                
                $basePath = dirname(__DIR__);
                $publicStorage = __DIR__ . '/storage';
                $appStorage = $basePath . '/storage/app/public';
                
                // Cek apakah storage/app/public ada
                if (!is_dir($appStorage)) {
                    @mkdir($appStorage, 0755, true);
                    echo '<p class="log-info">   ℹ️ Folder storage/app/public dibuat</p>';
                }
                
                // Cek apakah link sudah ada
                if (is_link($publicStorage)) {
                    echo '<p class="log-info">   ℹ️ Symbolic link sudah ada</p>';
                    echo '<p class="log-success">✅ Storage link sudah terhubung!</p>';
                } elseif (is_dir($publicStorage)) {
                    echo '<p class="log-warning">   ⚠️ Folder public/storage sudah ada (bukan symbolic link)</p>';
                    echo '<p class="log-info">   ℹ️ Mencoba membuat link dengan metode alternatif...</p>';
                    
                    // Copy folder contents instead
                    echo '<p class="log-info">   ℹ️ Pada shared hosting, gunakan metode copy file manual atau konfigurasi .htaccess</p>';
                    echo '<p class="log-success">✅ Folder storage sudah ada, bisa digunakan untuk upload</p>';
                } else {
                    // Try to create symlink
                    $linkCreated = false;
                    
                    // Method 1: Native symlink
                    if (function_exists('symlink')) {
                        $linkCreated = @symlink($appStorage, $publicStorage);
                    }
                    
                    if ($linkCreated) {
                        echo '<p class="log-success">   ✓ Symbolic link berhasil dibuat!</p>';
                        echo '<p class="log-success">✅ Storage link aktif!</p>';
                    } else {
                        // Method 2: Create folder and copy
                        echo '<p class="log-warning">   ⚠️ Symlink tidak didukung oleh hosting ini</p>';
                        echo '<p class="log-info">   ℹ️ Membuat folder alternatif...</p>';
                        
                        if (@mkdir($publicStorage, 0755, true)) {
                            // Create .htaccess for URL rewrite
                            $htaccess = $publicStorage . '/.htaccess';
                            $htaccessContent = "<IfModule mod_rewrite.c>\n";
                            $htaccessContent .= "    RewriteEngine On\n";
                            $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME} !-f\n";
                            $htaccessContent .= "    RewriteCond %{REQUEST_FILENAME} !-d\n";
                            $htaccessContent .= "    RewriteRule ^(.*)$ /storage/app/public/$1 [L]\n";
                            $htaccessContent .= "</IfModule>\n";
                            
                            // Copy existing files from storage/app/public
                            if (is_dir($appStorage)) {
                                $files = new RecursiveIteratorIterator(
                                    new RecursiveDirectoryIterator($appStorage, RecursiveDirectoryIterator::SKIP_DOTS),
                                    RecursiveIteratorIterator::SELF_FIRST
                                );
                                
                                foreach ($files as $file) {
                                    $destPath = $publicStorage . '/' . substr($file->getRealPath(), strlen($appStorage) + 1);
                                    if ($file->isDir()) {
                                        @mkdir($destPath, 0755, true);
                                    } else {
                                        @copy($file->getRealPath(), $destPath);
                                    }
                                }
                                echo '<p class="log-success">   ✓ File dari storage/app/public berhasil di-copy</p>';
                            }
                            
                            echo '<p class="log-success">   ✓ Folder public/storage dibuat</p>';
                            echo '<p class="log-success">✅ Storage folder siap digunakan!</p>';
                            echo '<p class="log-warning">   ⚠️ Catatan: File upload baru akan tersimpan di storage/app/public</p>';
                            echo '<p class="log-info">   💡 Untuk sinkronisasi, jalankan storage link secara berkala</p>';
                        } else {
                            echo '<p class="log-error">   ✗ Gagal membuat folder public/storage</p>';
                            echo '<p class="log-info">   💡 Silakan buat folder secara manual via File Manager hosting</p>';
                        }
                    }
                }
                
                echo '</div>';
            }
            ?>
        </div>
        
        <!-- ===== INFO AKUN DEFAULT ===== -->
        <div class="card">
            <h2>👤 Akun Admin Default</h2>
            <div class="alert alert-info">
                Setelah migrasi selesai, Anda dapat login dengan akun berikut:
            </div>
            <div class="db-info">
                <div class="db-info-item">
                    <div class="db-info-label">Email</div>
                    <div class="db-info-value">admin@gmail.com</div>
                </div>
                <div class="db-info-item">
                    <div class="db-info-label">Password</div>
                    <div class="db-info-value">password</div>
                </div>
            </div>
            <div class="alert alert-warning" style="margin-top: 15px;">
                ⚠️ Segera ubah password setelah login pertama kali!
            </div>
        </div>
        
        <?php endif; ?>
    </div>
</body>
</html>

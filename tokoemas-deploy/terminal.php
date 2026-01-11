<?php
// WEB TERMINAL SEDERHANA (PENGGANTI SSH)
// Upload ke folder /public_html/tokoemas/sites/wates/public/terminal.php
// Akses: https://wates.hartowiyono.my.id/terminal.php?key=RAHASIA

// 1. KEAMANAN
$secretKey = 'RAHASIA'; // Ganti dengan key yang sulit ditebak!
if (($_GET['key'] ?? '') !== $secretKey && ($_POST['key'] ?? '') !== $secretKey) {
    die('Akses Ditolak. Gunakan ?key=RAHASIA');
}

// 2. SESSION UNTUK SIMPAN POSISI FOLDER (CWD)
session_start();

// Tentukan root path awal (masuk ke folder project, naik dari public/sites/wates)
$initialPath = realpath(dirname(__DIR__, 3)); 
if (!isset($_SESSION['cwd']) || !is_dir($_SESSION['cwd'])) {
    $_SESSION['cwd'] = $initialPath;
}

// Handle Reset Path
if (isset($_POST['reset'])) {
    $_SESSION['cwd'] = $initialPath;
}

$currentDir = $_SESSION['cwd'];
$output = "Web Terminal Ready.\nCurrent Dir: $currentDir\n";
$command = '';

// 3. EKSEKUSI PERINTAH
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cmd'])) {
    $command = $_POST['cmd'];
    
    // Fitur 'cd' (Change Directory) manual karena PHP stateless
    if (preg_match('/^cd\s+(.*)/', $command, $matches)) {
        $target = trim($matches[1]);
        
        // Handle cd .. atau absolut
        if (substr($target, 0, 1) === '/') {
            $newDir = $target; // Absolute
        } else {
            $newDir = $currentDir . '/' . $target; // Relative
        }
        
        $realDir = realpath($newDir);
        
        if ($realDir && is_dir($realDir)) {
            $_SESSION['cwd'] = $realDir;
            $currentDir = $realDir;
            $output = "$ cd $target\nDirectory changed to: $realDir";
        } else {
            $output = "$ cd $target\nError: Directory not found.";
        }
    } else {
        // Eksekusi Command Biasa
        $cmdToRun = "cd " . escapeshellarg($currentDir) . " && " . $command . " 2>&1";
        
        // Coba pakai shell_exec
        if (function_exists('shell_exec')) {
            $output = "$ " . $command . "\n" . shell_exec($cmdToRun);
        } elseif (function_exists('exec')) {
            $result = [];
            exec($cmdToRun, $result);
            $output = "$ " . $command . "\n" . implode("\n", $result);
        } else {
            $output = "Error: shell_exec dan exec dinonaktifkan di server ini.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Terminal</title>
    <style>
        body { background-color: #1e1e1e; color: #00ff00; font-family: 'Consolas', 'Courier New', monospace; padding: 20px; margin: 0; }
        .container { max-width: 900px; margin: 0 auto; }
        .output { background: #000; padding: 15px; border-radius: 5px; min-height: 300px; white-space: pre-wrap; overflow-x: auto; border: 1px solid #333; margin-bottom: 20px; box-shadow: 0 0 10px rgba(0,255,0,0.1); }
        .input-group { display: flex; gap: 10px; }
        input[type="text"] { flex-grow: 1; background: #333; border: 1px solid #555; color: #fff; padding: 10px; font-family: inherit; font-size: 16px; border-radius: 4px; }
        input[type="text"]:focus { outline: none; border-color: #00ff00; }
        button { background: #008800; color: #fff; border: none; padding: 10px 20px; cursor: pointer; font-family: inherit; font-weight: bold; border-radius: 4px; }
        button:hover { background: #00aa00; }
        .info { color: #888; font-size: 12px; margin-bottom: 5px; }
        a { color: #00aaaa; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <h3>Web Terminal (<?= basename($currentDir) ?>)</h3>
        <p class="info">Path: <?= $currentDir ?></p>
        
        <div class="output"><?= htmlspecialchars($output) ?></div>
        
        <form method="POST">
            <input type="hidden" name="key" value="<?= htmlspecialchars($secretKey) ?>">
            <div class="input-group">
                <span style="padding-top:10px; font-weight:bold;">$</span>
                <input type="text" name="cmd" autofocus autocomplete="off" placeholder="Ketik perintah (ls, php artisan list, git status)...">
                <button type="submit">ENTER</button>
            </div>
        </form>
        <br>
        <form method="POST">
             <input type="hidden" name="key" value="<?= htmlspecialchars($secretKey) ?>">
             <input type="hidden" name="reset" value="1">
             <button type="submit" style="background:#555; font-size:12px;">Reset Directory</button>
        </form>
    </div>
    
    <script>
        // Auto scroll to bottom
        const output = document.querySelector('.output');
        output.scrollTop = output.scrollHeight;
    </script>
</body>
</html>

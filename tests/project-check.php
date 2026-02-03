<?php
// check_project.php - File untuk check struktur project
// Akses di browser: http://localhost/CoffeeshopCimahi/check_project.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Structure Check</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #6f4e37;
            padding-bottom: 10px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .file-item {
            display: flex;
            align-items: center;
            padding: 10px;
            margin: 5px 0;
            border-radius: 4px;
            border-left: 4px solid #ccc;
        }
        .file-item.exists {
            background-color: #e8f5e9;
            border-left-color: #4caf50;
        }
        .file-item.missing {
            background-color: #ffebee;
            border-left-color: #f44336;
        }
        .file-status {
            font-weight: bold;
            margin-right: 15px;
            width: 100px;
        }
        .file-path {
            font-family: 'Courier New', monospace;
            flex: 1;
        }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .stat {
            padding: 15px;
            text-align: center;
            border-radius: 6px;
            color: white;
        }
        .stat-total {
            background-color: #3498db;
        }
        .stat-exists {
            background-color: #27ae60;
        }
        .stat-missing {
            background-color: #e74c3c;
        }
        .stat-number {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info-box {
            background-color: #d1ecf1;
            border-left: 4px solid #17a2b8;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            color: #0c5460;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <h1>🔍 Project Structure Check</h1>
    
    <div class="container">
        <div class="info-box">
            <strong>✓ Current Path:</strong><br>
            <code><?php echo __DIR__; ?></code>
        </div>

        <h2>📁 Essential Files</h2>
        <?php
        $files = [
            'HTML Files' => [
                'index.html',
                'login-admin.html',
                'dashboard-admin.html'
            ],
            'CSS Files' => [
                'style.css'
            ],
            'JavaScript Files' => [
                'script.js',
                'script-admin.js'
            ],
            'PHP Backend Files' => [
                'add_coffeeshop.php',
                'config.php'
            ],
            'Configuration Files' => [
                'database_schema.sql',
                'test_database.php',
                'check_project.php'
            ],
            'Documentation Files' => [
                'docs/README.md',
                'docs/FITUR-TAMBAH-COFFEESHOP.md',
                'QUICK-START-COFFEESHOP.md',
                'DEBUGGING-GUIDE.md'
            ]
        ];

        $total = 0;
        $exists = 0;
        $missing = 0;

        foreach ($files as $category => $file_list) {
            echo "<h3>$category</h3>";
            foreach ($file_list as $file) {
                $full_path = __DIR__ . DIRECTORY_SEPARATOR . $file;
                $file_exists = file_exists($full_path);
                $total++;
                
                if ($file_exists) {
                    $exists++;
                    $status = "✅ EXISTS";
                    $class = "exists";
                } else {
                    $missing++;
                    $status = "❌ MISSING";
                    $class = "missing";
                }
                
                echo "<div class='file-item $class'>";
                echo "<span class='file-status'>$status</span>";
                echo "<span class='file-path'>$file</span>";
                echo "</div>";
            }
        }
        ?>
    </div>

    <div class="container">
        <h2>📊 Summary</h2>
        <div class="summary">
            <div class="stat stat-total">
                <div>Total Files</div>
                <div class="stat-number"><?php echo $total; ?></div>
            </div>
            <div class="stat stat-exists">
                <div>Files Found</div>
                <div class="stat-number"><?php echo $exists; ?></div>
            </div>
            <div class="stat stat-missing">
                <div>Missing Files</div>
                <div class="stat-number"><?php echo $missing; ?></div>
            </div>
        </div>
    </div>

    <div class="container">
        <h2>🔧 Configuration Status</h2>
        <?php
        // Check PHP Extensions
        echo "<h3>PHP Extensions</h3>";
        $extensions = [
            'mysqli' => 'MySQLi (Database)',
            'json' => 'JSON',
            'filter' => 'Filter'
        ];

        foreach ($extensions as $ext => $name) {
            $loaded = extension_loaded($ext);
            $status = $loaded ? "✅ LOADED" : "❌ NOT LOADED";
            $color = $loaded ? "exists" : "missing";
            echo "<div class='file-item $color'>";
            echo "<span class='file-status'>$status</span>";
            echo "<span class='file-path'>$name</span>";
            echo "</div>";
        }

        // Check file permissions
        echo "<h3>File Permissions</h3>";
        $check_files = [
            'add_coffeeshop.php',
            'test_database.php',
            'check_project.php'
        ];

        foreach ($check_files as $file) {
            $full_path = __DIR__ . DIRECTORY_SEPARATOR . $file;
            if (file_exists($full_path)) {
                $readable = is_readable($full_path) ? "✅ READABLE" : "❌ NOT READABLE";
                $writable = is_writable($full_path) ? "✅ WRITABLE" : "⚠️ NOT WRITABLE";
                echo "<div class='file-item exists'>";
                echo "<span class='file-path'><strong>$file</strong><br>";
                echo "Read: $readable | Write: $writable";
                echo "</span></div>";
            }
        }
        ?>
    </div>

    <div class="container">
        <h2>✅ Next Steps</h2>
        <div class="info-box">
            <p>1. Jika ada file yang ❌ MISSING, pastikan sudah di-create</p>
            <p>2. Jalankan <strong><code>test_database.php</code></strong> untuk test koneksi database</p>
            <p>3. Baca <strong><code>QUICK-START-COFFEESHOP.md</code></strong> untuk setup cepat</p>
            <p>4. Jika ada error, baca <strong><code>DEBUGGING-GUIDE.md</code></strong></p>
        </div>
    </div>

    <div class="container" style="margin-top: 30px; text-align: center;">
        <p style="color: #999; font-size: 12px;">
            Generated: <?php echo date('Y-m-d H:i:s'); ?><br>
            PHP Version: <?php echo phpversion(); ?>
        </p>
    </div>
</body>
</html>

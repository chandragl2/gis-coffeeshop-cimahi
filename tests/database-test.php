<?php
// test_database.php - File untuk test koneksi database
// Akses di browser: http://localhost/CoffeeshopCimahi/test_database.php

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Database Connection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .container {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #2c3e50;
        }
        .success {
            background-color: #d4edda;
            border-left-color: #28a745;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border-left-color: #dc3545;
            color: #721c24;
        }
        .info {
            background-color: #d1ecf1;
            border-left-color: #17a2b8;
            color: #0c5460;
        }
        h2 {
            margin-top: 0;
        }
        code {
            background: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        pre {
            background: #fff;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔧 Test Koneksi Database</h1>

    <?php
    // Konfigurasi Database
    $db_host = "localhost";
    $db_port = 3306;
    $db_username = "root";
    $db_password = "Kamarmandi23.";
    $db_name = "gis_coffeeshop";

    echo '<div class="container info">';
    echo '<h2>📋 Konfigurasi Database</h2>';
    echo '<p><strong>Host:</strong> <code>' . htmlspecialchars($db_host) . '</code></p>';
    echo '<p><strong>Port:</strong> <code>' . htmlspecialchars($db_port) . '</code></p>';
    echo '<p><strong>Username:</strong> <code>' . htmlspecialchars($db_username) . '</code></p>';
    echo '<p><strong>Password:</strong> <code>' . ($db_password ? '***' : 'Kosong') . '</code></p>';
    echo '<p><strong>Database:</strong> <code>' . htmlspecialchars($db_name) . '</code></p>';
    echo '</div>';

    // Test 1: Koneksi Database
    echo '<div class="container" style="margin-top: 20px;">';
    echo '<h2>✅ Test 1: Koneksi Database</h2>';

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name, $db_port);

    if ($conn->connect_error) {
        echo '<div class="error">';
        echo '<p><strong>❌ Koneksi Gagal:</strong></p>';
        echo '<pre>' . htmlspecialchars($conn->connect_error) . '</pre>';
        echo '<p><strong>Solusi:</strong></p>';
        echo '<ul>';
        echo '<li>Pastikan MySQL/PhpMyAdmin sudah running</li>';
        echo '<li>Check username dan password di file ini</li>';
        echo '<li>Pastikan database <code>gis_coffeeshop</code> sudah dibuat</li>';
        echo '</ul>';
        echo '</div>';
        exit;
    }

    echo '<div class="success">';
    echo '<p>✅ <strong>Koneksi Berhasil!</strong></p>';
    echo '</div>';
    echo '</div>';

    // Test 2: Check Tabel
    echo '<div class="container" style="margin-top: 20px;">';
    echo '<h2>✅ Test 2: Check Tabel coffeeshops</h2>';

    $result = $conn->query("SHOW TABLES LIKE 'coffeeshops'");
    
    if ($result->num_rows === 0) {
        echo '<div class="error">';
        echo '<p><strong>❌ Tabel "coffeeshops" tidak ditemukan!</strong></p>';
        echo '<p><strong>Solusi:</strong></p>';
        echo '<ul>';
        echo '<li>Buka PhpMyAdmin: <code>http://localhost/phpmyadmin</code></li>';
        echo '<li>Pilih database <code>gis_coffeeshop</code></li>';
        echo '<li>Pergi ke tab <code>SQL</code></li>';
        echo '<li>Copy dan paste isi file <code>database_schema.sql</code></li>';
        echo '<li>Klik <code>Go</code> atau <code>Execute</code></li>';
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="success">';
        echo '<p>✅ <strong>Tabel "coffeeshops" ditemukan!</strong></p>';
        echo '</div>';

        // Test 3: Check struktur tabel
        echo '<div class="container" style="margin-top: 20px;">';
        echo '<h2>✅ Test 3: Struktur Tabel</h2>';

        $columns = $conn->query("DESCRIBE coffeeshops");
        echo '<table style="width: 100%; border-collapse: collapse;">';
        echo '<tr style="background: #34495e; color: white;">';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Column</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Type</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Null</th>';
        echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Key</th>';
        echo '</tr>';

        while ($col = $columns->fetch_assoc()) {
            echo '<tr style="background: #ecf0f1;">';
            echo '<td style="padding: 10px; border: 1px solid #ddd;"><code>' . htmlspecialchars($col['Field']) . '</code></td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;"><code>' . htmlspecialchars($col['Type']) . '</code></td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($col['Null']) . '</td>';
            echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($col['Key']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';

        // Test 4: Count data
        echo '<div class="container" style="margin-top: 20px;">';
        echo '<h2>✅ Test 4: Data di Tabel</h2>';

        $count = $conn->query("SELECT COUNT(*) as total FROM coffeeshops");
        $row = $count->fetch_assoc();
        
        echo '<div class="success">';
        echo '<p>📊 <strong>Total Coffeeshop:</strong> ' . $row['total'] . ' baris</p>';
        echo '</div>';

        if ($row['total'] > 0) {
            echo '<div class="container info" style="margin-top: 20px;">';
            echo '<h2>📋 Preview Data</h2>';
            echo '<table style="width: 100%; border-collapse: collapse;">';
            echo '<tr style="background: #34495e; color: white;">';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">ID</th>';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Nama</th>';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Rating</th>';
            echo '<th style="padding: 10px; text-align: left; border: 1px solid #ddd;">Status</th>';
            echo '</tr>';

            $data = $conn->query("SELECT id, name, rating, status FROM coffeeshops LIMIT 5");
            while ($item = $data->fetch_assoc()) {
                echo '<tr style="background: #ecf0f1;">';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($item['id']) . '</td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($item['name']) . '</td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($item['rating']) . '</td>';
                echo '<td style="padding: 10px; border: 1px solid #ddd;">' . htmlspecialchars($item['status']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
    }

    $conn->close();

    echo '<div class="container success" style="margin-top: 20px;">';
    echo '<h2>✅ Semua Test Selesai!</h2>';
    echo '<p>Jika semua test berhasil, Anda bisa mulai menggunakan fitur Tambah Coffeeshop di dashboard admin.</p>';
    echo '<p><a href="dashboard-admin.html" style="color: #155724; font-weight: bold;">👉 Kembali ke Dashboard Admin</a></p>';
    echo '</div>';
    ?>
</body>
</html>

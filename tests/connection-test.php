<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Port MySQL</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        code { background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔍 Mencari Port MySQL</h1>
    
    <p>Mencoba berbagai port dan koneksi method...</p>
    
    <?php
    $ports_to_try = [3306, 3307, 8112, 33060, 3308];
    $found = false;
    
    echo '<h2>Mencoba Port TCP/IP:</h2>';
    foreach ($ports_to_try as $port) {
        $conn = @new mysqli('127.0.0.1', 'root', '', 'mysql', $port);
        if (!$conn->connect_error) {
            echo '<div class="success">✅ BERHASIL di port <code>' . $port . '</code></div>';
            $found = true;
            $working_port = $port;
            $conn->close();
        } else {
            echo '<div class="error">❌ Port ' . $port . ' - ' . htmlspecialchars($conn->connect_error) . '</div>';
        }
    }
    
    // Try localhost
    echo '<h2>Coba localhost:</h2>';
    $conn = @new mysqli('localhost', 'root', '');
    if (!$conn->connect_error) {
        echo '<div class="success">✅ BERHASIL dengan localhost (port default 3306)</div>';
        $found = true;
        $working_port = 3306;
        $conn->close();
    } else {
        echo '<div class="error">❌ localhost - ' . htmlspecialchars($conn->connect_error) . '</div>';
    }
    
    if ($found) {
        echo '<div class="info"><strong>Port MySQL yang bekerja:</strong> <code>' . $working_port . '</code></div>';
        echo '<p><strong>Update config.php dan file lain dengan port: ' . $working_port . '</strong></p>';
    } else {
        echo '<div class="error"><strong>⚠️ MySQL tidak terdeteksi di port manapun!</strong></div>';
        echo '<p>Solusi:</p>';
        echo '<ul>';
        echo '<li>Buka XAMPP Control Panel</li>';
        echo '<li>Cari "MySQL" - Klik "Start"</li>';
        echo '<li>Tunggu sampai status menjadi hijau (Running)</li>';
        echo '<li>Refresh halaman ini</li>';
        echo '</ul>';
    }
    
    // Check php.ini settings
    echo '<h2>📋 Informasi PHP:</h2>';
    echo '<div class="info">';
    echo '<p><strong>MySQL default port:</strong> <code>' . ini_get('mysqli.default_port') . '</code></p>';
    echo '<p><strong>MySQL default socket:</strong> <code>' . ini_get('mysqli.default_socket') . '</code></p>';
    echo '</div>';
    ?>
</body>
</html>

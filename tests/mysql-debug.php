<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug MySQL Connection</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .box { padding: 15px; margin: 10px 0; border-radius: 5px; }
        .ok { background: #d4edda; color: #155724; }
        .bad { background: #f8d7da; color: #721c24; }
        code { background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔧 Debug MySQL Connection</h1>
    
    <?php
    // Test 1: cek port terbuka
    echo '<div class="box"><h3>Test 1: Coba koneksi socket TCP</h3>';
    $fp = @fsockopen('localhost', 8112, $errno, $errstr, 5);
    if ($fp) {
        echo '<div class="box ok">✅ Port 8112 terbuka dan listening</div>';
        fclose($fp);
    } else {
        echo '<div class="box bad">❌ Port 8112 tidak terbuka: ' . $errstr . '</div>';
    }
    echo '</div>';
    
    // Test 2: mysqli tanpa port
    echo '<div class="box"><h3>Test 2: mysqli dengan localhost (port default)</h3>';
    $conn = @new mysqli('localhost', 'root', '', '', 0);
    if (!$conn->connect_error) {
        echo '<div class="box ok">✅ BERHASIL terhubung ke MySQL!</div>';
        echo '<p>Versi MySQL: ' . htmlspecialchars($conn->server_info) . '</p>';
        
        // Test 3: cek database
        echo '<div class="box"><h3>Test 3: Cek database gis_coffeeshop</h3>';
        if ($conn->select_db('gis_coffeeshop')) {
            echo '<div class="box ok">✅ Database gis_coffeeshop ditemukan</div>';
            
            // Test 4: cek tabel
            echo '<div class="box"><h3>Test 4: Cek tabel coffeeshops</h3>';
            $result = $conn->query("SHOW TABLES LIKE 'coffeeshops'");
            if ($result && $result->num_rows > 0) {
                echo '<div class="box ok">✅ Tabel coffeeshops ditemukan</div>';
                
                // Test 5: cek data
                echo '<div class="box"><h3>Test 5: Cek isi tabel</h3>';
                $data = $conn->query("SELECT COUNT(*) as cnt FROM coffeeshops");
                $row = $data->fetch_assoc();
                echo '<div class="box ok">✅ Tabel berisi ' . $row['cnt'] . ' data</div>';
            } else {
                echo '<div class="box bad">❌ Tabel coffeeshops tidak ditemukan</div>';
            }
            echo '</div>';
        } else {
            echo '<div class="box bad">❌ Database gis_coffeeshop tidak ditemukan</div>';
        }
        
        $conn->close();
    } else {
        echo '<div class="box bad">❌ Koneksi gagal: ' . htmlspecialchars($conn->connect_error) . '</div>';
        
        // Test dengan port eksplisit
        echo '<div class="box"><h3>Test 2b: Coba dengan port 8112 eksplisit</h3>';
        $conn2 = @new mysqli('localhost', 'root', '', '', 8112);
        if (!$conn2->connect_error) {
            echo '<div class="box ok">✅ BERHASIL dengan port 8112 eksplisit!</div>';
            $conn2->close();
        } else {
            echo '<div class="box bad">❌ Port 8112 juga gagal: ' . htmlspecialchars($conn2->connect_error) . '</div>';
        }
        echo '</div>';
    }
    ?>
    
    <div class="box" style="background: #d1ecf1; color: #0c5460; margin-top: 20px;">
        <h3>📋 Informasi PHP/MySQL</h3>
        <p><strong>PHP Version:</strong> <code><?php echo PHP_VERSION; ?></code></p>
        <p><strong>MySQLi Available:</strong> <code><?php echo extension_loaded('mysqli') ? 'Yes' : 'No'; ?></code></p>
        <p><strong>Default MySQL Port (php.ini):</strong> <code><?php echo ini_get('mysqli.default_port') ?: '3306'; ?></code></p>
        <p><strong>Default MySQL Socket:</strong> <code><?php echo ini_get('mysqli.default_socket') ?: '(none)'; ?></code></p>
    </div>
</body>
</html>

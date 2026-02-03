<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Setup Database</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        code { background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🔧 Setup Database Coffeeshop</h1>
    
    <?php
    $db_host = "localhost";
    $db_port = 3306;
    $db_username = "root";
    $db_password = "Kamarmandi23.";
    $db_name = "gis_coffeeshop";
    
    // Koneksi tanpa database
    $conn = new mysqli($db_host, $db_username, $db_password, "", $db_port);
    
    if ($conn->connect_error) {
        echo '<div class="error"><strong>❌ Koneksi MySQL gagal:</strong><br>' . htmlspecialchars($conn->connect_error) . '</div>';
        exit;
    }
    
    echo '<div class="info">✅ Terhubung ke MySQL di localhost:' . $db_port . '</div>';
    
    // Langkah 1: Buat database
    echo '<h2>Langkah 1: Membuat Database</h2>';
    $sql_create_db = "CREATE DATABASE IF NOT EXISTS `" . $db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    
    if ($conn->query($sql_create_db) === TRUE) {
        echo '<div class="success">✅ Database <code>' . $db_name . '</code> berhasil dibuat/sudah ada</div>';
    } else {
        echo '<div class="error">❌ Error: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }
    
    // Langkah 2: Pilih database
    echo '<h2>Langkah 2: Memilih Database</h2>';
    if ($conn->select_db($db_name)) {
        echo '<div class="success">✅ Database <code>' . $db_name . '</code> dipilih</div>';
    } else {
        echo '<div class="error">❌ Error: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }
    
    // Langkah 3: Buat tabel
    echo '<h2>Langkah 3: Membuat Tabel coffeeshops</h2>';
    $sql_create_table = "CREATE TABLE IF NOT EXISTS coffeeshops (
        id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL UNIQUE,
        address TEXT NOT NULL,
        latitude DECIMAL(10, 8) NOT NULL,
        longitude DECIMAL(11, 8) NOT NULL,
        rating DECIMAL(3, 2) NOT NULL CHECK (rating >= 0 AND rating <= 5),
        status VARCHAR(50) NOT NULL DEFAULT 'Aktif',
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_name (name),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql_create_table) === TRUE) {
        echo '<div class="success">✅ Tabel <code>coffeeshops</code> berhasil dibuat/sudah ada</div>';
    } else {
        echo '<div class="error">❌ Error: ' . htmlspecialchars($conn->error) . '</div>';
        exit;
    }
    
    // Langkah 4: Insert data dummy (jika tabel masih kosong)
    echo '<h2>Langkah 4: Memasukkan Data Dummy</h2>';
    $check_data = $conn->query("SELECT COUNT(*) as cnt FROM coffeeshops");
    $row = $check_data->fetch_assoc();
    
    if ($row['cnt'] == 0) {
        $sql_insert = "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone) VALUES
        ('Kopi Bersaudara', 'Jl. Pendidikan No. 45, Cimahi', -6.8886, 107.5570, 4.5, 'Aktif', '0271-123456'),
        ('The Coffee House', 'Jl. Raya Cimahi No. 120, Cimahi', -6.8950, 107.5480, 4.7, 'Aktif', '0271-123457'),
        ('Café Indah', 'Jl. Sipakubumen No. 78, Cimahi', -6.8820, 107.5620, 4.6, 'Aktif', '0271-123458'),
        ('Kopi Nusantara', 'Jl. Kompas No. 32, Cimahi', -6.9000, 107.5500, 4.4, 'Aktif', '0271-123459'),
        ('Brew Station', 'Jl. Moch. Toha No. 15, Cimahi', -6.8900, 107.5550, 4.8, 'Aktif', '0271-123460')";
        
        if ($conn->query($sql_insert) === TRUE) {
            echo '<div class="success">✅ ' . $conn->affected_rows . ' data dummy berhasil dimasukkan</div>';
        } else {
            echo '<div class="error">❌ Error: ' . htmlspecialchars($conn->error) . '</div>';
        }
    } else {
        echo '<div class="info">ℹ️ Tabel sudah berisi ' . $row['cnt'] . ' data, tidak perlu insert ulang</div>';
    }
    
    // Langkah 5: Verifikasi
    echo '<h2>Langkah 5: Verifikasi</h2>';
    $verify = $conn->query("SELECT COUNT(*) as total FROM coffeeshops");
    $verify_row = $verify->fetch_assoc();
    echo '<div class="success">✅ Total coffeeshop di database: <strong>' . $verify_row['total'] . '</strong> data</div>';
    
    $conn->close();
    
    echo '<div class="success" style="margin-top: 20px;">';
    echo '<h2>✅ Setup Berhasil!</h2>';
    echo '<p>Database dan tabel sudah siap digunakan.</p>';
    echo '<p><a href="dashboard-admin.html" style="color: #155724; font-weight: bold; font-size: 16px;">👉 Kembali ke Dashboard Admin</a></p>';
    echo '</div>';
    ?>
</body>
</html>

<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Insert Coffeeshop Data</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        code { background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>📊 Insert Coffeeshop Data</h1>
    
    <?php
    require_once __DIR__ . '/backend/config/database.php';
    
    $mysqli = $GLOBALS['db'];
    
    if (!$mysqli) {
        echo '<div class="error"><strong>❌ Koneksi database gagal</strong></div>';
        exit;
    }
    
    echo '<div class="success">✅ Terhubung ke database: gis_coffeeshop</div>';
    
    // Langkah 1: Check columns exist
    echo '<h2>Langkah 1: Cek Kolom Database</h2>';
    
    $result = $mysqli->query("SHOW COLUMNS FROM coffeeshops");
    $columns = [];
    
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row['Type'];
    }
    
    $required_cols = ['category', 'kecamatan', 'kelurahan', 'description'];
    $missing_cols = [];
    
    foreach ($required_cols as $col) {
        if (isset($columns[$col])) {
            echo '<div class="success">✅ Kolom <code>' . $col . '</code> ada</div>';
        } else {
            echo '<div class="error">❌ Kolom <code>' . $col . '</code> tidak ada - AKAN DITAMBAHKAN</div>';
            $missing_cols[] = $col;
        }
    }
    
    // Add missing columns
    if (!empty($missing_cols)) {
        echo '<h2>Langkah 2: Tambah Kolom yang Hilang</h2>';
        
        $alter_queries = [
            "ALTER TABLE coffeeshops ADD COLUMN category VARCHAR(100) AFTER phone" => "category",
            "ALTER TABLE coffeeshops ADD COLUMN kecamatan VARCHAR(100) AFTER category" => "kecamatan",
            "ALTER TABLE coffeeshops ADD COLUMN kelurahan VARCHAR(100) AFTER kecamatan" => "kelurahan",
            "ALTER TABLE coffeeshops ADD COLUMN description TEXT AFTER kelurahan" => "description",
        ];
        
        foreach ($alter_queries as $sql => $col_name) {
            if (in_array($col_name, $missing_cols)) {
                if ($mysqli->query($sql)) {
                    echo '<div class="success">✅ Kolom ' . $col_name . ' ditambahkan</div>';
                } else {
                    if (strpos($mysqli->error, 'Duplicate') !== false) {
                        echo '<div class="info">ℹ️ Kolom ' . $col_name . ' sudah ada</div>';
                    } else {
                        echo '<div class="error">❌ Error: ' . $mysqli->error . '</div>';
                    }
                }
            }
        }
    }
    
    // Langkah 3: Clear existing data
    echo '<h2>Langkah 3: Clear Data Lama</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops");
    $row = $result->fetch_assoc();
    $count = $row['count'];
    
    if ($count > 0) {
        if ($mysqli->query("DELETE FROM coffeeshops")) {
            echo '<div class="success">✅ ' . $count . ' data lama dihapus</div>';
        } else {
            echo '<div class="error">❌ Gagal hapus data: ' . $mysqli->error . '</div>';
        }
    } else {
        echo '<div class="info">ℹ️ Database sudah kosong</div>';
    }
    
    // Langkah 4: Insert data baru
    echo '<h2>Langkah 4: Insert Data Baru</h2>';
    
    $data = [
        ['Kopi Bersaudara', 'Jl. Pendidikan No. 45, Cimahi', -6.8886, 107.5570, 4.5, 'Aktif', '0271-123456', 'Kafe Modern', 'Cimahi Tengah', 'Setiabudhi', 'Kafe modern dengan suasana hangat dan nyaman.'],
        ['The Coffee House', 'Jl. Raya Cimahi No. 120, Cimahi', -6.8950, 107.5480, 4.7, 'Aktif', '0271-123457', 'Specialty Coffee', 'Cimahi Utara', 'Cibabat', 'Kedai kopi spesial dengan biji kopi pilihan.'],
        ['Café Indah', 'Jl. Sipakubumen No. 78, Cimahi', -6.8820, 107.5620, 4.6, 'Aktif', '0271-123458', 'Kafe Tradisional', 'Cimahi Selatan', 'Leuwigajah', 'Kafe tradisional dengan cita rasa lokal.'],
        ['Kopi Nusantara', 'Jl. Kompas No. 32, Cimahi', -6.9000, 107.5500, 4.4, 'Aktif', '0271-123459', 'Warkop', 'Cimahi Utara', 'Cibabat', 'Warkop asli dengan suasana rakyat.'],
        ['Brew Station', 'Jl. Moch. Toha No. 15, Cimahi', -6.8900, 107.5550, 4.8, 'Aktif', '0271-123460', 'Specialty Coffee', 'Cimahi Tengah', 'Melong', 'Tempat brewing kopi dengan teknik modern.'],
        ['Warkop Seuseupan', 'Jl. Cipaganti No. 99, Cimahi', -6.8750, 107.5650, 4.3, 'Aktif', '0271-123461', 'Warkop', 'Cimahi Selatan', 'Leuwigajah', 'Warkop seuseupan khas Bandung.'],
        ['Coffee & Co.', 'Jl. Pasteur No. 67, Cimahi', -6.9050, 107.5420, 4.7, 'Aktif', '0271-123462', 'Kafe Modern', 'Cimahi Utara', 'Sadang Serang', 'Kafe dengan konsep minimalis dan cozy.'],
        ['Kopitiam', 'Jl. Cikampak No. 45, Cimahi', -6.8850, 107.5700, 4.5, 'Aktif', '0271-123463', 'Specialty Coffee', 'Cimahi Tengah', 'Setiabudhi', 'Kedai kopi dengan interior unik dan instagramable.'],
    ];
    
    $inserted = 0;
    $failed = 0;
    
    foreach ($data as $d) {
        $stmt = $mysqli->prepare("INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone, category, kecamatan, kelurahan, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            echo '<div class="error">❌ ' . $d[0] . ': Prepare failed - ' . $mysqli->error . '</div>';
            $failed++;
            continue;
        }
        
        // Bind parameters: s=string, d=double, d=double, d=double, d=double, s=string, s=string, s=string, s=string, s=string, s=string
        $stmt->bind_param('sddddssssss', 
            $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], 
            $d[7], $d[8], $d[9], $d[10]
        );
        
        if ($stmt->execute()) {
            echo '<div class="success">✅ ' . $d[0] . '</div>';
            $inserted++;
        } else {
            echo '<div class="error">❌ ' . $d[0] . ': ' . $stmt->error . '</div>';
            $failed++;
        }
        
        $stmt->close();
    }
    
    // Langkah 5: Verify
    echo '<h2>Langkah 5: Verifikasi</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops");
    $row = $result->fetch_assoc();
    $total = $row['count'];
    
    echo '<div class="success">✅ Total data: <strong>' . $total . '</strong> coffeeshop</div>';
    echo '<div class="success">✅ Inserted: ' . $inserted . ' | Failed: ' . $failed . '</div>';
    
    ?>
    
    <h2>✨ Complete!</h2>
    <div class="success">
        <strong>Data sudah ter-update!</strong><br><br>
        Silakan refresh halaman admin untuk melihat data yang ter-updated.
    </div>
    
    <p style="margin-top: 30px; text-align: center;">
        <a href="admin/index.html" style="display: inline-block; padding: 10px 20px; background: #6f4e37; color: white; text-decoration: none; border-radius: 5px;">Buka Admin Dashboard</a>
        &nbsp;&nbsp;
        <a href="public/index.html" style="display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px;">Buka Halaman Public</a>
    </p>
</body>
</html>

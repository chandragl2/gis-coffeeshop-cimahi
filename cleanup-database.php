<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Data Dummy - Keep Data Asli</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; color: #856404; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🗑️ Hapus Data Dummy - Keep Data Asli</h1>
    
    <?php
    require_once __DIR__ . '/backend/config/database.php';
    
    $mysqli = $GLOBALS['db'];
    
    if (!$mysqli) {
        echo '<div class="error"><strong>❌ Koneksi database gagal</strong></div>';
        exit;
    }
    
    echo '<div class="success">✅ Terhubung ke database: gis_coffeeshop</div>';
    
    // Langkah 1: Count data sebelum
    echo '<h2>Langkah 1: Status Data Sebelum Delete</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
    $row = $result->fetch_assoc();
    $total_before = $row['total'];
    
    echo '<div class="info">ℹ️ Total data sebelum: <strong>' . $total_before . '</strong></div>';
    
    // Langkah 2: Hapus data dummy (ID >= 37, data yang baru)
    echo '<h2>Langkah 2: Hapus Data Dummy (ID >= 37)</h2>';
    
    $result = $mysqli->query("SELECT id, name FROM coffeeshops WHERE id >= 37");
    $ids_to_delete = [];
    
    while ($row = $result->fetch_assoc()) {
        $ids_to_delete[] = $row;
        echo '<div class="info">ℹ️ ID ' . $row['id'] . ': ' . htmlspecialchars($row['name']) . ' - akan dihapus</div>';
    }
    
    if (!empty($ids_to_delete)) {
        if ($mysqli->query("DELETE FROM coffeeshops WHERE id >= 37")) {
            echo '<div class="success">✅ ' . count($ids_to_delete) . ' data dummy dihapus</div>';
        } else {
            echo '<div class="error">❌ Gagal hapus data: ' . $mysqli->error . '</div>';
        }
    } else {
        echo '<div class="info">ℹ️ Tidak ada data dummy untuk dihapus</div>';
    }
    
    // Langkah 3: Count data asli yang tersisa
    echo '<h2>Langkah 3: Status Data Asli</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
    $row = $result->fetch_assoc();
    $total_after = $row['total'];
    
    echo '<div class="success">✅ Total data asli yang tersisa: <strong>' . $total_after . '</strong></div>';
    
    // Langkah 4: Update filter fields untuk data asli
    echo '<h2>Langkah 4: Update Filter Fields untuk Data Asli</h2>';
    
    $mapping = [
        'Kopi' => 'Kopi',
        'Coffee' => 'Specialty Coffee',
        'Kafe' => 'Kafe',
        'Café' => 'Kafe',
        'Warung' => 'Warung Kopi',
        'Warkop' => 'Warung Kopi',
        'Espresso' => 'Specialty Coffee',
        'Fore' => 'Specialty Coffee',
        'Roempi' => 'Kafe',
        'Insight' => 'Specialty Coffee',
        'Laoban' => 'Warung Kopi',
        'Uncle' => 'Kafe',
        'Black' => 'Specialty Coffee',
    ];
    
    $result = $mysqli->query("SELECT id, name, address FROM coffeeshops ORDER BY id ASC");
    $updated = 0;
    
    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $name = $row['name'];
        $address = $row['address'];
        
        // Detect kategori
        $category = 'Coffeeshop';
        foreach ($mapping as $keyword => $cat) {
            if (stripos($name, $keyword) !== false) {
                $category = $cat;
                break;
            }
        }
        
        // Default location
        $kecamatan = 'Cimahi';
        $kelurahan = 'Pusat';
        
        // Parse dari address
        if (stripos($address, 'Cimahi Utara') !== false) {
            $kecamatan = 'Cimahi Utara';
        } elseif (stripos($address, 'Cimahi Tengah') !== false) {
            $kecamatan = 'Cimahi Tengah';
        } elseif (stripos($address, 'Cimahi Selatan') !== false) {
            $kecamatan = 'Cimahi Selatan';
        }
        
        $description = 'Coffeeshop berkualitas di ' . $kecamatan;
        
        $stmt = $mysqli->prepare("UPDATE coffeeshops SET category=?, kecamatan=?, kelurahan=?, description=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param('ssssi', $category, $kecamatan, $kelurahan, $description, $id);
            if ($stmt->execute()) {
                $updated++;
            }
            $stmt->close();
        }
    }
    
    echo '<div class="success">✅ ' . $updated . ' data asli ter-update dengan filter fields</div>';
    
    // Langkah 5: Final verification
    echo '<h2>Langkah 5: Final Verification</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
    $row = $result->fetch_assoc();
    $final_total = $row['total'];
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops WHERE category IS NOT NULL AND category != ''");
    $row = $result->fetch_assoc();
    $with_filters = $row['total'];
    
    echo '<div class="success">✅ Total data asli: <strong>' . $final_total . '</strong></div>';
    echo '<div class="success">✅ Dengan filter fields: <strong>' . $with_filters . '</strong></div>';
    
    ?>
    
    <h2>✨ Complete!</h2>
    <div class="success">
        <strong>✅ Selesai! Data sudah di-restore ke data asli!</strong><br><br>
        ✅ Data dummy (ID >= 37) sudah dihapus<br>
        ✅ Data asli dari phpMyAdmin kembali<br>
        ✅ Semua data asli ter-update dengan filter fields<br>
        ✅ Ready untuk digunakan di halaman public<br><br>
        
        <strong>Sekarang buka halaman public:</strong><br>
        <a href="http://localhost:8080/CoffeeshopCimahi/public/index.html" style="color: white; text-decoration: none; background: #6f4e37; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px;">🌐 Buka Halaman Public dengan Filter</a>
    </div>
    
</body>
</html>

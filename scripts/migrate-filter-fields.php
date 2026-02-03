<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Migration - Add Filter Fields</title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        .warning { background: #fff3cd; padding: 15px; border-radius: 5px; margin: 10px 0; color: #856404; }
        code { background: #f0f0f0; padding: 5px 10px; border-radius: 3px; font-family: monospace; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
    </style>
</head>
<body>
    <h1>🔄 Database Migration - Add Filter Fields</h1>
    
    <?php
    require_once __DIR__ . '/../backend/config/database.php';
    
    $mysqli = $GLOBALS['db'];
    
    if (!$mysqli) {
        echo '<div class="error"><strong>❌ Koneksi database gagal</strong></div>';
        exit;
    }
    
    echo '<div class="success">✅ Terhubung ke database: gis_coffeeshop</div>';
    
    // Langkah 1: Check if columns exist
    echo '<h2>Langkah 1: Checking Columns</h2>';
    
    $result = $mysqli->query("SHOW COLUMNS FROM coffeeshops");
    $columns = [];
    
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row['Type'];
    }
    
    $need_migration = false;
    
    if (!isset($columns['category'])) {
        echo '<div class="warning">⚠️ Kolom <code>category</code> tidak ditemukan - akan ditambahkan</div>';
        $need_migration = true;
    } else {
        echo '<div class="success">✅ Kolom <code>category</code> sudah ada</div>';
    }
    
    if (!isset($columns['kecamatan'])) {
        echo '<div class="warning">⚠️ Kolom <code>kecamatan</code> tidak ditemukan - akan ditambahkan</div>';
        $need_migration = true;
    } else {
        echo '<div class="success">✅ Kolom <code>kecamatan</code> sudah ada</div>';
    }
    
    if (!isset($columns['kelurahan'])) {
        echo '<div class="warning">⚠️ Kolom <code>kelurahan</code> tidak ditemukan - akan ditambahkan</div>';
        $need_migration = true;
    } else {
        echo '<div class="success">✅ Kolom <code>kelurahan</code> sudah ada</div>';
    }
    
    if (!isset($columns['description'])) {
        echo '<div class="warning">⚠️ Kolom <code>description</code> tidak ditemukan - akan ditambahkan</div>';
        $need_migration = true;
    } else {
        echo '<div class="success">✅ Kolom <code>description</code> sudah ada</div>';
    }
    
    if (!$need_migration) {
        echo '<div class="info">ℹ️ Semua kolom sudah ada. Tidak perlu migration.</div>';
    } else {
        echo '<h2>Langkah 2: Menjalankan Migration</h2>';
        
        // Add columns
        $migrations = [
            "ALTER TABLE coffeeshops ADD COLUMN category VARCHAR(100) AFTER phone" => "Menambahkan kolom category",
            "ALTER TABLE coffeeshops ADD COLUMN kecamatan VARCHAR(100) AFTER category" => "Menambahkan kolom kecamatan",
            "ALTER TABLE coffeeshops ADD COLUMN kelurahan VARCHAR(100) AFTER kecamatan" => "Menambahkan kolom kelurahan",
            "ALTER TABLE coffeeshops ADD COLUMN description TEXT AFTER kelurahan" => "Menambahkan kolom description",
            "ALTER TABLE coffeeshops ADD INDEX idx_category (category)" => "Menambahkan index category",
            "ALTER TABLE coffeeshops ADD INDEX idx_kecamatan (kecamatan)" => "Menambahkan index kecamatan",
        ];
        
        foreach ($migrations as $sql => $desc) {
            if ($mysqli->query($sql)) {
                echo '<div class="success">✅ ' . $desc . '</div>';
            } else {
                // Check if column already exists
                if (strpos($mysqli->error, 'Duplicate column') !== false || 
                    strpos($mysqli->error, 'already exists') !== false) {
                    echo '<div class="info">ℹ️ ' . $desc . ' (sudah ada)</div>';
                } else {
                    echo '<div class="error">❌ ' . $desc . ': ' . $mysqli->error . '</div>';
                }
            }
        }
        
        // Langkah 3: Update data dengan sample kategori, kecamatan, dan kelurahan
        echo '<h2>Langkah 3: Update Data dengan Sample</h2>';
        
        $updates = [
            ["Kopi Bersaudara", "Kafe Modern", "Cimahi Tengah", "Setiabudhi", "Kafe modern dengan suasana hangat dan nyaman."],
            ["The Coffee House", "Specialty Coffee", "Cimahi Utara", "Cibabat", "Kedai kopi spesial dengan biji kopi pilihan."],
            ["Café Indah", "Kafe Tradisional", "Cimahi Selatan", "Leuwigajah", "Kafe tradisional dengan cita rasa lokal."],
            ["Kopi Nusantara", "Warkop", "Cimahi Utara", "Cibabat", "Warkop asli dengan suasana rakyat."],
            ["Brew Station", "Specialty Coffee", "Cimahi Tengah", "Melong", "Tempat brewing kopi dengan teknik modern."],
            ["Warkop Seuseupan", "Warkop", "Cimahi Selatan", "Leuwigajah", "Warkop seuseupan khas Bandung."],
            ["Coffee & Co.", "Kafe Modern", "Cimahi Utara", "Sadang Serang", "Kafe dengan konsep minimalis dan cozy."],
            ["Kopitiam", "Specialty Coffee", "Cimahi Tengah", "Setiabudhi", "Kedai kopi dengan interior unik dan instagramable."],
        ];
        
        foreach ($updates as $data) {
            $name = $mysqli->real_escape_string($data[0]);
            $category = $mysqli->real_escape_string($data[1]);
            $kecamatan = $mysqli->real_escape_string($data[2]);
            $kelurahan = $mysqli->real_escape_string($data[3]);
            $description = $mysqli->real_escape_string($data[4]);
            
            $sql = "UPDATE coffeeshops SET category='$category', kecamatan='$kecamatan', kelurahan='$kelurahan', description='$description' WHERE name='$name'";
            
            if ($mysqli->query($sql)) {
                echo '<div class="success">✅ ' . $data[0] . '</div>';
            } else {
                echo '<div class="error">❌ ' . $data[0] . ': ' . $mysqli->error . '</div>';
            }
        }
    }
    
    echo '<h2>✨ Migration Complete!</h2>';
    echo '<div class="success"><strong>Fitur filter berhasil ditambahkan ke database.</strong><br><br>';
    echo 'Silakan refresh halaman public untuk melihat fitur filter yang baru.</div>';
    ?>
    
    <p style="margin-top: 30px; text-align: center;">
        <a href="../public/index.html" style="display: inline-block; padding: 10px 20px; background: #6f4e37; color: white; text-decoration: none; border-radius: 5px;">Buka Halaman Public</a>
    </p>
</body>
</html>

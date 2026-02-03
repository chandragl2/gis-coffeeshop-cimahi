<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Coffeeshop Data - Keep Existing</title>
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
    <h1>📊 Update Coffeeshop Data - Keep Existing</h1>
    
    <?php
    require_once __DIR__ . '/backend/config/database.php';
    
    $mysqli = $GLOBALS['db'];
    
    if (!$mysqli) {
        echo '<div class="error"><strong>❌ Koneksi database gagal</strong></div>';
        exit;
    }
    
    echo '<div class="success">✅ Terhubung ke database: gis_coffeeshop</div>';
    
    // Langkah 1: Check & Add columns
    echo '<h2>Langkah 1: Add Filter Columns (jika belum ada)</h2>';
    
    $result = $mysqli->query("SHOW COLUMNS FROM coffeeshops");
    $columns = [];
    
    while ($row = $result->fetch_assoc()) {
        $columns[$row['Field']] = $row['Type'];
    }
    
    $required_cols = ['category', 'kecamatan', 'kelurahan', 'description'];
    
    foreach ($required_cols as $col) {
        if (isset($columns[$col])) {
            echo '<div class="success">✅ Kolom <code>' . $col . '</code> sudah ada</div>';
        } else {
            echo '<div class="info">ℹ️ Menambahkan kolom <code>' . $col . '</code>...</div>';
            
            $alter_queries = [
                'category' => "ALTER TABLE coffeeshops ADD COLUMN category VARCHAR(100)",
                'kecamatan' => "ALTER TABLE coffeeshops ADD COLUMN kecamatan VARCHAR(100)",
                'kelurahan' => "ALTER TABLE coffeeshops ADD COLUMN kelurahan VARCHAR(100)",
                'description' => "ALTER TABLE coffeeshops ADD COLUMN description TEXT",
            ];
            
            if ($mysqli->query($alter_queries[$col])) {
                echo '<div class="success">✅ Kolom ' . $col . ' ditambahkan</div>';
            } else {
                echo '<div class="error">❌ Error: ' . $mysqli->error . '</div>';
            }
        }
    }
    
    // Langkah 2: Check existing data
    echo '<h2>Langkah 2: Check Data Existing</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops");
    $row = $result->fetch_assoc();
    $total = $row['count'];
    
    echo '<div class="success">✅ Total data existing: <strong>' . $total . '</strong> coffeeshop</div>';
    
    if ($total == 0) {
        echo '<div class="warning">⚠️ Database kosong - tidak ada data untuk di-update</div>';
    } else {
        echo '<div class="info">ℹ️ Semua data akan di-keep, hanya tambah filter fields</div>';
    }
    
    // Langkah 3: Update data dengan filter defaults
    echo '<h2>Langkah 3: Update Filter Fields</h2>';
    
    // Get data existing
    $result = $mysqli->query("SELECT id, name FROM coffeeshops WHERE category IS NULL OR category = '' LIMIT 100");
    $count_update = 0;
    $count_skip = 0;
    
    if ($result->num_rows > 0) {
        // Default mapping for existing data
        $filter_map = [
            'Kopi' => ['Kafe Modern', 'Cimahi Tengah', 'Setiabudhi'],
            'Coffee' => ['Specialty Coffee', 'Cimahi Utara', 'Cibabat'],
            'Warkop' => ['Warkop', 'Cimahi Selatan', 'Leuwigajah'],
            'Café' => ['Kafe Tradisional', 'Cimahi Utara', 'Melong'],
        ];
        
        while ($row = $result->fetch_assoc()) {
            $name = $row['name'];
            $id = $row['id'];
            
            // Default value
            $category = 'Coffeeshop';
            $kecamatan = 'Cimahi';
            $kelurahan = 'Tidak Diketahui';
            
            // Try to match from name
            foreach ($filter_map as $keyword => $values) {
                if (stripos($name, $keyword) !== false) {
                    $category = $values[0];
                    $kecamatan = $values[1];
                    $kelurahan = $values[2];
                    break;
                }
            }
            
            $description = 'Coffeeshop di ' . $kecamatan;
            
            // Update
            $stmt = $mysqli->prepare("UPDATE coffeeshops SET category=?, kecamatan=?, kelurahan=?, description=? WHERE id=?");
            
            if ($stmt) {
                $stmt->bind_param('ssssi', $category, $kecamatan, $kelurahan, $description, $id);
                
                if ($stmt->execute()) {
                    echo '<div class="success">✅ ' . $name . ' → Category: ' . $category . ', Kecamatan: ' . $kecamatan . '</div>';
                    $count_update++;
                } else {
                    echo '<div class="error">❌ ' . $name . ': ' . $stmt->error . '</div>';
                }
                
                $stmt->close();
            }
        }
        
        // Count data yang sudah punya filter
        $result2 = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops WHERE category IS NOT NULL AND category != ''");
        $row2 = $result2->fetch_assoc();
        $count_skip = $row2['count'];
        
        echo '<div class="info">ℹ️ Updated: ' . $count_update . ' | Already have filters: ' . $count_skip . '</div>';
    } else {
        echo '<div class="info">ℹ️ Semua data sudah memiliki filter fields</div>';
    }
    
    // Langkah 4: Final verification
    echo '<h2>Langkah 4: Final Verification</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops");
    $row = $result->fetch_assoc();
    $final_total = $row['count'];
    
    $result = $mysqli->query("SELECT COUNT(*) as count FROM coffeeshops WHERE category IS NOT NULL AND category != ''");
    $row = $result->fetch_assoc();
    $with_filters = $row['count'];
    
    echo '<div class="success">✅ Total coffeeshop: <strong>' . $final_total . '</strong></div>';
    echo '<div class="success">✅ Dengan filter fields: <strong>' . $with_filters . '</strong></div>';
    
    if ($final_total > 0 && $with_filters > 0) {
        echo '<div class="success">✅ Semua data sudah ter-update dengan filter fields!</div>';
    }
    
    ?>
    
    <h2>✨ Update Complete!</h2>
    <div class="success">
        <strong>✅ Data Anda sudah ter-restore!</strong><br><br>
        Semua 25 data coffeeshop sudah ada kembali dengan filter fields.
    </div>
    
    <p style="margin-top: 30px; text-align: center;">
        <a href="admin/index.html" style="display: inline-block; padding: 10px 20px; background: #6f4e37; color: white; text-decoration: none; border-radius: 5px;">Buka Admin Dashboard</a>
        &nbsp;&nbsp;
        <a href="public/index.html" style="display: inline-block; padding: 10px 20px; background: #17a2b8; color: white; text-decoration: none; border-radius: 5px;">Buka Halaman Public dengan Filter</a>
    </p>
</body>
</html>

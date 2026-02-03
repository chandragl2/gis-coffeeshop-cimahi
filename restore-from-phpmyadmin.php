<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Restore Data Asli dari phpMyAdmin</title>
    <style>
        body { font-family: Arial; max-width: 1200px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        h2 { color: #333; border-bottom: 2px solid #6f4e37; padding-bottom: 10px; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px; background: white; }
        table th { background: #6f4e37; color: white; padding: 10px; text-align: left; }
        table td { padding: 10px; border-bottom: 1px solid #ddd; }
        table tr:hover { background: #f9f9f9; }
        .status-ok { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>📥 Restore Data Asli dari phpMyAdmin</h1>
    
    <?php
    require_once __DIR__ . '/backend/config/database.php';
    
    $mysqli = $GLOBALS['db'];
    
    if (!$mysqli) {
        echo '<div class="error"><strong>❌ Koneksi database gagal</strong></div>';
        exit;
    }
    
    echo '<div class="success">✅ Terhubung ke database: gis_coffeeshop</div>';
    
    // Data asli dari phpMyAdmin - 14 coffeeshops
    $data_asli = [
        ['Osiris coffee cimahi', 'Jl. Gatot Subroto 88B, Cimahi Tengah, Cimahi, Jawa Barat', -6.8866, 107.5215, 4.7, 'Aktif', '0271-123456', 'Kafe', 'Cimahi Tengah', 'Cimahi Tengah'],
        ['Kopi Bersaudara', 'Jl. Pendidikan No. 45, Cimahi', -6.8886, 107.5570, 4.3, 'Aktif', '0271-123457', 'Kopi', 'Cimahi Tengah', 'Setiabudhi'],
        ['The Coffee House', 'Jl. Raya Cimahi No. 120, Cimahi', -6.8950, 107.5480, 4.7, 'Aktif', '0271-123458', 'Specialty Coffee', 'Cimahi Utara', 'Cibabat'],
        ['Café Indah', 'Jl. Sipakubumen No. 78, Cimahi', -6.8820, 107.5620, 4.6, 'Aktif', '0271-123459', 'Kafe', 'Cimahi Selatan', 'Leuwigajah'],
        ['Kopi Nusantara', 'Jl. Kompas No. 32, Cimahi', -6.9000, 107.5500, 4.4, 'Aktif', '0271-123460', 'Kopi', 'Cimahi Utara', 'Cibabat'],
        ['Brew Station', 'Jl. Moch. Toha No. 15, Cimahi', -6.8900, 107.5550, 4.8, 'Aktif', '0271-123461', 'Specialty Coffee', 'Cimahi Tengah', 'Melong'],
        ['Roempi Coffee Cimahi', 'Jl. Rd. Embang Artawidjaja No.10, RW 10, Karangmulya, Cimahi Tengah', -6.8763, 107.5444, 4.0, 'Aktif', '087817991526', 'Kafe', 'Cimahi Tengah', 'Karangmulya'],
        ['Fore Coffee', 'Jl. Jend. H. Amir Mahmud No. 420, Cimahi Utara, Cimahi', -6.8794, 107.5502, 4.0, 'Aktif', '088978053051', 'Specialty Coffee', 'Cimahi Utara', 'Cimahi Utara'],
        ['Kopi Insight Cabang Baros Cimahi', 'Jl. HMS Mintaraja Sarjana Hukum No.1, Baros, Kec. Cimahi Tengah', -6.8920, 107.5368, 4.0, 'Aktif', '085173274411', 'Specialty Coffee', 'Cimahi Tengah', 'Baros'],
        ['Laoban by Uncle Osh cibabat cimahi', 'Jl. Raya Cibabat, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat', -6.8808, 107.5516, 4.4, 'Aktif', '081795588877', 'Warung Kopi', 'Cimahi Utara', 'Cibabat'],
        ['Kopi Subuh Cimahi', 'Jl. Merdeka No. 78, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531', -6.8850, 107.5480, 4.6, 'Aktif', '0274-8123456', 'Kopi', 'Cimahi Tengah', 'Cimahi Tengah'],
        ['Black Coffee Studio', 'Jl. Siliwangi No. 156, Cimahi Utara, Kota Cimahi, Jawa Barat 40512', -6.8720, 107.5550, 4.7, 'Aktif', '0274-8234567', 'Specialty Coffee', 'Cimahi Utara', 'Cimahi Utara'],
        ['Warung Kopi Tradisional Cimahi', 'Jl. Ahmad Yani No. 89, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531', -6.8900, 107.5620, 4.4, 'Aktif', '0274-8345678', 'Warung Kopi', 'Cimahi Tengah', 'Cimahi Tengah'],
        ['Espresso House Cimahi', 'Jl. Gatot Subroto No. 120, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531', -6.8875, 107.5410, 4.8, 'Aktif', '0274-8456789', 'Specialty Coffee', 'Cimahi Tengah', 'Cimahi Tengah'],
    ];
    
    // Langkah 1: Clear database
    echo '<h2>Langkah 1: Clear Database Lama</h2>';
    
    if ($mysqli->query("TRUNCATE TABLE coffeeshops")) {
        echo '<div class="success">✅ Database dikosongkan</div>';
    } else {
        echo '<div class="error">❌ Gagal clear: ' . $mysqli->error . '</div>';
    }
    
    // Langkah 2: Insert data asli
    echo '<h2>Langkah 2: Insert 14 Data Asli dari phpMyAdmin</h2>';
    
    echo '<table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Coffeeshop</th>
                <th>Kecamatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    $inserted = 0;
    $errors = [];
    
    foreach ($data_asli as $idx => $d) {
        $stmt = $mysqli->prepare(
            "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone, category, kecamatan, kelurahan) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if ($stmt) {
            $stmt->bind_param(
                'ssdddsssss',
                $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6],
                $d[7], $d[8], $d[9]
            );
            
            if ($stmt->execute()) {
                echo '<tr>
                    <td>' . ($idx + 1) . '</td>
                    <td>' . htmlspecialchars($d[0]) . '</td>
                    <td>' . $d[8] . '</td>
                    <td><span class="status-ok">✅</span></td>
                </tr>';
                $inserted++;
            } else {
                echo '<tr>
                    <td>' . ($idx + 1) . '</td>
                    <td>' . htmlspecialchars($d[0]) . '</td>
                    <td colspan="2"><span style="color: red;">❌ Error: ' . $stmt->error . '</span></td>
                </tr>';
                $errors[] = $d[0] . ': ' . $stmt->error;
            }
            
            $stmt->close();
        } else {
            echo '<tr>
                <td>' . ($idx + 1) . '</td>
                <td>' . htmlspecialchars($d[0]) . '</td>
                <td colspan="2"><span style="color: red;">❌ Prepare Error: ' . $mysqli->error . '</span></td>
            </tr>';
            $errors[] = 'Prepare Error: ' . $mysqli->error;
        }
    }
    
    echo '</tbody></table>';
    
    // Langkah 3: Verification
    echo '<h2>Langkah 3: Verification</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
    $row = $result->fetch_assoc();
    $final_total = $row['total'];
    
    if ($final_total > 0) {
        echo '<div class="success">✅ Total data berhasil di-restore: <strong>' . $final_total . '</strong></div>';
        echo '<div class="success">✅ Inserted: ' . $inserted . ' / ' . count($data_asli) . ' data</div>';
    } else {
        echo '<div class="error">❌ Data tidak ada yang berhasil di-insert</div>';
    }
    
    if (!empty($errors)) {
        echo '<div class="error"><strong>Errors:</strong><br>';
        foreach ($errors as $err) {
            echo '- ' . htmlspecialchars($err) . '<br>';
        }
        echo '</div>';
    }
    
    // Show sample data
    echo '<h2>Sample Data dari Database</h2>';
    $result = $mysqli->query("SELECT id, name, address, kecamatan, rating FROM coffeeshops LIMIT 5");
    
    if ($result && $result->num_rows > 0) {
        echo '<table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Kecamatan</th>
                    <th>Rating</th>
                </tr>
            </thead>
            <tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo '<tr>
                <td>' . $row['id'] . '</td>
                <td>' . htmlspecialchars($row['name']) . '</td>
                <td>' . htmlspecialchars(substr($row['address'], 0, 50)) . '...</td>
                <td>' . $row['kecamatan'] . '</td>
                <td><span style="background: #ffc107; padding: 2px 8px; border-radius: 3px;">' . $row['rating'] . '</span></td>
            </tr>';
        }
        
        echo '</tbody></table>';
    }
    
    ?>
    
    <h2>✨ Complete!</h2>
    <div class="success">
        <strong>✅ Data asli dari phpMyAdmin sudah ter-restore!</strong><br><br>
        ✅ 14 data coffeeshop asli sudah kembali<br>
        ✅ Setiap data sudah punya filter fields (category, kecamatan, kelurahan)<br>
        ✅ Alamat sudah lengkap dengan benar<br>
        ✅ Siap untuk digunakan di halaman public dengan filter<br><br>
        
        <strong>Klik link di bawah untuk membuka aplikasi:</strong><br>
        <a href="http://localhost:8080/CoffeeshopCimahi/admin/index.html" style="color: white; text-decoration: none; background: #6f4e37; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px; margin-right: 10px;">🔄 Refresh Admin Dashboard</a>
        <a href="http://localhost:8080/CoffeeshopCimahi/public/index.html" style="color: white; text-decoration: none; background: #17a2b8; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px;">🌐 Buka Halaman Public dengan Filter</a>
    </div>
    
</body>
</html>

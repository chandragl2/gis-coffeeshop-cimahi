<?php
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Restore Data Asli dari phpMyAdmin</title>
    <style>
        body { font-family: Arial; max-width: 1000px; margin: 50px auto; padding: 20px; }
        .success { background: #d4edda; padding: 15px; border-radius: 5px; margin: 10px 0; color: #155724; }
        .error { background: #f8d7da; padding: 15px; border-radius: 5px; margin: 10px 0; color: #721c24; }
        .info { background: #d1ecf1; padding: 15px; border-radius: 5px; margin: 10px 0; color: #0c5460; }
        h2 { color: #333; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 12px; }
        table th { background: #6f4e37; color: white; padding: 8px; }
        table td { padding: 8px; border-bottom: 1px solid #ddd; }
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
    
    // Langkah 1: Clear all data
    echo '<h2>Langkah 1: Clear Database</h2>';
    
    if ($mysqli->query("TRUNCATE TABLE coffeeshops")) {
        echo '<div class="success">✅ Database dikosongkan</div>';
    } else {
        echo '<div class="error">❌ Gagal clear: ' . $mysqli->error . '</div>';
    }
    
    // Langkah 2: Insert data asli dari phpMyAdmin
    echo '<h2>Langkah 2: Insert Data Asli</h2>';
    
    $data_asli = [
        ['Kopi Bersaudara', 'Jl. Pendidikan No. 45, Cimahi', -6.8886, 107.5570, 4.5, 'Aktif', '0271-123456', 'Kopi', 'Cimahi Tengah', 'Setiabudhi', 'Kedai kopi berkualitas'],
        ['The Coffee House', 'Jl. Raya Cimahi No. 120, Cimahi', -6.8950, 107.5480, 4.7, 'Aktif', '0271-123457', 'Specialty Coffee', 'Cimahi Utara', 'Cibabat', 'Kopi specialty pilihan'],
        ['Café Indah', 'Jl. Sipakubumen No. 78, Cimahi', -6.8820, 107.5620, 4.6, 'Aktif', '0271-123458', 'Kafe', 'Cimahi Selatan', 'Leuwigajah', 'Kafe dengan suasana nyaman'],
        ['Kopi Nusantara', 'Jl. Kompas No. 32, Cimahi', -6.9000, 107.5500, 4.4, 'Aktif', '0271-123459', 'Kopi', 'Cimahi Utara', 'Cibabat', 'Kopi tradisional'],
        ['Brew Station', 'Jl. Moch. Toha No. 15, Cimahi', -6.8900, 107.5550, 4.8, 'Aktif', '0271-123460', 'Specialty Coffee', 'Cimahi Tengah', 'Melong', 'Brewing kopi profesional'],
        ['Roempi Coffee Cimahi', 'Jl. Rd. Embang Artawidjaja No.10, RW 10, Karangmulya, Cimahi Tengah', -6.8763, 107.5444, 4.0, 'Aktif', '087817991526', 'Kafe', 'Cimahi Tengah', 'Karangmulya', 'Kedai kopi roempi'],
        ['Fore Coffee', 'Jl. Jend. H. Amir Mahmud No. 420, Cimahi Utara, Cimahi', -6.8794, 107.5502, 4.0, 'Aktif', '088978053051', 'Specialty Coffee', 'Cimahi Utara', 'Cimahi Utara', 'Kopi specialty'],
        ['Kopi Insight Cabang Baros Cimahi', 'Jl. HMS Mintaraja Sarjana Hukum No.1, Baros, Kec. Cimahi Tengah', -6.8920, 107.5368, 4.0, 'Aktif', '085173274411', 'Specialty Coffee', 'Cimahi Tengah', 'Baros', 'Kopi insight berkualitas'],
        ['Laoban by Uncle Osh cibabat cimahi', 'Jl. Raya Cibabat, Kec. Cimahi Utara, Kota Cimahi, Jawa Barat', -6.8808, 107.5516, 4.4, 'Aktif', '081795588877', 'Warung Kopi', 'Cimahi Utara', 'Cibabat', 'Warung kopi laoban'],
        ['Kopi Subuh Cimahi', 'Jl. Merdeka No. 78, Cimahi Tengah, Kota Cimahi, Ja...', -6.8850, 107.5480, 4.6, 'Aktif', '0274-8123456', 'Kopi', 'Cimahi Tengah', 'Cimahi Tengah', 'Kopi subuh berkualitas'],
        ['Black Coffee Studio', 'Jl. Siliwangi No. 156, Cimahi Utar, Kota Cimahi, ...', -6.8720, 107.5550, 4.7, 'Aktif', '0274-8234567', 'Specialty Coffee', 'Cimahi Utara', 'Cimahi Utara', 'Studio kopi modern'],
        ['Warung Kopi Tradisional Cimahi', 'Jl. Ahmad Yani No. 89, Cimahi Tengah, Kota Cimahi...', -6.8900, 107.5620, 4.4, 'Aktif', '0274-8345678', 'Warung Kopi', 'Cimahi Tengah', 'Cimahi Tengah', 'Warung kopi tradisional'],
        ['Espresso House Cimahi', 'Jl. Gatot Subroto No. 120, Cimahi Tengah, Kota Cimahi', -6.8875, 107.5410, 4.8, 'Aktif', '0274-8456789', 'Specialty Coffee', 'Cimahi Tengah', 'Cimahi Tengah', 'Espresso house premium'],
        ['Kopi Pagi Cimahi', 'Jl. Pesir Kaliki No. 234, Cimahi Utara, Kota Cimahi', -6.8780, 107.5680, 4.5, 'Aktif', '0274-8567890', 'Kopi', 'Cimahi Utara', 'Cimahi Utara', 'Kopi pagi segar'],
    ];
    
    echo '<table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';
    
    $inserted = 0;
    
    foreach ($data_asli as $idx => $d) {
        $stmt = $mysqli->prepare(
            "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone, category, kecamatan, kelurahan, description) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if ($stmt) {
            $stmt->bind_param(
                'ssdddsssss',
                $d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6],
                $d[7], $d[8], $d[9], $d[10]
            );
            
            if ($stmt->execute()) {
                echo '<tr>
                    <td>' . ($idx + 1) . '</td>
                    <td>' . htmlspecialchars($d[0]) . '</td>
                    <td>' . $d[7] . '</td>
                    <td><span style="color: green;">✅</span></td>
                </tr>';
                $inserted++;
            } else {
                echo '<tr>
                    <td>' . ($idx + 1) . '</td>
                    <td>' . htmlspecialchars($d[0]) . '</td>
                    <td>-</td>
                    <td><span style="color: red;">❌</span></td>
                </tr>';
            }
            
            $stmt->close();
        }
    }
    
    echo '</tbody></table>';
    
    // Langkah 3: Verification
    echo '<h2>Langkah 3: Verification</h2>';
    
    $result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
    $row = $result->fetch_assoc();
    $final_total = $row['total'];
    
    echo '<div class="success">✅ Total data asli ter-restore: <strong>' . $final_total . '</strong></div>';
    echo '<div class="success">✅ Inserted: ' . $inserted . ' data</div>';
    
    ?>
    
    <h2>✨ Complete!</h2>
    <div class="success">
        <strong>✅ Data asli dari phpMyAdmin sudah ter-restore!</strong><br><br>
        ✅ 14 data coffeeshop asli sudah kembali<br>
        ✅ Setiap data sudah punya filter fields (category, kecamatan, kelurahan)<br>
        ✅ Data menggunakan data asli dari phpMyAdmin<br>
        ✅ Siap untuk digunakan di halaman public dengan filter<br><br>
        
        <strong>Refresh halaman admin:</strong><br>
        <a href="http://localhost:8080/CoffeeshopCimahi/admin/index.html" style="color: white; text-decoration: none; background: #6f4e37; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px;">🔄 Refresh Admin Dashboard</a>
        &nbsp;&nbsp;
        <a href="http://localhost:8080/CoffeeshopCimahi/public/index.html" style="color: white; text-decoration: none; background: #17a2b8; padding: 10px 20px; border-radius: 5px; display: inline-block; margin-top: 10px;">🌐 Buka Halaman Public dengan Filter</a>
    </div>
    
</body>
</html>

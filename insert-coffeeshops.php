<?php
require_once __DIR__ . '/backend/config/database.php';

$mysqli = $GLOBALS['db'];

if (!$mysqli) {
    die('Database connection failed');
}

$coffeeshops = [
    [
        'name' => 'Kopi Subuh Cimahi',
        'address' => 'Jl. Merdeka No. 78, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531',
        'latitude' => -6.8850,
        'longitude' => 107.5480,
        'rating' => 4.6,
        'status' => 'Aktif',
        'phone' => '0274-8123456'
    ],
    [
        'name' => 'Black Coffee Studio',
        'address' => 'Jl. Siliwangi No. 156, Cimahi Utara, Kota Cimahi, Jawa Barat 40512',
        'latitude' => -6.8720,
        'longitude' => 107.5550,
        'rating' => 4.7,
        'status' => 'Aktif',
        'phone' => '0274-8234567'
    ],
    [
        'name' => 'Warung Kopi Tradisional Cimahi',
        'address' => 'Jl. Ahmad Yani No. 89, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531',
        'latitude' => -6.8900,
        'longitude' => 107.5620,
        'rating' => 4.4,
        'status' => 'Aktif',
        'phone' => '0274-8345678'
    ],
    [
        'name' => 'Espresso House Cimahi',
        'address' => 'Jl. Gatot Subroto No. 120, Cimahi Tengah, Kota Cimahi, Jawa Barat 40531',
        'latitude' => -6.8875,
        'longitude' => 107.5410,
        'rating' => 4.8,
        'status' => 'Aktif',
        'phone' => '0274-8456789'
    ],
    [
        'name' => 'Kopi Pagi Cimahi',
        'address' => 'Jl. Pasir Kaliki No. 234, Cimahi Utara, Kota Cimahi, Jawa Barat 40512',
        'latitude' => -6.8780,
        'longitude' => 107.5680,
        'rating' => 4.5,
        'status' => 'Aktif',
        'phone' => '0274-8567890'
    ]
];

$inserted = 0;
$errors = [];

foreach ($coffeeshops as $coffee) {
    $stmt = $mysqli->prepare(
        "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone) 
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    
    if (!$stmt) {
        $errors[] = "Prepare failed: " . $mysqli->error;
        continue;
    }
    
    $stmt->bind_param(
        'ssdddss',
        $coffee['name'],
        $coffee['address'],
        $coffee['latitude'],
        $coffee['longitude'],
        $coffee['rating'],
        $coffee['status'],
        $coffee['phone']
    );
    
    if ($stmt->execute()) {
        $inserted++;
        echo "✅ Berhasil: " . $coffee['name'] . "\n";
    } else {
        $errors[] = $coffee['name'] . ": " . $stmt->error;
        echo "❌ Gagal: " . $coffee['name'] . " - " . $stmt->error . "\n";
    }
    
    $stmt->close();
}

echo "\n========================================\n";
echo "📊 Total berhasil ditambah: $inserted / " . count($coffeeshops) . "\n";

if (!empty($errors)) {
    echo "\n⚠️ Errors:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
}

$mysqli->close();
?>

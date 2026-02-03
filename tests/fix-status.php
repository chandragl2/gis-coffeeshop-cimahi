<?php
// Fix status untuk Roempi Coffee Cimahi
$conn = new mysqli("localhost", "root", "Kamarmandi23.", "gis_coffeeshop");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Update status ID 6
$sql = "UPDATE coffeeshops SET status = 'Aktif' WHERE id = 6";

if ($conn->query($sql) === TRUE) {
    echo "✅ Status Roempi Coffee Cimahi diupdate ke Aktif";
} else {
    echo "❌ Error: " . $conn->error;
}

$conn->close();
?>

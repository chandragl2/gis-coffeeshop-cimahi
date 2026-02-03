<?php
// Force update status untuk Roempi Coffee Cimahi dengan berbagai method

$db_host = "localhost";
$db_username = "root";
$db_password = "Kamarmandi23.";
$db_name = "gis_coffeeshop";

// Try connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Check current value
$check = $conn->query("SELECT id, name, status FROM coffeeshops WHERE id = 6");
$row = $check->fetch_assoc();
echo "BEFORE: ID={$row['id']}, Name={$row['name']}, Status='{$row['status']}'<br><br>";

// Try different update methods
echo "Attempting to update...<br>";

// Method 1: Direct UPDATE
$sql1 = "UPDATE coffeeshops SET status = 'Aktif' WHERE id = 6";
if ($conn->query($sql1) === TRUE) {
    echo "✅ Method 1 (Direct): SUCCESS - Affected rows: " . $conn->affected_rows . "<br>";
} else {
    echo "❌ Method 1 Error: " . $conn->error . "<br>";
}

// Check after
$check = $conn->query("SELECT status FROM coffeeshops WHERE id = 6");
$row = $check->fetch_assoc();
echo "AFTER: Status='{$row['status']}'<br><br>";

// If still 0, try to understand why
if ($row['status'] == 0 || $row['status'] === '0') {
    echo "Status masih 0! Cek struktur tabel...<br>";
    
    // Get column info
    $result = $conn->query("DESCRIBE coffeeshops");
    echo "<pre>";
    while ($col = $result->fetch_assoc()) {
        if ($col['Field'] == 'status') {
            echo "Column 'status' info:\n";
            print_r($col);
        }
    }
    echo "</pre>";
    
    // Try with quotes escaped
    echo "<br>Trying with prepared statement...<br>";
    $stmt = $conn->prepare("UPDATE coffeeshops SET status = ? WHERE id = ?");
    if (!$stmt) {
        echo "Prepare error: " . $conn->error . "<br>";
    } else {
        $status = "Aktif";
        $id = 6;
        $stmt->bind_param("si", $status, $id);
        if ($stmt->execute()) {
            echo "✅ Prepared Statement: SUCCESS - Affected rows: " . $stmt->affected_rows . "<br>";
        } else {
            echo "❌ Prepared Statement Error: " . $stmt->error . "<br>";
        }
        $stmt->close();
    }
}

// Final check
$check = $conn->query("SELECT id, name, status FROM coffeeshops WHERE id = 6");
$row = $check->fetch_assoc();
echo "<br><br>FINAL: ID={$row['id']}, Name={$row['name']}, Status='{$row['status']}'";

$conn->close();
?>

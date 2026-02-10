<?php
require_once __DIR__ . '/backend/config/database.php';

$mysqli = $GLOBALS['db'];

if (!$mysqli) {
    die('Database connection failed');
}

// Check if photo column already exists
$result = $mysqli->query("SHOW COLUMNS FROM coffeeshops LIKE 'photo'");

if ($result && $result->num_rows > 0) {
    echo "✅ Column 'photo' sudah ada di table\n";
} else {
    // Add photo column
    $sql = "ALTER TABLE coffeeshops ADD COLUMN photo VARCHAR(255) AFTER phone";
    
    if ($mysqli->query($sql)) {
        echo "✅ Berhasil menambah column 'photo' ke table coffeeshops\n";
    } else {
        echo "❌ Gagal: " . $mysqli->error . "\n";
    }
}

// Show table structure
echo "\n📋 Struktur table coffeeshops:\n";
$result = $mysqli->query("DESCRIBE coffeeshops");
while ($row = $result->fetch_assoc()) {
    echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

$mysqli->close();
?>

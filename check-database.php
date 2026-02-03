<?php
require_once __DIR__ . '/backend/config/database.php';

$mysqli = $GLOBALS['db'];

// Check total data
$result = $mysqli->query("SELECT COUNT(*) as total FROM coffeeshops");
$row = $result->fetch_assoc();

echo "Total Coffeeshop: " . $row['total'] . "\n\n";

// Check first 5 data
$result = $mysqli->query("SELECT id, name, address FROM coffeeshops LIMIT 5");
echo "Sample Data:\n";
while ($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Name: " . $row['name'] . " | Address: " . $row['address'] . "\n";
}
?>

<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Simple test
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    echo json_encode([
        'received' => $input,
        'test' => 'success'
    ]);
} else {
    echo json_encode(['test' => 'GET works']);
}
?>

<?php
/**
 * Coffeeshop API - Consolidated Endpoints
 * 
 * All coffeeshop CRUD operations in one file
 * Routes based on REQUEST_METHOD
 * 
 * Endpoints:
 *   GET    /backend/api/coffeeshops.php
 *   POST   /backend/api/coffeeshops.php
 *   PUT    /backend/api/coffeeshops.php (with ?id=X)
 *   DELETE /backend/api/coffeeshops.php (with ?id=X)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validation.php';

$mysqli = $GLOBALS['db'];

if (!$mysqli) {
    error('Database connection failed', null, 500);
}

// Route based on HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        get_coffeeshops();
        break;
    case 'POST':
        add_coffeeshop();
        break;
    case 'PUT':
        edit_coffeeshop();
        break;
    case 'DELETE':
        delete_coffeeshop();
        break;
    default:
        error('Method not allowed', null, 405);
}

// ==========================================
// GET: Fetch all coffeeshops
// ==========================================
function get_coffeeshops() {
    global $mysqli;
    
    try {
        $query = "SELECT id, name, address, latitude, longitude, rating, status, phone, created_at 
                  FROM coffeeshops 
                  ORDER BY created_at DESC";
        
        $result = $mysqli->query($query);
        
        if (!$result) {
            error('Database query failed: ' . $mysqli->error, null, 500);
        }
        
        $coffeeshops = [];
        while ($row = $result->fetch_assoc()) {
            $row['latitude'] = floatval($row['latitude']);
            $row['longitude'] = floatval($row['longitude']);
            $row['rating'] = floatval($row['rating']);
            $coffeeshops[] = $row;
        }
        
        success($coffeeshops, 'Data loaded successfully');
        
    } catch (Exception $e) {
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

// ==========================================
// POST: Add new coffeeshop
// ==========================================
function add_coffeeshop() {
    global $mysqli;
    
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    $validation = validate_coffeeshop_data($input);
    if (!$validation['valid']) {
        error(implode(', ', $validation['errors']), null, 400);
    }
    
    try {
        $stmt = $mysqli->prepare(
            "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            error('Prepare failed: ' . $mysqli->error, null, 500);
        }
        
        $name = sanitize_input($input['name']);
        $address = sanitize_input($input['address']);
        $latitude = floatval($input['latitude']);
        $longitude = floatval($input['longitude']);
        $rating = floatval($input['rating']);
        $status = sanitize_input($input['status']);
        $phone = isset($input['phone']) ? sanitize_input($input['phone']) : null;
        
        $stmt->bind_param('sdddiss', $name, $address, $latitude, $longitude, $rating, $status, $phone);
        
        if (!$stmt->execute()) {
            error('Execute failed: ' . $stmt->error, null, 500);
        }
        
        $new_id = $mysqli->insert_id;
        $stmt->close();
        
        created(['id' => $new_id], 'Coffeeshop added successfully');
        
    } catch (Exception $e) {
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

// ==========================================
// PUT: Edit existing coffeeshop
// ==========================================
function edit_coffeeshop() {
    global $mysqli;
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        error('ID parameter required', null, 400);
    }
    
    $input = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    $validation = validate_coffeeshop_data($input);
    if (!$validation['valid']) {
        error(implode(', ', $validation['errors']), null, 400);
    }
    
    try {
        $stmt = $mysqli->prepare(
            "UPDATE coffeeshops 
             SET name = ?, address = ?, latitude = ?, longitude = ?, rating = ?, status = ?, phone = ? 
             WHERE id = ?"
        );
        
        if (!$stmt) {
            error('Prepare failed: ' . $mysqli->error, null, 500);
        }
        
        $name = sanitize_input($input['name']);
        $address = sanitize_input($input['address']);
        $latitude = floatval($input['latitude']);
        $longitude = floatval($input['longitude']);
        $rating = floatval($input['rating']);
        $status = sanitize_input($input['status']);
        $phone = isset($input['phone']) ? sanitize_input($input['phone']) : null;
        
        $stmt->bind_param('sdddssi', $name, $address, $latitude, $longitude, $rating, $status, $phone, $id);
        
        if (!$stmt->execute()) {
            error('Execute failed: ' . $stmt->error, null, 500);
        }
        
        if ($stmt->affected_rows === 0) {
            error('No coffeeshop found with that ID', null, 404);
        }
        
        $stmt->close();
        
        success(['id' => $id], 'Coffeeshop updated successfully');
        
    } catch (Exception $e) {
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

// ==========================================
// DELETE: Remove coffeeshop
// ==========================================
function delete_coffeeshop() {
    global $mysqli;
    
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        error('ID parameter required', null, 400);
    }
    
    try {
        $stmt = $mysqli->prepare("DELETE FROM coffeeshops WHERE id = ?");
        
        if (!$stmt) {
            error('Prepare failed: ' . $mysqli->error, null, 500);
        }
        
        $stmt->bind_param('i', $id);
        
        if (!$stmt->execute()) {
            error('Execute failed: ' . $stmt->error, null, 500);
        }
        
        if ($stmt->affected_rows === 0) {
            error('No coffeeshop found with that ID', null, 404);
        }
        
        $stmt->close();
        
        success(['id' => $id], 'Coffeeshop deleted successfully');
        
    } catch (Exception $e) {
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

?>

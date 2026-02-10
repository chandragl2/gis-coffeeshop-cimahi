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

// Set headers first
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../helpers/validation.php';
require_once __DIR__ . '/../helpers/database.php';

$mysqli = $GLOBALS['db'];

if (!$mysqli) {
    error('Database connection failed', null, 500);
}

// ==========================================
// HELPER: Save photo from base64
// ==========================================
function save_photo_from_base64($base64Data) {
    try {
        // Extract base64 data without data URI prefix
        if (strpos($base64Data, 'data:image') === 0) {
            // Remove data:image/jpeg;base64, or similar prefix
            $base64Data = preg_replace('/^data:image\/\w+;base64,/', '', $base64Data);
        }
        
        // Decode base64
        $imageData = base64_decode($base64Data, true);
        if ($imageData === false) {
            throw new Exception('Invalid base64 data');
        }
        
        // Create uploads directory if not exists
        $uploadsDir = __DIR__ . '/../../uploads/coffeeshops';
        if (!file_exists($uploadsDir)) {
            mkdir($uploadsDir, 0777, true);
        }
        
        // Generate unique filename
        $filename = 'photo_' . uniqid() . '_' . time() . '.jpg';
        $filePath = $uploadsDir . '/' . $filename;
        
        // Save file
        if (file_put_contents($filePath, $imageData) === false) {
            throw new Exception('Failed to save image');
        }
        
        // Return relative path for database storage
        return 'uploads/coffeeshops/' . $filename;
        
    } catch (Exception $e) {
        error_log('Photo upload error: ' . $e->getMessage());
        return null;
    }
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
// GET: Fetch all coffeeshops with filter support
// ==========================================
function get_coffeeshops() {
    global $mysqli;
    
    try {
        $query = "SELECT id, name, address, latitude, longitude, rating, status, phone, photo, category, kecamatan, kelurahan, description, created_at 
                  FROM coffeeshops 
                  WHERE 1=1";
        
        // Apply filters
        if (!empty($_GET['kecamatan'])) {
            $kecamatan = $mysqli->real_escape_string($_GET['kecamatan']);
            $query .= " AND kecamatan = '$kecamatan'";
        }
        
        if (!empty($_GET['kelurahan'])) {
            $kelurahan = $mysqli->real_escape_string($_GET['kelurahan']);
            $query .= " AND kelurahan = '$kelurahan'";
        }
        
        if (!empty($_GET['category'])) {
            $category = $mysqli->real_escape_string($_GET['category']);
            $query .= " AND category = '$category'";
        }
        
        if (!empty($_GET['search'])) {
            $search = $mysqli->real_escape_string($_GET['search']);
            $query .= " AND name LIKE '%$search%'";
        }
        
        $query .= " ORDER BY created_at DESC";
        
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
    
    try {
        // Decode input
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        // Prepare and validate data
        $data = prepare_coffeeshop_data($input);
        $validation = validate_prepared_data($data);
        
        if (!$validation['valid']) {
            log_db_operation('INSERT', 'coffeeshops', $data, 'warning');
            error(implode(', ', $validation['errors']), null, 400);
        }
        
        // Also validate using existing validation function
        $validation2 = validate_coffeeshop_data($input);
        if (!$validation2['valid']) {
            error(implode(', ', $validation2['errors']), null, 400);
        }
        
        // Handle photo upload if provided
        $photoPath = null;
        if (!empty($input['photo']) && is_string($input['photo'])) {
            $photoPath = save_photo_from_base64($input['photo']);
        }
        
        // Prepare statement
        $stmt = $mysqli->prepare(
            "INSERT INTO coffeeshops (name, address, latitude, longitude, rating, status, phone, photo, category, kecamatan, kelurahan, description) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        
        // Use safe bind param with ordered fields
        $order = ['name', 'address', 'latitude', 'longitude', 'rating', 'status', 'phone', 'photo', 'category', 'kecamatan', 'kelurahan', 'description'];
        $params = generate_bind_params($data, $order);
        
        // Inject photo path into params
        $photoData = $input;
        $photoData['photo'] = $photoPath;
        $params = generate_bind_params($photoData, $order);
        
        $stmt->bind_param($params['types'], ...$params['values']);
        
        // Execute safely
        safe_execute($stmt);
        
        $new_id = $mysqli->insert_id;
        $stmt->close();
        
        log_db_operation('INSERT', 'coffeeshops', array_merge($data, ['id' => $new_id]), 'info');
        
        created(['id' => $new_id], 'Coffeeshop added successfully');
        
    } catch (Exception $e) {
        error_log("Error in add_coffeeshop: " . $e->getMessage());
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

// ==========================================
// PUT: Edit existing coffeeshop
// ==========================================
function edit_coffeeshop() {
    global $mysqli;
    
    try {
        // Get ID from query parameter
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$id) {
            error('ID parameter required', null, 400);
        }
        
        // Decode input
        $input = json_decode(file_get_contents("php://input"), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        // Prepare and validate data
        $data = prepare_coffeeshop_data($input);
        $validation = validate_prepared_data($data);
        
        if (!$validation['valid']) {
            log_db_operation('UPDATE', 'coffeeshops', array_merge($data, ['id' => $id]), 'warning');
            error(implode(', ', $validation['errors']), null, 400);
        }
        
        // Also validate using existing validation function
        $validation2 = validate_coffeeshop_data($input);
        if (!$validation2['valid']) {
            error(implode(', ', $validation2['errors']), null, 400);
        }
        
        // Handle photo upload if provided
        $photoPath = null;
        if (!empty($input['photo']) && is_string($input['photo']) && strpos($input['photo'], 'data:image') === 0) {
            $photoPath = save_photo_from_base64($input['photo']);
        }
        
        // Prepare statement
        $stmt = $mysqli->prepare(
            "UPDATE coffeeshops 
             SET name = ?, address = ?, latitude = ?, longitude = ?, rating = ?, status = ?, phone = ?, photo = ?, category = ?, kecamatan = ?, kelurahan = ?, description = ? 
             WHERE id = ?"
        );
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        
        // Use safe bind param with ordered fields (include id and photo at the end)
        $order = ['name', 'address', 'latitude', 'longitude', 'rating', 'status', 'phone', 'photo', 'category', 'kecamatan', 'kelurahan', 'description'];
        $photoData = $input;
        $photoData['photo'] = $photoPath !== null ? $photoPath : ($input['photo'] ?? null);
        $params = generate_bind_params($photoData, $order);
        $params['types'] .= 'i'; // Add integer type for id
        $params['values'][] = $id; // Add id to values
        
        $stmt->bind_param($params['types'], ...$params['values']);
        
        // Execute safely
        safe_execute($stmt);
        
        if ($stmt->affected_rows === 0) {
            $stmt->close();
            error('No coffeeshop found with that ID', null, 404);
        }
        
        $stmt->close();
        
        log_db_operation('UPDATE', 'coffeeshops', array_merge($data, ['id' => $id]), 'info');
        
        success(['id' => $id], 'Coffeeshop updated successfully');
        
    } catch (Exception $e) {
        error_log("Error in edit_coffeeshop: " . $e->getMessage());
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

// ==========================================
// DELETE: Remove coffeeshop
// ==========================================
function delete_coffeeshop() {
    global $mysqli;
    
    try {
        // Get ID from query parameter
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;
        
        if (!$id) {
            error('ID parameter required', null, 400);
        }
        
        // Prepare statement
        $stmt = $mysqli->prepare("DELETE FROM coffeeshops WHERE id = ?");
        
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        
        // Bind parameter safely
        $stmt->bind_param('i', $id);
        
        // Execute safely
        safe_execute($stmt);
        
        if ($stmt->affected_rows === 0) {
            $stmt->close();
            error('No coffeeshop found with that ID', null, 404);
        }
        
        $stmt->close();
        
        log_db_operation('DELETE', 'coffeeshops', ['id' => $id], 'info');
        
        success(['id' => $id], 'Coffeeshop deleted successfully');
        
    } catch (Exception $e) {
        error_log("Error in delete_coffeeshop: " . $e->getMessage());
        error('Error: ' . $e->getMessage(), null, 500);
    }
}

?>

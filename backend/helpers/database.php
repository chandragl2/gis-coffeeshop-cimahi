<?php
/**
 * Database Helper Functions
 * Provides safe and reusable database operations
 */

// ==========================================
// TYPE DETECTION & PARAMETER BINDING
// ==========================================

/**
 * Auto-detect and bind parameters safely
 * Prevents type mismatch errors
 * 
 * @param mysqli_stmt $stmt Prepared statement
 * @param array $data Array of values to bind
 * @throws Exception If parameter count doesn't match
 */
function safe_bind_param(&$stmt, array $data) {
    if (empty($data)) {
        return true;
    }
    
    $types = '';
    $values = [];
    
    foreach ($data as $value) {
        if (is_int($value) || is_bool($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $values[] = $value;
    }
    
    try {
        return $stmt->bind_param($types, ...$values);
    } catch (Exception $e) {
        error_log("Bind param error - Types: $types, Count: " . count($values));
        throw new Exception("Database bind error: " . $e->getMessage());
    }
}

/**
 * Safely execute prepared statement with error handling
 * 
 * @param mysqli_stmt $stmt Prepared statement
 * @return bool Success status
 * @throws Exception If execution fails
 */
function safe_execute(&$stmt) {
    if (!$stmt->execute()) {
        $error = "Execute failed: " . $stmt->error;
        error_log("SQL Error: " . $error);
        throw new Exception($error);
    }
    return true;
}

// ==========================================
// DATA PREPARATION & VALIDATION
// ==========================================

/**
 * Prepare coffeeshop data for database insertion/update
 * Sanitizes input and ensures correct data types
 * 
 * @param array $input Raw input data
 * @param array $fields List of allowed fields
 * @return array Prepared data
 */
function prepare_coffeeshop_data(array $input, array $fields = []) {
    $defaultFields = ['name', 'address', 'latitude', 'longitude', 'rating', 'status', 'phone', 'category', 'kecamatan', 'kelurahan', 'description'];
    $fields = !empty($fields) ? $fields : $defaultFields;
    
    $prepared = [];
    
    // Required fields
    $required = ['name', 'address', 'latitude', 'longitude', 'rating', 'status'];
    
    // Sanitize and type-cast
    if (isset($input['name'])) {
        $prepared['name'] = sanitize_input($input['name']);
    }
    
    if (isset($input['address'])) {
        $prepared['address'] = sanitize_input($input['address']);
    }
    
    if (isset($input['latitude'])) {
        $prepared['latitude'] = (float)$input['latitude'];
    }
    
    if (isset($input['longitude'])) {
        $prepared['longitude'] = (float)$input['longitude'];
    }
    
    if (isset($input['rating'])) {
        $prepared['rating'] = (float)$input['rating'];
    }
    
    if (isset($input['status'])) {
        $prepared['status'] = sanitize_input($input['status']);
    }
    
    if (isset($input['phone']) && !empty($input['phone'])) {
        $prepared['phone'] = sanitize_input($input['phone']);
    } else {
        $prepared['phone'] = null;
    }
    
    if (isset($input['category']) && !empty($input['category'])) {
        $prepared['category'] = sanitize_input($input['category']);
    } else {
        $prepared['category'] = null;
    }
    
    if (isset($input['kecamatan']) && !empty($input['kecamatan'])) {
        $prepared['kecamatan'] = sanitize_input($input['kecamatan']);
    } else {
        $prepared['kecamatan'] = null;
    }
    
    if (isset($input['kelurahan']) && !empty($input['kelurahan'])) {
        $prepared['kelurahan'] = sanitize_input($input['kelurahan']);
    } else {
        $prepared['kelurahan'] = null;
    }
    
    if (isset($input['description']) && !empty($input['description'])) {
        $prepared['description'] = sanitize_input($input['description']);
    } else {
        $prepared['description'] = null;
    }
    
    return $prepared;
}

/**
 * Generate type string and values array for bind_param
 * 
 * @param array $data Prepared data array
 * @param array $order Optional field order
 * @return array ['types' => string, 'values' => array]
 */
function generate_bind_params(array $data, array $order = []) {
    $types = '';
    $values = [];
    
    // Use custom order if provided
    $keys = !empty($order) ? $order : array_keys($data);
    
    foreach ($keys as $key) {
        // Always include the field (even if not in data) - use null as default
        $value = isset($data[$key]) ? $data[$key] : null;
        
        if (is_int($value) || is_bool($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            // String or NULL - both use 's' type
            $types .= 's';
        }
        
        $values[] = $value;
    }
    
    return [
        'types' => $types,
        'values' => $values
    ];
}

// ==========================================
// LOGGING & DEBUGGING
// ==========================================

/**
 * Log database operations for debugging
 * 
 * @param string $operation Type of operation (INSERT, UPDATE, DELETE, SELECT)
 * @param string $table Table name
 * @param array $data Data being operated on
 * @param string $level Log level (info, warning, error)
 */
function log_db_operation($operation, $table, $data = [], $level = 'info') {
    $timestamp = date('Y-m-d H:i:s');
    $message = "[$timestamp] $operation on $table";
    
    if (!empty($data)) {
        // Hide sensitive data in logs
        $safe_data = $data;
        if (isset($safe_data['password'])) {
            $safe_data['password'] = '***';
        }
        $message .= " - Data: " . json_encode($safe_data);
    }
    
    error_log($message);
}

/**
 * Validate prepared coffeeshop data
 * 
 * @param array $data Prepared data from prepare_coffeeshop_data()
 * @return array ['valid' => bool, 'errors' => array]
 */
function validate_prepared_data(array $data) {
    $errors = [];
    
    if (empty($data['name'])) {
        $errors[] = 'Nama coffeeshop harus diisi';
    }
    
    if (empty($data['address'])) {
        $errors[] = 'Alamat harus diisi';
    }
    
    if (empty($data['latitude']) || !is_numeric($data['latitude'])) {
        $errors[] = 'Latitude harus berupa angka';
    }
    
    if (empty($data['longitude']) || !is_numeric($data['longitude'])) {
        $errors[] = 'Longitude harus berupa angka';
    }
    
    if (empty($data['rating']) || !is_numeric($data['rating']) || $data['rating'] < 0 || $data['rating'] > 5) {
        $errors[] = 'Rating harus berupa angka antara 0-5';
    }
    
    if (empty($data['status'])) {
        $errors[] = 'Status harus diisi';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

?>

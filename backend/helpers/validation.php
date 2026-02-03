<?php
/**
 * Validation Helper - Input Validation Functions
 */

/**
 * Validate coordinates
 * Latitude: -90 to 90
 * Longitude: -180 to 180
 */
function validate_coordinates($latitude, $longitude) {
    $lat = floatval($latitude);
    $lng = floatval($longitude);
    
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        return ['valid' => false, 'error' => 'Koordinat harus berupa angka'];
    }
    
    if ($lat < -90 || $lat > 90) {
        return ['valid' => false, 'error' => 'Latitude harus antara -90 dan 90'];
    }
    
    if ($lng < -180 || $lng > 180) {
        return ['valid' => false, 'error' => 'Longitude harus antara -180 dan 180'];
    }
    
    return ['valid' => true];
}

/**
 * Validate rating
 * Rating: 0 to 5
 */
function validate_rating($rating) {
    $r = floatval($rating);
    
    if (!is_numeric($rating)) {
        return ['valid' => false, 'error' => 'Rating harus berupa angka'];
    }
    
    if ($r < 0 || $r > 5) {
        return ['valid' => false, 'error' => 'Rating harus antara 0 dan 5'];
    }
    
    return ['valid' => true];
}

/**
 * Validate status
 * Status: 'Aktif' or 'Tidak Aktif'
 */
function validate_status($status) {
    $allowed = ['Aktif', 'Tidak Aktif'];
    
    if (!in_array($status, $allowed)) {
        return ['valid' => false, 'error' => 'Status harus "Aktif" atau "Tidak Aktif"'];
    }
    
    return ['valid' => true];
}

/**
 * Validate coffeeshop data
 */
function validate_coffeeshop_data($data) {
    $errors = [];
    
    // Required fields
    if (empty($data['name'])) {
        $errors[] = 'Nama coffeeshop harus diisi';
    }
    
    if (empty($data['address'])) {
        $errors[] = 'Alamat harus diisi';
    }
    
    if (empty($data['latitude']) || empty($data['longitude'])) {
        $errors[] = 'Koordinat harus diisi';
    } else {
        $coord_validation = validate_coordinates($data['latitude'], $data['longitude']);
        if (!$coord_validation['valid']) {
            $errors[] = $coord_validation['error'];
        }
    }
    
    if (empty($data['rating'])) {
        $errors[] = 'Rating harus diisi';
    } else {
        $rating_validation = validate_rating($data['rating']);
        if (!$rating_validation['valid']) {
            $errors[] = $rating_validation['error'];
        }
    }
    
    if (empty($data['status'])) {
        $errors[] = 'Status harus diisi';
    } else {
        $status_validation = validate_status($data['status']);
        if (!$status_validation['valid']) {
            $errors[] = $status_validation['error'];
        }
    }
    
    if (!empty($errors)) {
        return ['valid' => false, 'errors' => $errors];
    }
    
    return ['valid' => true];
}

/**
 * Sanitize input string
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

?>

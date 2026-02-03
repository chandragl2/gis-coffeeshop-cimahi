<?php
/**
 * Response Helper - Standardized API Responses
 * 
 * Provides consistent JSON response format for all API endpoints
 */

/**
 * Send standardized JSON response
 * 
 * @param bool $success - Success status
 * @param mixed $data - Response data (array or null)
 * @param string $message - Optional message
 * @param int $httpCode - HTTP status code (default 200)
 */
function json_response($success, $data = null, $message = null, $httpCode = 200) {
    http_response_code($httpCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
    exit;
}

/**
 * Send success response
 */
function success($data = null, $message = 'Success') {
    json_response(true, $data, $message, 200);
}

/**
 * Send error response
 */
function error($message = 'Error', $data = null, $httpCode = 400) {
    json_response(false, $data, $message, $httpCode);
}

/**
 * Send created response (201)
 */
function created($data = null, $message = 'Created successfully') {
    json_response(true, $data, $message, 201);
}

/**
 * Send not found response (404)
 */
function not_found($message = 'Resource not found') {
    json_response(false, null, $message, 404);
}

/**
 * Send unauthorized response (401)
 */
function unauthorized($message = 'Unauthorized') {
    json_response(false, null, $message, 401);
}

?>

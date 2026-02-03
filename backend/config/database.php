<?php
/**
 * Database Configuration
 * Centralized database connection settings
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_NAME', 'gis_coffeeshop');
define('DB_USER', 'root');
define('DB_PASS', 'Kamarmandi23.');

// Alternative ports (fallback)
define('DB_PORTS', [3306, 8112]);

// Application settings
define('APP_NAME', 'GIS Coffeeshop Cimahi');
define('APP_VERSION', '1.0');
define('APP_ENV', 'production'); // development or production

// API settings
define('API_TIMEOUT', 30);
define('API_MAX_RETRIES', 3);

/**
 * Get database connection
 * Returns MySQLi connection with automatic fallback
 */
function get_db_connection() {
    $mysqli = null;
    
    // Try main port
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($mysqli->connect_error) {
        // Try fallback ports
        foreach (DB_PORTS as $port) {
            if ($port == DB_PORT) continue; // Skip already tried port
            
            $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, $port);
            
            if (!$mysqli->connect_error) {
                return $mysqli;
            }
        }
        
        return null; // Connection failed
    }
    
    // Set charset
    $mysqli->set_charset('utf8mb4');
    
    return $mysqli;
}

/**
 * Close database connection
 */
function close_db_connection($mysqli) {
    if ($mysqli) {
        $mysqli->close();
    }
}

// Create global connection
$GLOBALS['db'] = get_db_connection();

?>

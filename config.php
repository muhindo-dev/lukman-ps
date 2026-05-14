<?php
/**
 * Lukman Primary School Website Configuration
 * 
 * For production deployment:
 * 1. Copy config-production.php to config.php on your server
 * 2. Update all credentials with production values
 * 3. Never commit production credentials to version control
 */

// Environment detection (set to 'production' on live server)
define('ENVIRONMENT', 'development');

// Canonical base URL — used in meta tags and redirects; never rely on HTTP_HOST (spoofable)
define('SITE_BASE_URL', ENVIRONMENT === 'production' ? 'https://lukmanps.ac.ug' : 'http://localhost:8888/lukman-ps');

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '8889');
define('DB_NAME', 'lukman_php');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_SOCKET', '/Applications/MAMP/tmp/mysql/mysql.sock'); // Leave empty '' for production/online hosting

// Error reporting based on environment
if (ENVIRONMENT === 'production') {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Session security settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    if (ENVIRONMENT === 'production') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}
?>

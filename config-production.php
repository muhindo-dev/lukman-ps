<?php
// ============================================================
// Production configuration — Lukman Primary School
// Update the values marked CHANGE_ME before deploying.
// ============================================================

// Database
define('DB_HOST',   'localhost');           // Usually localhost on shared hosting
define('DB_NAME',   'lukman_php');          // Your production database name
define('DB_USER',   'CHANGE_ME');           // Production DB username
define('DB_PASS',   'CHANGE_ME');           // Production DB password — use a strong password
define('DB_SOCKET', '');                    // Leave empty; set socket path only if required by host

// Site
define('SITE_URL',  'https://lukmanps.ac.ug'); // Live domain — no trailing slash

// Environment
define('ENVIRONMENT', 'production');

// Error reporting — do NOT show errors to visitors
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors',     1);
ini_set('error_log',      __DIR__ . '/error.log');

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

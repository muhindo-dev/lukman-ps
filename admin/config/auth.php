<?php
/**
 * Admin Authentication System
 * Handles login, logout, session management, and security
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration and functions
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../functions.php';

// Seed default admin user if none exists
(function() {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return;

        // Create admin_users table if it doesn't exist
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(50),
            role ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
            status ENUM('active', 'inactive') DEFAULT 'active',
            avatar VARCHAR(255),
            last_login DATETIME,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_username (username),
            INDEX idx_email (email),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $count = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($count == 0) {
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, full_name, email, role, status) VALUES (?, ?, ?, ?, 'super_admin', 'active')");
            $stmt->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'Admin User', 'info@lukmanps.ac.ug']);
        }
    } catch (Exception $e) {
        error_log("Admin seed error: " . $e->getMessage());
    }
})();

/**
 * Check if admin is logged in
 * @return bool True if logged in, false otherwise
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Require admin authentication - redirect to login if not authenticated
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Authenticate admin credentials
 * @param string $username Username
 * @param string $password Password
 * @return array Result with success status and message
 */
function authenticateAdmin($username, $password) {
    try {
        $pdo = getDBConnection();
        
        if (!$pdo) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }
        
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? AND status = 'active' LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$admin) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        // Verify password
        if (password_verify($password, $admin['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['admin_last_activity'] = time();
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = ?");
            $updateStmt->execute([$admin['id']]);
            
            return ['success' => true, 'message' => 'Login successful'];
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
        
    } catch (PDOException $e) {
        error_log("Authentication error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Authentication failed'];
    }
}

/**
 * Logout admin user
 */
function logoutAdmin() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Check session timeout (30 minutes of inactivity)
 */
function checkSessionTimeout() {
    $timeout = 1800; // 30 minutes
    
    if (isset($_SESSION['admin_last_activity'])) {
        $elapsed = time() - $_SESSION['admin_last_activity'];
        
        if ($elapsed > $timeout) {
            logoutAdmin();
            header('Location: login.php?timeout=1');
            exit;
        }
    }
    
    $_SESSION['admin_last_activity'] = time();
}

/**
 * Get current admin details
 * @return array Admin details or null
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['admin_id'] ?? null,
        'username' => $_SESSION['admin_username'] ?? null,
        'name' => $_SESSION['admin_name'] ?? 'Admin'
    ];
}

/**
 * Generate CSRF token
 * @return string CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * @param string $token Token to verify
 * @return bool True if valid, false otherwise
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Log admin activity
 * @param string $action Action performed
 * @param string $module Module affected
 * @param int $recordId Record ID (optional)
 */
function logAdminActivity($action, $module, $recordId = null) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return;
        
        $admin = getCurrentAdmin();
        if (!$admin) return;
        
        $stmt = $pdo->prepare("
            INSERT INTO admin_activity_log (admin_id, action, module, record_id, ip_address, user_agent, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $admin['id'],
            $action,
            $module,
            $recordId,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        
    } catch (PDOException $e) {
        error_log("Activity logging error: " . $e->getMessage());
    }
}

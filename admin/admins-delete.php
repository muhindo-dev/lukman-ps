<?php
/**
 * Admin Users - Delete
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

// Verify CSRF token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (!verifyCSRFToken($token)) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    header('Location: admins.php');
    exit;
}

$currentAdmin = getCurrentAdmin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid admin ID.'];
    header('Location: admins.php');
    exit;
}

// Prevent self-deletion
if ($id == $currentAdmin['id']) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'You cannot delete your own account.'];
    header('Location: admins.php');
    exit;
}

// Get admin to delete avatar
$admin = getRecordById('admin_users', $id);

if (!$admin) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Admin user not found.'];
    header('Location: admins.php');
    exit;
}

// Delete avatar file if exists
if ($admin['avatar'] && file_exists('../uploads/' . $admin['avatar'])) {
    unlink('../uploads/' . $admin['avatar']);
}

// Delete the admin
$result = deleteRecord('admin_users', $id);

if ($result) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Admin user deleted successfully.'];
} else {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to delete admin user.'];
}

header('Location: admins.php');
exit;

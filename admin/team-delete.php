<?php
/**
 * Our Team - Delete Member
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

// Verify CSRF token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (!verifyCSRFToken($token)) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    header('Location: team.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$member = getRecordById('team_members', $id);

if (!$member) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Team member not found.'];
    header('Location: team.php');
    exit;
}

// Delete photo if exists
if ($member['photo'] && file_exists('../uploads/' . $member['photo'])) {
    unlink('../uploads/' . $member['photo']);
}

// Delete record
$result = deleteRecord('team_members', $id);

if ($result) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Team member deleted successfully.'];
} else {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Failed to delete team member.'];
}

header('Location: team.php');
exit;

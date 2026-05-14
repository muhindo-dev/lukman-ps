<?php
/**
 * Events Management - Delete Event
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

// Check authentication
requireAdmin();
checkSessionTimeout();

// Verify CSRF token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (!verifyCSRFToken($token)) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    header('Location: events.php');
    exit;
}

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$eventId) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid event ID'];
    header('Location: events.php');
    exit;
}

// Get event data
$event = getRecordById('events', $eventId);

if (!$event) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Event not found'];
    header('Location: events.php');
    exit;
}

// Delete event image if exists
if ($event['event_image']) {
    deleteUploadedFile('../uploads/' . $event['event_image']);
}

// Delete event record
$deleted = deleteRecord('events', $eventId);

if ($deleted) {
    logAdminActivity('delete', 'events', $eventId);
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Event deleted successfully!'];
} else {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Failed to delete event'];
}

header('Location: events.php');
exit;

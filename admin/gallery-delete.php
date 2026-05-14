<?php
/**
 * Gallery Management - Delete Album
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

// Verify CSRF token
$token = isset($_GET['token']) ? $_GET['token'] : '';
if (!verifyCSRFToken($token)) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
    header('Location: gallery.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid album ID.'];
    header('Location: gallery.php');
    exit;
}

$album = getRecordById('gallery_albums', $id);

if (!$album) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Album not found.'];
    header('Location: gallery.php');
    exit;
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE album_id = ?");
$stmt->execute([$id]);
$images = $stmt->fetchAll();

foreach ($images as $image) {
    if ($image['image_path'] && file_exists('../uploads/' . $image['image_path'])) {
        unlink('../uploads/' . $image['image_path']);
    }
    if ($image['thumbnail_path'] && file_exists('../uploads/' . $image['thumbnail_path'])) {
        unlink('../uploads/' . $image['thumbnail_path']);
    }
}

$stmt = $pdo->prepare("DELETE FROM gallery_images WHERE album_id = ?");
$stmt->execute([$id]);

if (!empty($album['cover_image']) && file_exists('../uploads/' . $album['cover_image'])) {
    unlink('../uploads/' . $album['cover_image']);
}

$result = deleteRecord('gallery_albums', $id);

if ($result) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Album and all images deleted successfully!'];
} else {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Failed to delete album.'];
}

header('Location: gallery.php');
exit;

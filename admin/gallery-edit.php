<?php
/**
 * Gallery Management - Edit Album
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'gallery';
$pageTitle = 'Edit Album';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$album = getRecordById('gallery_albums', $id);

if (!$album) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Album not found.'];
    header('Location: gallery.php');
    exit;
}

$error = '';
$success = '';

$categories = ['Events', 'Programs', 'Community', 'Volunteers', 'Success Stories', 'Behind the Scenes'];

$events = getAllRecords('events', [], 'event_date DESC', 100, 0);
if (!$events) $events = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $eventId = !empty($_POST['event_id']) ? (int)$_POST['event_id'] : null;
    $status = $_POST['status'] ?? 'active';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Validation
    if (empty($title)) {
        $error = 'Album title is required.';
    } elseif (empty($category)) {
        $error = 'Please select a category.';
    }
    
    // Handle cover image upload
    $coverImagePath = $album['cover_image'];
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK && $_FILES['cover_image']['size'] > 0) {
        $uploadDir = '../uploads/gallery/albums/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        // Manual upload with extended type support
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowedExts)) {
            $newName = uniqid('cover_') . '.' . $ext;
            $targetPath = $uploadDir . $newName;
            
            if (move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetPath)) {
                // Delete old cover if exists
                if ($album['cover_image'] && file_exists('../uploads/' . $album['cover_image'])) {
                    unlink('../uploads/' . $album['cover_image']);
                }
                $coverImagePath = 'gallery/albums/' . $newName;
            } else {
                $error = 'Failed to upload cover image.';
            }
        } else {
            $error = 'Invalid image type. Allowed: JPG, PNG, GIF, WebP';
        }
    }
    
    if (!$error) {
        $slug = $album['slug'];
        if ($title !== $album['title']) {
            $slug = generateSlug($title);
            $slug = ensureUniqueSlug('gallery_albums', $slug, $id);
        }
        
        $data = [
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'cover_image' => $coverImagePath,
            'category' => $category,
            'event_id' => $eventId,
            'status' => $status,
            'is_featured' => $isFeatured,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = updateRecord('gallery_albums', $id, $data);
        
        if ($result) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Album updated successfully!'];
            header('Location: gallery.php');
            exit;
        } else {
            $error = 'Failed to update album. Please try again.';
        }
    }
    
    // Update album data with form values for redisplay
    $album['title'] = $title;
    $album['description'] = $description;
    $album['category'] = $category;
    $album['event_id'] = $eventId;
    $album['status'] = $status;
    $album['is_featured'] = $isFeatured;
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-edit"></i> Edit Album</h1>
        <div class="header-actions">
            <a href="gallery-images.php?album=<?php echo $id; ?>" class="btn-sm btn-primary">
                <i class="fas fa-images"></i> Manage Images (<?php echo $album['image_count']; ?>)
            </a>
            <a href="gallery.php" class="btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="albumForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-edit"></i> Album Details</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="form-group mb-3">
                            <label for="title" class="form-label required">Album Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Enter album title..." required maxlength="200" value="<?php echo htmlspecialchars($album['title']); ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Brief description of this album..."><?php echo htmlspecialchars($album['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="card-compact stats-card">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-chart-bar"></i> Album Statistics</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="stats-row">
                            <div class="stat-box">
                                <span class="stat-value"><?php echo $album['image_count']; ?></span>
                                <span class="stat-label">Images</span>
                            </div>
                            <div class="stat-box">
                                <span class="stat-value"><?php echo number_format($album['views']); ?></span>
                                <span class="stat-label">Views</span>
                            </div>
                            <div class="stat-box">
                                <span class="stat-value"><?php echo date('M j, Y', strtotime($album['created_at'])); ?></span>
                                <span class="stat-label">Created</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-cog"></i> Album Settings</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="form-group mb-3">
                            <label for="category" class="form-label required">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo ($album['category'] == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="event_id" class="form-label">Link to Event</label>
                            <select id="event_id" name="event_id" class="form-control">
                                <option value="">No Event</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?php echo $event['id']; ?>" <?php echo ($album['event_id'] == $event['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($event['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text">Associate with an event (optional)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo ($album['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="hidden" <?php echo ($album['status'] == 'hidden') ? 'selected' : ''; ?>>Hidden</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Cover Image</label>
                            <?php if ($album['cover_image']): ?>
                                <div class="current-cover">
                                    <img src="../uploads/<?php echo htmlspecialchars($album['cover_image']); ?>" alt="Current cover">
                                </div>
                            <?php endif; ?>
                            <div class="upload-area-small">
                                <input type="file" name="cover_image" id="coverInput" accept="image/*" class="upload-input">
                                <div class="upload-content-small">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span><?php echo $album['cover_image'] ? 'Upload new cover' : 'Click to upload'; ?></span>
                                </div>
                            </div>
                            <div id="coverPreview" class="cover-preview" style="display:none;">
                                <img id="previewImg" src="" alt="Preview">
                                <button type="button" class="remove-btn" onclick="removeCoverPreview()"><i class="fas fa-times"></i></button>
                            </div>
                            <small class="form-text">Or manage images to set a different cover</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="is_featured" value="1" <?php echo $album['is_featured'] ? 'checked' : ''; ?>>
                                <span>Featured Album</span>
                            </label>
                            <small class="form-text d-block">Show on homepage gallery section</small>
                        </div>

                        <hr class="my-3">

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Update Album
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.header-actions {
    display: flex;
    gap: 0.5rem;
}

.stats-card {
    background: #fffbeb;
}
.stats-card .card-header-compact {
    border-bottom-color: #FFC107;
}
.stats-row {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.75rem;
}
.stat-box {
    text-align: center;
    padding: 0.75rem 0.5rem;
    background: #fff;
    border: 2px solid #FFC107;
}
.stat-value {
    display: block;
    font-size: 1.125rem;
    font-weight: 700;
    color: #000;
}
.stat-label {
    font-size: 0.6875rem;
    color: #6c757d;
    text-transform: uppercase;
}

.current-cover {
    margin-bottom: 0.5rem;
}
.current-cover img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border: 2px solid #dee2e6;
}

.upload-area-small {
    border: 2px dashed #dee2e6;
    background: #f8f9fa;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}
.upload-area-small:hover {
    border-color: #FFC107;
    background: #fffbeb;
}
.upload-input {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    top: 0;
    left: 0;
    z-index: 10;
}
.upload-content-small {
    padding: 1rem;
    text-align: center;
}
.upload-content-small i {
    font-size: 1.5rem;
    color: #FFC107;
    display: block;
    margin-bottom: 0.25rem;
}
.upload-content-small span {
    font-size: 0.8125rem;
    color: #6c757d;
}

.cover-preview {
    position: relative;
    margin-top: 0.5rem;
}
.cover-preview img {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border: 2px solid #FFC107;
}
.remove-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 24px;
    height: 24px;
    background: #dc3545;
    color: #fff;
    border: none;
    cursor: pointer;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.checkbox-inline {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: 500;
}
.checkbox-inline input {
    width: 18px;
    height: 18px;
    margin: 0;
}

.btn-block {
    width: 100%;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coverInput = document.getElementById('coverInput');
    const coverPreview = document.getElementById('coverPreview');
    const previewImg = document.getElementById('previewImg');
    const uploadArea = document.querySelector('.upload-area-small');
    
    coverInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                coverPreview.style.display = 'block';
                uploadArea.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});

function removeCoverPreview() {
    document.getElementById('coverInput').value = '';
    document.getElementById('coverPreview').style.display = 'none';
    document.querySelector('.upload-area-small').style.display = 'block';
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
/**
 * Gallery Management - Create Album
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'gallery';
$pageTitle = 'Create Album';

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
    } elseif (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        $error = 'Please upload at least one image.';
    }
    
    if (!$error) {
        $slug = generateSlug($title);
        $uniqueSlug = ensureUniqueSlug('gallery_albums', $slug);
        
        $data = [
            'title' => $title,
            'slug' => $uniqueSlug,
            'description' => $description,
            'cover_image' => null,
            'category' => $category,
            'event_id' => $eventId,
            'status' => $status,
            'is_featured' => $isFeatured,
            'image_count' => 0,
            'views' => 0,
            'created_by' => $_SESSION['admin_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $albumId = insertRecord('gallery_albums', $data);
        
        if ($albumId) {
            $uploadDir = '../uploads/gallery/albums/';
            $thumbDir = '../uploads/gallery/thumbs/';
            
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);
            
            $successCount = 0;
            $firstThumb = null;
            $pdo = getDBConnection();
            
            $fileCount = count($_FILES['images']['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['images']['tmp_name'][$i];
                    $originalName = $_FILES['images']['name'][$i];
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $newName = uniqid('img_') . '.' . $ext;
                        $imagePath = 'gallery/albums/' . $newName;
                        $thumbPath = 'gallery/thumbs/' . $newName;
                        
                        if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                            createThumbnail($uploadDir . $newName, $thumbDir . $newName, 300, 300);
                            
                            $isCover = ($successCount === 0) ? 1 : 0;
                            $stmt = $pdo->prepare("INSERT INTO gallery_images (album_id, image_path, thumbnail_path, title, sort_order, is_cover, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                            $stmt->execute([$albumId, $imagePath, $thumbPath, pathinfo($originalName, PATHINFO_FILENAME), $successCount + 1, $isCover]);
                            
                            if ($successCount === 0) {
                                $firstThumb = $thumbPath;
                            }
                            $successCount++;
                        }
                    }
                }
            }
            
            $stmt = $pdo->prepare("UPDATE gallery_albums SET image_count = ?, cover_image = ? WHERE id = ?");
            $stmt->execute([$successCount, $firstThumb, $albumId]);
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => "Album created with $successCount images!"];
            header('Location: gallery.php');
            exit;
        } else {
            $error = 'Failed to create album. Please try again.';
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-plus-circle"></i> Create Album</h1>
        <a href="gallery.php" class="btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
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
                            <input type="text" id="title" name="title" class="form-control" placeholder="Enter album title..." required maxlength="200" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Brief description of this album..."><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-images"></i> Album Images</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="upload-area" id="uploadArea">
                            <input type="file" name="images[]" id="imageInput" multiple accept="image/*" class="upload-input">
                            <div class="upload-content">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p><strong>Click to select images</strong></p>
                                <small>JPG, PNG, GIF, WebP • Hold Ctrl/Cmd to select multiple</small>
                            </div>
                        </div>
                        
                        <div id="fileInfo" class="file-info" style="display:none;">
                            <i class="fas fa-check-circle"></i> <span id="fileCount">0</span> image(s) selected
                        </div>
                        
                        <div id="previewGrid" class="preview-grid"></div>
                        
                        <small class="form-text"><i class="fas fa-info-circle"></i> First image will be set as album cover</small>
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
                                    <option value="<?php echo $cat; ?>" <?php echo (($_POST['category'] ?? '') == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="event_id" class="form-label">Link to Event</label>
                            <select id="event_id" name="event_id" class="form-control">
                                <option value="">No Event</option>
                                <?php foreach ($events as $event): ?>
                                    <option value="<?php echo $event['id']; ?>" <?php echo (($_POST['event_id'] ?? '') == $event['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($event['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text">Associate with an event (optional)</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo (($_POST['status'] ?? 'active') == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="hidden" <?php echo (($_POST['status'] ?? '') == 'hidden') ? 'selected' : ''; ?>>Hidden</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="checkbox-inline">
                                <input type="checkbox" name="is_featured" value="1" <?php echo isset($_POST['is_featured']) ? 'checked' : ''; ?>>
                                <span>Featured Album</span>
                            </label>
                            <small class="form-text d-block">Show on homepage gallery section</small>
                        </div>

                        <hr class="my-3">

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Create Album
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.upload-area {
    border: 2px dashed #dee2e6;
    background: #f8f9fa;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}
.upload-area:hover, .upload-area.dragover {
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
.upload-content {
    padding: 2rem;
    text-align: center;
}
.upload-content i {
    font-size: 2.5rem;
    color: #FFC107;
    margin-bottom: 0.5rem;
}
.upload-content p {
    margin: 0.5rem 0 0.25rem;
}
.upload-content small {
    color: #6c757d;
}

.file-info {
    margin-top: 0.75rem;
    padding: 0.5rem 0.75rem;
    background: #d4edda;
    border: 2px solid #28a745;
    color: #155724;
    font-size: 0.875rem;
}

.preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 0.5rem;
    margin-top: 1rem;
}
.preview-grid:empty {
    display: none;
}
.preview-item {
    position: relative;
    aspect-ratio: 1;
}
.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border: 2px solid #dee2e6;
}
.preview-item:first-child img {
    border-color: #FFC107;
}
.preview-item:first-child::after {
    content: 'Cover';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: #FFC107;
    color: #000;
    font-size: 0.625rem;
    text-align: center;
    padding: 2px;
    font-weight: 600;
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
    const uploadArea = document.getElementById('uploadArea');
    const imageInput = document.getElementById('imageInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileCount = document.getElementById('fileCount');
    const previewGrid = document.getElementById('previewGrid');
    
    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            updatePreviews();
        }
    });
    
    imageInput.addEventListener('change', updatePreviews);
    
    function updatePreviews() {
        previewGrid.innerHTML = '';
        
        if (imageInput.files && imageInput.files.length > 0) {
            fileInfo.style.display = 'block';
            fileCount.textContent = imageInput.files.length;
            
            Array.from(imageInput.files).forEach(function(file) {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'preview-item';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        div.appendChild(img);
                        previewGrid.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            fileInfo.style.display = 'none';
        }
    }
});
</script>

<?php include 'includes/footer.php'; ?>

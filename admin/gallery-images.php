<?php
/**
 * Gallery Management - Album Images
 * Creative multi-image upload with drag & drop, sortable grid, lightbox preview
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'gallery';

$albumId = isset($_GET['album']) ? (int)$_GET['album'] : 0;
$album = getRecordById('gallery_albums', $albumId);

if (!$album) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Album not found.'];
    header('Location: gallery.php');
    exit;
}

$pageTitle = 'Images: ' . $album['title'];

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE album_id = ? ORDER BY sort_order ASC, created_at DESC");
$stmt->execute([$albumId]);
$images = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action === 'upload' && isset($_FILES['images'])) {
            $uploadDir = '../uploads/gallery/albums/';
            $thumbDir = '../uploads/gallery/thumbs/';
            $successCount = 0;
            $errorCount = 0;
            
            $files = $_FILES['images'];
            $fileCount = count($files['name']);
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files['tmp_name'][$i];
                    $originalName = $files['name'][$i];
                    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $newName = uniqid('img_') . '.' . $ext;
                        $imagePath = 'gallery/albums/' . $newName;
                        $thumbPath = 'gallery/thumbs/' . $newName;
                        
                        if (move_uploaded_file($tmpName, $uploadDir . $newName)) {
                            // Create thumbnail
                            createThumbnail($uploadDir . $newName, $thumbDir . $newName, 300, 300);
                            
                            // Get current max sort order
                            $stmt = $pdo->prepare("SELECT MAX(sort_order) as max_order FROM gallery_images WHERE album_id = ?");
                            $stmt->execute([$albumId]);
                            $maxOrder = $stmt->fetch()['max_order'] ?? 0;
                            
                            // Insert image record
                            $stmt = $pdo->prepare("INSERT INTO gallery_images (album_id, image_path, thumbnail_path, title, sort_order, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
                            $stmt->execute([$albumId, $imagePath, $thumbPath, pathinfo($originalName, PATHINFO_FILENAME), $maxOrder + 1]);
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        $errorCount++;
                    }
                }
            }
            
            // Update image count
            $stmt = $pdo->prepare("UPDATE gallery_albums SET image_count = (SELECT COUNT(*) FROM gallery_images WHERE album_id = ?), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$albumId, $albumId]);
            
            // Set cover if none exists
            if (!$album['cover_image']) {
                $stmt = $pdo->prepare("SELECT thumbnail_path FROM gallery_images WHERE album_id = ? ORDER BY sort_order LIMIT 1");
                $stmt->execute([$albumId]);
                $firstImage = $stmt->fetch();
                if ($firstImage) {
                    $stmt = $pdo->prepare("UPDATE gallery_albums SET cover_image = ? WHERE id = ?");
                    $stmt->execute([$firstImage['thumbnail_path'], $albumId]);
                }
            }
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => "$successCount images uploaded" . ($errorCount ? ", $errorCount failed" : "") . "."];
            header('Location: gallery-images.php?album=' . $albumId);
            exit;
        }
        
        if ($action === 'delete' && isset($_POST['image_id'])) {
            // Verify CSRF token
            $csrfToken = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
            if (!verifyCSRFToken($csrfToken)) {
                $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid security token. Please try again.'];
                header('Location: gallery-images.php?album=' . $albumId);
                exit;
            }
            
            $imageId = (int)$_POST['image_id'];
            $stmt = $pdo->prepare("SELECT * FROM gallery_images WHERE id = ? AND album_id = ?");
            $stmt->execute([$imageId, $albumId]);
            $image = $stmt->fetch();
            
            if ($image) {
                if ($image['image_path'] && file_exists('../uploads/' . $image['image_path'])) {
                    unlink('../uploads/' . $image['image_path']);
                }
                if ($image['thumbnail_path'] && file_exists('../uploads/' . $image['thumbnail_path'])) {
                    unlink('../uploads/' . $image['thumbnail_path']);
                }
                
                $stmt = $pdo->prepare("DELETE FROM gallery_images WHERE id = ?");
                $stmt->execute([$imageId]);
                
                $stmt = $pdo->prepare("UPDATE gallery_albums SET image_count = image_count - 1, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$albumId]);
                
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Image deleted.'];
            }
            header('Location: gallery-images.php?album=' . $albumId);
            exit;
        }
        
        if ($action === 'set_cover' && isset($_POST['image_id'])) {
            $imageId = (int)$_POST['image_id'];
            $stmt = $pdo->prepare("SELECT thumbnail_path FROM gallery_images WHERE id = ? AND album_id = ?");
            $stmt->execute([$imageId, $albumId]);
            $image = $stmt->fetch();
            
            if ($image) {
                $stmt = $pdo->prepare("UPDATE gallery_albums SET cover_image = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$image['thumbnail_path'], $albumId]);
                
                // Reset all is_cover flags and set new one
                $pdo->prepare("UPDATE gallery_images SET is_cover = 0 WHERE album_id = ?")->execute([$albumId]);
                $pdo->prepare("UPDATE gallery_images SET is_cover = 1 WHERE id = ?")->execute([$imageId]);
                
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Album cover updated.'];
            }
            header('Location: gallery-images.php?album=' . $albumId);
            exit;
        }
        
        if ($action === 'update_order' && isset($_POST['order'])) {
            $order = json_decode($_POST['order'], true);
            if ($order) {
                foreach ($order as $position => $imageId) {
                    $stmt = $pdo->prepare("UPDATE gallery_images SET sort_order = ? WHERE id = ? AND album_id = ?");
                    $stmt->execute([$position + 1, $imageId, $albumId]);
                }
            }
            echo json_encode(['success' => true]);
            exit;
        }
        
        if ($action === 'update_caption' && isset($_POST['image_id'])) {
            $imageId = (int)$_POST['image_id'];
            $title = trim($_POST['title'] ?? '');
            $caption = trim($_POST['caption'] ?? '');
            $alt_text = trim($_POST['alt_text'] ?? '');
            
            $stmt = $pdo->prepare("UPDATE gallery_images SET title = ?, caption = ?, alt_text = ? WHERE id = ? AND album_id = ?");
            $stmt->execute([$title, $caption, $alt_text, $imageId, $albumId]);
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Image details updated.'];
            header('Location: gallery-images.php?album=' . $albumId);
            exit;
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
            <i class="fas fa-<?php echo $_SESSION['alert']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $_SESSION['alert']['message']; ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>

    <div class="content-header-compact">
        <div class="header-left">
            <h1><i class="fas fa-images"></i> <?php echo htmlspecialchars($album['title']); ?></h1>
            <span class="image-count"><?php echo count($images); ?> images</span>
        </div>
        <div class="header-actions">
            <a href="gallery-edit.php?id=<?php echo $albumId; ?>" class="btn-sm btn-secondary"><i class="fas fa-edit"></i> Edit Album</a>
            <a href="gallery.php" class="btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> All Albums</a>
        </div>
    </div>

    <!-- Multi-Image Upload Zone -->
    <div class="upload-area" id="dropZone">
        <div class="upload-inner">
            <div class="upload-icon">
                <i class="fas fa-cloud-upload-alt"></i>
            </div>
            <h3>Drag & Drop Images Here</h3>
            <p>or click to browse • JPG, PNG, GIF, WebP • Max 10MB each</p>
            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="action" value="upload">
                <input type="file" name="images[]" id="fileInput" multiple accept="image/*" style="display: none;">
                <button type="button" class="btn-upload" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-folder-open"></i> Select Images
                </button>
            </form>
        </div>
        <div class="upload-progress" id="uploadProgress" style="display: none;">
            <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
            <span id="progressText">Uploading...</span>
        </div>
        <div class="upload-preview-grid" id="previewGrid"></div>
    </div>

    <?php if (empty($images)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>No images in this album yet</p>
            <p class="hint">Drag and drop images above to get started</p>
        </div>
    <?php else: ?>
        <div class="toolbar">
            <span class="toolbar-hint"><i class="fas fa-grip-vertical"></i> Drag to reorder</span>
            <span class="toolbar-hint"><i class="fas fa-mouse-pointer"></i> Click image for actions</span>
        </div>

        <div class="images-grid" id="imagesGrid">
            <?php foreach ($images as $image): ?>
                <div class="image-card" data-id="<?php echo $image['id']; ?>">
                    <div class="drag-handle"><i class="fas fa-grip-vertical"></i></div>
                    <div class="image-thumb" onclick="openLightbox(<?php echo $image['id']; ?>)">
                        <img src="../uploads/<?php echo htmlspecialchars($image['thumbnail_path'] ?: $image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?: $image['title']); ?>">
                        <?php if ($image['is_cover']): ?>
                            <span class="cover-badge"><i class="fas fa-star"></i> Cover</span>
                        <?php endif; ?>
                    </div>
                    <div class="image-actions">
                        <button type="button" class="action-btn" onclick="editImage(<?php echo $image['id']; ?>)" title="Edit Details"><i class="fas fa-edit"></i></button>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="set_cover">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <button type="submit" class="action-btn" title="Set as Cover" <?php echo $image['is_cover'] ? 'disabled' : ''; ?>><i class="fas fa-star"></i></button>
                        </form>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this image?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <button type="submit" class="action-btn danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                    <?php if ($image['title']): ?>
                        <div class="image-title"><?php echo htmlspecialchars($image['title']); ?></div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Lightbox Modal -->
<div class="lightbox" id="lightbox">
    <button class="lightbox-close" onclick="closeLightbox()"><i class="fas fa-times"></i></button>
    <button class="lightbox-nav prev" onclick="navLightbox(-1)"><i class="fas fa-chevron-left"></i></button>
    <button class="lightbox-nav next" onclick="navLightbox(1)"><i class="fas fa-chevron-right"></i></button>
    <div class="lightbox-content">
        <img id="lightboxImg" src="" alt="">
        <div class="lightbox-caption" id="lightboxCaption"></div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Image Details</h3>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="update_caption">
            <input type="hidden" name="image_id" id="editImageId">
            <div class="modal-body">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" id="editTitle" class="form-control" placeholder="Image title">
                </div>
                <div class="form-group">
                    <label>Caption</label>
                    <textarea name="caption" id="editCaption" class="form-control" rows="3" placeholder="Image caption"></textarea>
                </div>
                <div class="form-group">
                    <label>Alt Text (for accessibility)</label>
                    <input type="text" name="alt_text" id="editAltText" class="form-control" placeholder="Describe the image">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Save</button>
            </div>
        </form>
    </div>
</div>

<style>
.content-header-compact { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.375rem; border-bottom: 2px solid #FFC107; }
.header-left { display: flex; align-items: center; gap: 0.75rem; }
.content-header-compact h1 { font-size: 1.25rem; margin: 0; font-weight: 700; }
.image-count { background: #FFC107; color: #000; padding: 0.125rem 0.5rem; font-size: 0.75rem; font-weight: 600; }
.header-actions { display: flex; gap: 0.375rem; }
.btn-sm { padding: 0.25rem 0.625rem; font-size: 0.8125rem; border: 2px solid; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; background: #fff; }
.btn-sm.btn-secondary { background: #6c757d; border-color: #6c757d; color: #fff; }

/* Upload Area */
.upload-area { background: linear-gradient(135deg, #fff9e6 0%, #fff 100%); border: 3px dashed #FFC107; padding: 2rem; margin-bottom: 1rem; text-align: center; transition: all 0.3s; }
.upload-area.dragover { background: #fff9e6; border-style: solid; transform: scale(1.01); }
.upload-icon { font-size: 3rem; color: #FFC107; margin-bottom: 0.5rem; animation: bounce 2s infinite; }
@keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
.upload-area h3 { font-size: 1rem; margin: 0 0 0.25rem; color: #000; }
.upload-area p { font-size: 0.75rem; color: #6c757d; margin: 0 0 1rem; }
.btn-upload { background: #FFC107; border: 2px solid #FFC107; color: #000; padding: 0.5rem 1.5rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.375rem; transition: all 0.2s; }
.btn-upload:hover { background: #000; color: #FFC107; }

.upload-progress { margin-top: 1rem; }
.progress-bar { height: 8px; background: #dee2e6; overflow: hidden; }
.progress-fill { height: 100%; background: #FFC107; width: 0; transition: width 0.3s; }
#progressText { font-size: 0.75rem; color: #6c757d; display: block; margin-top: 0.25rem; }

.upload-preview-grid { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; margin-top: 1rem; }
.preview-item { position: relative; width: 80px; height: 80px; }
.preview-item img { width: 100%; height: 100%; object-fit: cover; border: 2px solid #FFC107; }
.preview-item .remove { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; background: #dc3545; color: #fff; border: none; cursor: pointer; font-size: 0.625rem; display: flex; align-items: center; justify-content: center; }

/* Toolbar */
.toolbar { display: flex; gap: 1.5rem; justify-content: center; margin-bottom: 0.75rem; padding: 0.5rem; background: #f8f9fa; border: 2px solid #dee2e6; }
.toolbar-hint { font-size: 0.75rem; color: #6c757d; display: flex; align-items: center; gap: 0.375rem; }

/* Images Grid */
.images-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 0.75rem; }
.image-card { background: #fff; border: 2px solid #dee2e6; position: relative; transition: all 0.2s; cursor: move; }
.image-card:hover { border-color: #FFC107; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.image-card.dragging { opacity: 0.5; }
.drag-handle { position: absolute; top: 0; left: 0; width: 24px; height: 24px; background: rgba(0,0,0,0.6); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; z-index: 2; opacity: 0; transition: opacity 0.2s; }
.image-card:hover .drag-handle { opacity: 1; }
.image-thumb { height: 120px; overflow: hidden; cursor: pointer; position: relative; }
.image-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.image-card:hover .image-thumb img { transform: scale(1.05); }
.cover-badge { position: absolute; bottom: 0; left: 0; right: 0; background: #FFC107; color: #000; font-size: 0.625rem; font-weight: 600; padding: 0.125rem 0.375rem; text-align: center; }
.image-actions { display: flex; justify-content: center; gap: 0.25rem; padding: 0.375rem; border-top: 2px solid #dee2e6; }
.action-btn { width: 28px; height: 28px; border: 2px solid #dee2e6; background: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; color: #495057; transition: all 0.2s; }
.action-btn:hover { border-color: #FFC107; background: #fff9e6; }
.action-btn.danger:hover { border-color: #dc3545; background: #f8d7da; color: #dc3545; }
.action-btn:disabled { opacity: 0.3; cursor: not-allowed; }
.image-title { font-size: 0.6875rem; padding: 0.25rem 0.375rem; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; border-top: 1px solid #eee; color: #6c757d; }

/* Lightbox */
.lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.95); z-index: 9999; align-items: center; justify-content: center; }
.lightbox.active { display: flex; }
.lightbox-close { position: absolute; top: 1rem; right: 1rem; width: 40px; height: 40px; background: #FFC107; border: none; color: #000; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; }
.lightbox-nav { position: absolute; top: 50%; transform: translateY(-50%); width: 50px; height: 50px; background: rgba(255,193,7,0.9); border: none; color: #000; font-size: 1.25rem; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.lightbox-nav.prev { left: 1rem; }
.lightbox-nav.next { right: 1rem; }
.lightbox-content { max-width: 90%; max-height: 90%; text-align: center; }
.lightbox-content img { max-width: 100%; max-height: 80vh; object-fit: contain; }
.lightbox-caption { color: #fff; margin-top: 1rem; font-size: 0.875rem; }

/* Edit Modal */
.modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 9998; align-items: center; justify-content: center; padding: 1rem; }
.modal.active { display: flex; }
.modal-content { background: #fff; width: 100%; max-width: 400px; border: 2px solid #FFC107; }
.modal-header { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; border-bottom: 2px solid #FFC107; background: #fff9e6; }
.modal-header h3 { margin: 0; font-size: 0.875rem; display: flex; align-items: center; gap: 0.375rem; }
.modal-close { width: 28px; height: 28px; border: none; background: none; cursor: pointer; font-size: 1rem; }
.modal-body { padding: 0.75rem; }
.modal-footer { display: flex; justify-content: flex-end; gap: 0.5rem; padding: 0.75rem; border-top: 2px solid #dee2e6; }
.form-group { margin-bottom: 0.625rem; }
.form-group label { display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.25rem; }
.form-control { width: 100%; padding: 0.375rem 0.5rem; font-size: 0.8125rem; border: 2px solid #dee2e6; outline: none; box-sizing: border-box; }
.form-control:focus { border-color: #FFC107; }
.btn-primary, .btn-secondary { padding: 0.375rem 0.75rem; font-size: 0.8125rem; font-weight: 600; border: 2px solid; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; }
.btn-primary { background: #FFC107; border-color: #FFC107; color: #000; }
.btn-secondary { background: #6c757d; border-color: #6c757d; color: #fff; }

.empty-state { text-align: center; padding: 3rem 1rem; color: #6c757d; background: #fff; border: 2px solid #dee2e6; }
.empty-state i { font-size: 3rem; color: #dee2e6; margin-bottom: 1rem; display: block; }
.empty-state p { margin: 0.25rem 0; font-size: 0.875rem; }
.empty-state .hint { font-size: 0.75rem; color: #adb5bd; }

.alert { padding: 0.625rem 0.75rem; margin-bottom: 0.75rem; border: 2px solid; font-size: 0.8125rem; }
.alert-success { background: #d4edda; border-color: #28a745; color: #155724; }
.alert-danger { background: #f8d7da; border-color: #dc3545; color: #721c24; }

@media (max-width: 768px) {
    .content-header-compact { flex-direction: column; gap: 0.5rem; align-items: stretch; }
    .header-left { justify-content: space-between; }
    .header-actions { justify-content: flex-end; }
    .images-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 0.5rem; }
    .image-thumb { height: 100px; }
    .toolbar { flex-direction: column; gap: 0.25rem; }
    .lightbox-nav { width: 40px; height: 40px; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Image data for lightbox
const imageData = <?php echo json_encode(array_map(function($img) {
    return [
        'id' => $img['id'],
        'src' => '../uploads/' . $img['image_path'],
        'title' => $img['title'],
        'caption' => $img['caption'],
        'alt_text' => $img['alt_text']
    ];
}, $images)); ?>;

let currentImageIndex = 0;

// Drag & Drop Upload
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const previewGrid = document.getElementById('previewGrid');
const uploadForm = document.getElementById('uploadForm');
const uploadProgress = document.getElementById('uploadProgress');
const progressFill = document.getElementById('progressFill');
const progressText = document.getElementById('progressText');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(evt => {
    dropZone.addEventListener(evt, e => { e.preventDefault(); e.stopPropagation(); });
});

['dragenter', 'dragover'].forEach(evt => {
    dropZone.addEventListener(evt, () => dropZone.classList.add('dragover'));
});

['dragleave', 'drop'].forEach(evt => {
    dropZone.addEventListener(evt, () => dropZone.classList.remove('dragover'));
});

dropZone.addEventListener('drop', e => {
    const files = e.dataTransfer.files;
    handleFiles(files);
});

fileInput.addEventListener('change', e => {
    handleFiles(e.target.files);
});

function handleFiles(files) {
    if (files.length === 0) return;
    
    previewGrid.innerHTML = '';
    
    Array.from(files).forEach((file, i) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `<img src="${e.target.result}" alt=""><button type="button" class="remove" onclick="removePreview(${i})"><i class="fas fa-times"></i></button>`;
                previewGrid.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Auto submit with progress
    setTimeout(() => {
        uploadProgress.style.display = 'block';
        progressText.textContent = `Uploading ${files.length} image(s)...`;
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += 10;
            progressFill.style.width = Math.min(progress, 90) + '%';
        }, 200);
        
        uploadForm.submit();
    }, 500);
}

// Sortable Grid
<?php if (!empty($images)): ?>
const grid = document.getElementById('imagesGrid');
new Sortable(grid, {
    animation: 150,
    handle: '.drag-handle',
    ghostClass: 'dragging',
    onEnd: function() {
        const order = Array.from(grid.children).map(card => card.dataset.id);
        fetch('gallery-images.php?album=<?php echo $albumId; ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'action=update_order&order=' + encodeURIComponent(JSON.stringify(order))
        });
    }
});
<?php endif; ?>

// Lightbox
function openLightbox(imageId) {
    currentImageIndex = imageData.findIndex(img => img.id == imageId);
    if (currentImageIndex === -1) return;
    
    updateLightbox();
    document.getElementById('lightbox').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').classList.remove('active');
    document.body.style.overflow = '';
}

function navLightbox(dir) {
    currentImageIndex = (currentImageIndex + dir + imageData.length) % imageData.length;
    updateLightbox();
}

function updateLightbox() {
    const img = imageData[currentImageIndex];
    document.getElementById('lightboxImg').src = img.src;
    document.getElementById('lightboxCaption').innerHTML = img.title ? `<strong>${img.title}</strong>` + (img.caption ? `<br>${img.caption}` : '') : '';
}

document.addEventListener('keydown', e => {
    if (!document.getElementById('lightbox').classList.contains('active')) return;
    if (e.key === 'Escape') closeLightbox();
    if (e.key === 'ArrowLeft') navLightbox(-1);
    if (e.key === 'ArrowRight') navLightbox(1);
});

// Edit Modal
function editImage(imageId) {
    const img = imageData.find(i => i.id == imageId);
    if (!img) return;
    
    document.getElementById('editImageId').value = imageId;
    document.getElementById('editTitle').value = img.title || '';
    document.getElementById('editCaption').value = img.caption || '';
    document.getElementById('editAltText').value = img.alt_text || '';
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}
</script>

<?php include 'includes/footer.php'; ?>

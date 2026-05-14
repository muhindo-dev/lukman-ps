<?php
/**
 * News Blog Management - Add New Post
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

// Check authentication
requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'news';
$pageTitle = 'Add New Post';

$error = '';
$success = '';

// Blog categories
$categories = ['News', 'Stories', 'Updates', 'Impact Stories', 'Announcements'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? ''; // Rich text, don't strip HTML
    $tags = sanitizeInput($_POST['tags'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $publishDate = $_POST['publish_date'] ?? null;
    
    // Validation
    $validation = validateRequiredFields($_POST, ['title', 'content', 'category']);
    
    if (!$validation['success']) {
        $error = $validation['message'];
    } else {
        // Generate slug
        $slug = generateSlug($title);
        $slug = ensureUniqueSlug('news_posts', $slug);
        
        // Handle featured image upload
        $featuredImage = null;
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = uploadImage($_FILES['featured_image'], '../uploads/news/');
            if ($uploadResult['success']) {
                $featuredImage = 'news/' . $uploadResult['filename'];
            } else {
                $error = 'Image upload failed: ' . $uploadResult['message'];
            }
        }
        
        if (!$error) {
            // Prepare data
            $data = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'content' => $content,
                'featured_image' => $featuredImage,
                'author_id' => $currentAdmin['id'],
                'category' => $category,
                'tags' => $tags,
                'status' => $status,
                'published_at' => ($status === 'published' && $publishDate) ? $publishDate : null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Insert record
            $insertId = insertRecord('news_posts', $data);
            
            if ($insertId) {
                logAdminActivity('create', 'news_posts', $insertId);
                $success = 'Post created successfully!';
                
                // Redirect after 2 seconds
                header("refresh:2;url=news.php");
            } else {
                $error = 'Failed to create post. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-plus-circle"></i> Add New Post</h1>
        <a href="news.php" class="btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="newsForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-file-alt"></i> Post Content</h4>
                    </div>
                    <div class="card-body-compact">
                        <!-- Title -->
                        <div class="form-group mb-4">
                            <label for="title" class="form-label required">Post Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="form-control" 
                                placeholder="Enter post title..."
                                required
                                maxlength="200"
                                value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
                            >
                            <small class="form-text">Maximum 200 characters</small>
                        </div>

                        <!-- Excerpt -->
                        <div class="form-group mb-4">
                            <label for="excerpt" class="form-label">Excerpt / Summary</label>
                            <textarea 
                                id="excerpt" 
                                name="excerpt" 
                                class="form-control" 
                                rows="3"
                                placeholder="Brief summary of the post (optional)..."
                                maxlength="500"
                            ><?php echo htmlspecialchars($_POST['excerpt'] ?? ''); ?></textarea>
                            <small class="form-text">Optional. Used for previews and SEO. Maximum 500 characters.</small>
                        </div>

                        <!-- Content Editor -->
                        <div class="form-group mb-4">
                            <label for="content" class="form-label required">Full Content</label>
                            <input type="hidden" id="content" name="content" value="<?php echo htmlspecialchars($_POST['content'] ?? ''); ?>">
                            <div id="editor-container" style="height: 400px; background: #fff;"><?php echo $_POST['content'] ?? ''; ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Settings Card (Combined) -->
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-cog"></i> Post Settings</h4>
                    </div>
                    <div class="card-body-compact">
                        <!-- Status -->
                        <div class="form-group mb-3">
                            <label for="status" class="form-label required">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="draft" <?php echo (($_POST['status'] ?? '') == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo (($_POST['status'] ?? '') == 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo (($_POST['status'] ?? '') == 'archived') ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </div>

                        <!-- Publish Date -->
                        <div class="form-group mb-3">
                            <label for="publish_date" class="form-label">Publish Date</label>
                            <input 
                                type="datetime-local" 
                                id="publish_date" 
                                name="publish_date" 
                                class="form-control"
                                value="<?php echo htmlspecialchars($_POST['publish_date'] ?? date('Y-m-d\TH:i')); ?>"
                            >
                            <small class="form-text">Leave blank for immediate publish</small>
                        </div>

                        <hr class="my-3">

                        <!-- Category & Tags -->
                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="category" class="form-label required">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo (($_POST['category'] ?? '') == $cat) ? 'selected' : ''; ?>>
                                        <?php echo $cat; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tags -->
                        <div class="form-group">
                            <label for="tags" class="form-label">Tags</label>
                            <input 
                                type="text" 
                                id="tags" 
                                name="tags" 
                                class="form-control" 
                                placeholder="e.g., charity, children, education"
                                value="<?php echo htmlspecialchars($_POST['tags'] ?? ''); ?>"
                            >
                            <small class="form-text">Separate with commas</small>
                        </div>

                        <hr class="my-3">

                        <!-- Featured Image -->
                        <div class="form-group">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input 
                                type="file" 
                                id="featured_image" 
                                name="featured_image" 
                                class="form-control" 
                                accept="image/*"
                            >
                            <small class="form-text">1200x630px. Max 5MB</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <hr class="my-3">

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Post
                            </button>
                            <a href="news.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quill.js Editor -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Enter post content...',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Form validation
    document.getElementById('newsForm').addEventListener('submit', function(e) {
        // Get Quill content and update hidden input
        var content = quill.root.innerHTML;
        document.getElementById('content').value = content;
        
        // Check if content is empty (only has empty tags)
        var textContent = quill.getText().trim();
        if (!textContent || textContent === '') {
            e.preventDefault();
            alert('Please enter the post content');
            quill.focus();
            return false;
        }
    });

    // Image preview
    document.getElementById('featured_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; height: auto; border: 2px solid #dee2e6;">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
});
</script>

<style>
.content-header-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.375rem;
    border-bottom: 2px solid #FFC107;
}

.content-header-compact h1 {
    font-size: 1.25rem;
    margin: 0;
    font-weight: 700;
}

.header-actions {
    display: flex;
    gap: 0.375rem;
}

.btn-sm {
    padding: 0.25rem 0.625rem;
    font-size: 0.8125rem;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
}

.btn-sm.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.btn-sm.btn-danger {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

.card-compact {
    background: #fff;
    border: 2px solid #dee2e6;
    margin-bottom: 0.75rem;
}

.card-header-compact {
    padding: 0.5rem 0.75rem;
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.card-header-compact h4 {
    margin: 0;
    font-size: 0.9375rem;
    font-weight: 600;
}

.card-body-compact {
    padding: 0.75rem;
}

.form-group {
    margin-bottom: 0.75rem;
}

.form-label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
    font-size: 0.8125rem;
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 0.375rem 0.5rem;
    border: 2px solid #dee2e6;
    font-size: 0.8125rem;
}

.form-control:focus {
    outline: none;
    border-color: #FFC107;
    background: #fff9e6;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6c757d;
}

.btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    cursor: pointer;
}

.btn-primary {
    background: #FFC107;
    border-color: #FFC107;
    color: #000;
}

.btn-primary:hover {
    background: #000;
    color: #FFC107;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.d-grid {
    display: grid;
}

.gap-2 {
    gap: 0.5rem;
}

.my-3 {
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

hr {
    border: 0;
    border-top: 1px solid #dee2e6;
    margin: 0.75rem 0;
}

.alert {
    padding: 0.625rem 0.75rem;
    margin-bottom: 0.75rem;
    border: 2px solid;
    font-size: 0.8125rem;
}

.alert-danger {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}
</style>

<?php include 'includes/footer.php'; ?>

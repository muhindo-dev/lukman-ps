<?php
/**
 * News Blog Management - Edit Post
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

// Check authentication
requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'news';
$pageTitle = 'Edit Post';

$error = '';
$success = '';

// Get post ID
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$postId) {
    header('Location: news.php');
    exit;
}

// Get post data
$post = getRecordById('news_posts', $postId);

if (!$post) {
    header('Location: news.php');
    exit;
}

// Blog categories
$categories = ['News', 'Stories', 'Updates', 'Impact Stories', 'Announcements'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $category = sanitizeInput($_POST['category'] ?? '');
    $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $tags = sanitizeInput($_POST['tags'] ?? '');
    $status = $_POST['status'] ?? 'draft';
    $publishDate = $_POST['publish_date'] ?? null;
    
    // Validation
    $validation = validateRequiredFields($_POST, ['title', 'content', 'category']);
    
    if (!$validation['success']) {
        $error = $validation['message'];
    } else {
        // Generate slug if title changed
        $slug = $post['slug'];
        if ($title !== $post['title']) {
            $slug = generateSlug($title);
            $slug = ensureUniqueSlug('news_posts', $slug, $postId);
        }
        
        // Handle featured image upload
        $featuredImage = $post['featured_image'];
        if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if ($featuredImage) {
                deleteUploadedFile('../uploads/' . $featuredImage);
            }
            
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
                'category' => $category,
                'tags' => $tags,
                'status' => $status,
                'published_at' => ($status === 'published' && $publishDate) ? $publishDate : $post['published_at'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Update record
            $updated = updateRecord('news_posts', $postId, $data);
            
            if ($updated) {
                logAdminActivity('update', 'news_posts', $postId);
                $success = 'Post updated successfully!';
                
                // Refresh post data
                $post = getRecordById('news_posts', $postId);
            } else {
                $error = 'Failed to update post. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-edit"></i> Edit Post</h1>
        <div class="header-actions">
            <a href="news.php" class="btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="news-delete.php?id=<?php echo $postId; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this post?');">
                <i class="fas fa-trash"></i>
            </a>
        </div>
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
                                value="<?php echo htmlspecialchars($post['title']); ?>"
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
                            ><?php echo htmlspecialchars($post['excerpt']); ?></textarea>
                            <small class="form-text">Optional. Used for previews and SEO. Maximum 500 characters.</small>
                        </div>

                        <!-- Content Editor -->
                        <div class="form-group mb-4">
                            <label for="content" class="form-label required">Full Content</label>
                            <input type="hidden" id="content" name="content" value="<?php echo htmlspecialchars($post['content']); ?>">
                            <div id="editor-container" style="height: 400px; background: #fff;"><?php echo $post['content']; ?></div>
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
                                <option value="draft" <?php echo ($post['status'] == 'draft') ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo ($post['status'] == 'published') ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo ($post['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
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
                                value="<?php echo $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : date('Y-m-d\TH:i'); ?>"
                            >
                        </div>

                        <hr class="my-3">

                        <!-- Category & Tags -->
                        <!-- Category -->
                        <div class="form-group mb-3">
                            <label for="category" class="form-label required">Category</label>
                            <select id="category" name="category" class="form-control" required>
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat; ?>" <?php echo ($post['category'] == $cat) ? 'selected' : ''; ?>>
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
                                value="<?php echo htmlspecialchars($post['tags']); ?>"
                            >
                            <small class="form-text">Separate with commas</small>
                        </div>

                        <hr class="my-3">

                        <!-- Featured Image -->
                        <?php if ($post['featured_image']): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image:</label>
                                <img src="../uploads/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="" style="max-width: 100%; height: auto; border: 2px solid #dee2e6; margin-bottom: 0.5rem;">
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="featured_image" class="form-label">Featured Image</label>
                            <input 
                                type="file" 
                                id="featured_image" 
                                name="featured_image" 
                                class="form-control" 
                                accept="image/*"
                            >
                            <small class="form-text"><?php echo $post['featured_image'] ? 'Upload new or keep current. ' : ''; ?>1200x630px. Max 5MB</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <hr class="my-3">

                        <!-- Post Info -->
                        <div class="form-group">
                            <div class="post-meta">
                                <small><strong>Created:</strong> <?php echo formatDateTime($post['created_at']); ?></small><br>
                                <small><strong>Views:</strong> <?php echo number_format($post['views']); ?> | <strong>Slug:</strong> <?php echo htmlspecialchars($post['slug']); ?></small>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Post
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

    // SEO Preview Updates
    const titleInput = document.getElementById('title');
    const excerptInput = document.getElementById('excerpt');
    
    titleInput.addEventListener('input', function() {
        const title = this.value || 'Your Post Title Here';
        document.getElementById('seoTitle').textContent = title;
        
        // Generate slug preview
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/[\s-]+/g, '-')
            .trim();
        document.getElementById('seoUrl').textContent = 'ulfa.org/news/' + (slug || '<?php echo $post['slug']; ?>');
    });
    
    excerptInput.addEventListener('input', function() {
        const excerpt = this.value || 'Your excerpt will appear here as a search result description...';
        document.getElementById('seoDescription').textContent = excerpt;
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

.post-meta {
    font-size: 0.75rem;
    color: #6c757d;
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

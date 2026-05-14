<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'downloads';
$pageTitle   = 'Add Download';

$pdo   = getDBConnection();
$error = '';

$categories = ['fees','circulars','academic','forms','routine','general'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(strip_tags($_POST['title']       ?? ''));
    $description = trim(strip_tags($_POST['description'] ?? ''));
    $category    = in_array($_POST['category'] ?? '', $categories) ? $_POST['category'] : 'general';
    $status      = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';

    if (!$title) $error = 'Title is required.';
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'A file is required.';
    }

    $filePath = null; $fileType = null; $fileSize = null;
    if (!$error) {
        $origName = $_FILES['file']['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','zip'];
        if (!in_array($ext, $allowed)) {
            $error = 'File type not allowed. Allowed: ' . implode(', ', $allowed);
        } else {
            $dir = '../uploads/downloads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'dl_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dir . $fname)) {
                $filePath = 'downloads/' . $fname;
                $fileType = $ext;
                $fileSize = $_FILES['file']['size'];
            } else {
                $error = 'File upload failed.';
            }
        }
    }

    if (!$error) {
        $pdo->prepare("INSERT INTO downloads (title, description, file_path, file_type, file_size, category, status) VALUES (?,?,?,?,?,?,?)")
            ->execute([$title, $description, $filePath, $fileType, $fileSize, $category, $status]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Download added successfully.'];
        header('Location: downloads.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add Download</h1>
    <a href="downloads.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Title <span class="required">*</span></label>
        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
                <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo ($_POST['category'] ?? 'general') === $c ? 'selected' : ''; ?>><?php echo ucfirst($c); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active" <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active (visible)</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>File <span class="required">*</span></label>
        <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
        <small class="form-text">Allowed: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG, ZIP</small>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Download</button>
        <a href="downloads.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

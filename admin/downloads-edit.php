<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'downloads';
$pageTitle   = 'Edit Download';

$pdo = getDBConnection();
$id  = (int)($_GET['id'] ?? 0);

$r = $pdo->prepare("SELECT * FROM downloads WHERE id = ?");
$r->execute([$id]);
$dl = $r->fetch(PDO::FETCH_ASSOC);
if (!$dl) { header('Location: downloads.php'); exit; }

$categories = ['fees','circulars','academic','forms','routine','general'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim(strip_tags($_POST['title']       ?? ''));
    $description = trim(strip_tags($_POST['description'] ?? ''));
    $category    = in_array($_POST['category'] ?? '', $categories) ? $_POST['category'] : 'general';
    $status      = ($_POST['status'] ?? '') === 'active' ? 'active' : 'inactive';

    if (!$title) $error = 'Title is required.';

    $filePath = $dl['file_path'];
    $fileType = $dl['file_type'];
    $fileSize = $dl['file_size'];

    if (!$error && isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $origName = $_FILES['file']['name'];
        $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed  = ['pdf','doc','docx','xls','xlsx','ppt','pptx','jpg','jpeg','png','zip'];
        if (!in_array($ext, $allowed)) {
            $error = 'File type not allowed.';
        } else {
            $dir = '../uploads/downloads/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'dl_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $dir . $fname)) {
                if ($dl['file_path'] && file_exists('../uploads/' . $dl['file_path'])) {
                    @unlink('../uploads/' . $dl['file_path']);
                }
                $filePath = 'downloads/' . $fname;
                $fileType = $ext;
                $fileSize = $_FILES['file']['size'];
            } else {
                $error = 'File upload failed.';
            }
        }
    }

    if (!$error) {
        $pdo->prepare("UPDATE downloads SET title=?, description=?, file_path=?, file_type=?, file_size=?, category=?, status=? WHERE id=?")
            ->execute([$title, $description, $filePath, $fileType, $fileSize, $category, $status, $id]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Download updated.'];
        header('Location: downloads.php'); exit;
    }

    $dl = array_merge($dl, compact('title','description','category','status'));
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-edit"></i> Edit Download</h1>
    <a href="downloads.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Title <span class="required">*</span></label>
        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($dl['title']); ?>">
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($dl['description'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
                <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $dl['category'] === $c ? 'selected' : ''; ?>><?php echo ucfirst($c); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo $dl['status'] === 'active'   ? 'selected' : ''; ?>>Active (visible)</option>
                <option value="inactive" <?php echo $dl['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Replace File</label>
        <?php if ($dl['file_path']): ?>
        <div class="mb-2 text-muted small"><i class="fas fa-file me-1"></i> Current: <?php echo htmlspecialchars(basename($dl['file_path'])); ?> (<?php echo $dl['file_size'] ? round($dl['file_size']/1024,1).' KB' : '?'; ?>)</div>
        <?php endif; ?>
        <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.zip">
        <small class="form-text">Leave blank to keep existing file.</small>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Update Download</button>
        <a href="downloads.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

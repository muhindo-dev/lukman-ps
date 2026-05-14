<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentAdmin = getCurrentAdmin();
$currentPage  = 'testimonials';
$pageTitle    = 'Add Testimonial';

$pdo   = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim(strip_tags($_POST['name']         ?? ''));
    $role         = trim(strip_tags($_POST['role']         ?? ''));
    $relationship = trim(strip_tags($_POST['relationship'] ?? ''));
    $content      = trim(strip_tags($_POST['content']      ?? ''));
    $rating       = max(1, min(5, (int)($_POST['rating']   ?? 5)));
    $status       = in_array($_POST['status'] ?? '', ['approved','pending','rejected']) ? $_POST['status'] : 'pending';

    if (!$name)    $error = 'Name is required.';
    if (!$content) $error = 'Testimonial content is required.';

    $photoPath = null;
    if (!$error && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $dir = '../uploads/testimonials/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'testi_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $fname)) {
                $photoPath = 'testimonials/' . $fname;
            }
        } else {
            $error = 'Photo must be JPG, PNG, or WebP.';
        }
    }

    if (!$error) {
        $pdo->prepare("INSERT INTO testimonials (name, role, relationship_type, content, rating, photo, status) VALUES (?,?,?,?,?,?,?)")
            ->execute([$name, $role, $relationship, $content, $rating, $photoPath, $status]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Testimonial added successfully.'];
        header('Location: testimonials.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add Testimonial</h1>
    <a href="testimonials.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Name <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Role / Title</label>
            <input type="text" name="role" class="form-control" placeholder="e.g. Parent of P5 Pupil" value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Relationship</label>
            <select name="relationship" class="form-control">
                <option value="">— Select —</option>
                <?php foreach (['parent','former_pupil','teacher','community_member','other'] as $r): ?>
                <option value="<?php echo $r; ?>" <?php echo ($_POST['relationship'] ?? '') === $r ? 'selected' : ''; ?>><?php echo ucwords(str_replace('_',' ',$r)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Star Rating</label>
            <select name="rating" class="form-control">
                <?php for ($i=5;$i>=1;$i--): ?>
                <option value="<?php echo $i; ?>" <?php echo (int)($_POST['rating'] ?? 5) === $i ? 'selected' : ''; ?>><?php echo $i; ?> Star<?php echo $i>1?'s':''; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Testimonial Content <span class="required">*</span></label>
        <textarea name="content" class="form-control" rows="5" required><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Photo (optional)</label>
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
            <small class="form-text">JPG/PNG/WebP, max 2MB</small>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="approved" <?php echo ($_POST['status'] ?? '') === 'approved' ? 'selected' : ''; ?>>Approved (visible)</option>
                <option value="pending"  <?php echo ($_POST['status'] ?? 'pending') === 'pending' ? 'selected' : ''; ?>>Pending review</option>
                <option value="rejected" <?php echo ($_POST['status'] ?? '') === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Testimonial</button>
        <a href="testimonials.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

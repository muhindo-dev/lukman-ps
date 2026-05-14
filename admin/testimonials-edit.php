<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'testimonials';
$pageTitle   = 'Edit Testimonial';

$pdo = getDBConnection();
$id  = (int)($_GET['id'] ?? 0);

$row = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$row->execute([$id]);
$testi = $row->fetch(PDO::FETCH_ASSOC);
if (!$testi) { header('Location: testimonials.php'); exit; }

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

    $photoPath = $testi['photo']; // keep existing by default
    if (!$error && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $dir = '../uploads/testimonials/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'testi_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dir . $fname)) {
                // remove old photo
                if ($testi['photo'] && file_exists('../uploads/' . $testi['photo'])) {
                    @unlink('../uploads/' . $testi['photo']);
                }
                $photoPath = 'testimonials/' . $fname;
            }
        } else {
            $error = 'Photo must be JPG, PNG, or WebP.';
        }
    }

    if (!$error) {
        if (isset($_POST['remove_photo']) && $_POST['remove_photo'] === '1') {
            if ($testi['photo'] && file_exists('../uploads/' . $testi['photo'])) {
                @unlink('../uploads/' . $testi['photo']);
            }
            $photoPath = null;
        }

        $pdo->prepare("UPDATE testimonials SET name=?, role=?, relationship_type=?, content=?, rating=?, photo=?, status=?, updated_at=NOW() WHERE id=?")
            ->execute([$name, $role, $relationship, $content, $rating, $photoPath, $status, $id]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Testimonial updated successfully.'];
        header('Location: testimonials.php'); exit;
    }

    // repopulate with POST values on error
    $testi = array_merge($testi, compact('name','role','relationship','content','rating','status'));
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-edit"></i> Edit Testimonial</h1>
    <a href="testimonials.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Name <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($testi['name']); ?>">
        </div>
        <div class="form-group">
            <label>Role / Title</label>
            <input type="text" name="role" class="form-control" placeholder="e.g. Parent of P5 Pupil" value="<?php echo htmlspecialchars($testi['role'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Relationship</label>
            <select name="relationship" class="form-control">
                <option value="">— Select —</option>
                <?php foreach (['parent','former_pupil','teacher','community_member','other'] as $r): ?>
                <option value="<?php echo $r; ?>" <?php echo ($testi['relationship_type'] ?? '') === $r ? 'selected' : ''; ?>><?php echo ucwords(str_replace('_',' ',$r)); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Star Rating</label>
            <select name="rating" class="form-control">
                <?php for ($i=5;$i>=1;$i--): ?>
                <option value="<?php echo $i; ?>" <?php echo (int)$testi['rating'] === $i ? 'selected' : ''; ?>><?php echo $i; ?> Star<?php echo $i>1?'s':''; ?></option>
                <?php endfor; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Testimonial Content <span class="required">*</span></label>
        <textarea name="content" class="form-control" rows="5" required><?php echo htmlspecialchars($testi['content']); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Photo</label>
            <?php if (!empty($testi['photo'])): ?>
            <div class="mb-2">
                <img src="../uploads/<?php echo htmlspecialchars($testi['photo']); ?>" alt="Current photo" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid #ddd;">
                <label class="ms-2"><input type="checkbox" name="remove_photo" value="1"> Remove current photo</label>
            </div>
            <?php endif; ?>
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
            <small class="form-text">Leave blank to keep existing. JPG/PNG/WebP, max 2MB</small>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="approved" <?php echo $testi['status'] === 'approved' ? 'selected' : ''; ?>>Approved (visible)</option>
                <option value="pending"  <?php echo $testi['status'] === 'pending'  ? 'selected' : ''; ?>>Pending review</option>
                <option value="rejected" <?php echo $testi['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Update Testimonial</button>
        <a href="testimonials.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

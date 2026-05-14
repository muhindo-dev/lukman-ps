<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'hifdh';
$pageTitle   = 'Edit Ḥifẓ Achievement';

$pdo   = getDBConnection();
$error = '';
$milestones = ['juz1','juz5','juz10','juz15','juz20','half_hifdh','full_hifdh'];
$milestoneLabels = [
    'juz1'=>"Juz' 1",'juz5'=>"Juz' 5",'juz10'=>"Juz' 10",'juz15'=>"Juz' 15",
    'juz20'=>"Juz' 20",'half_hifdh'=>'Half Ḥifẓ (15 Juz\')','full_hifdh'=>'Full Ḥifẓ (Complete Qur\'an)',
];

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM hifdh_achievements WHERE id = ?");
$stmt->execute([$id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$item) { header('Location: hifdh.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pupil_name   = trim(strip_tags($_POST['pupil_name']   ?? ''));
    $class        = trim(strip_tags($_POST['class']        ?? ''));
    $milestone    = in_array($_POST['milestone'] ?? '', $milestones) ? $_POST['milestone'] : $item['milestone'];
    $date_achieved= $_POST['date_achieved'] ?? $item['date_achieved'];
    $testimonial  = trim(strip_tags($_POST['testimonial']  ?? ''));
    $display_order= (int)($_POST['display_order'] ?? 0);
    $status       = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$pupil_name) $error = 'Pupil name is required.';

    // Handle new photo upload
    $photo = $item['photo'];
    if (!$error && !empty($_FILES['photo']['name'])) {
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) {
            $error = 'Photo must be JPG, PNG, or WebP.';
        } elseif ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
            $error = 'Photo must be under 3 MB.';
        } else {
            $filename = 'hifdh/' . uniqid('hifdh_') . '.' . $ext;
            $dest     = '../uploads/' . $filename;
            if (!is_dir('../uploads/hifdh')) @mkdir('../uploads/hifdh', 0755, true);
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $dest)) {
                // Remove old photo
                if ($photo && file_exists('../uploads/' . $photo)) @unlink('../uploads/' . $photo);
                $photo = $filename;
            } else {
                $error = 'Failed to save photo.';
            }
        }
    }

    // Remove photo if checked
    if (!$error && isset($_POST['remove_photo']) && $photo) {
        if (file_exists('../uploads/' . $photo)) @unlink('../uploads/' . $photo);
        $photo = '';
    }

    if (!$error) {
        $pdo->prepare("UPDATE hifdh_achievements SET pupil_name=?,class=?,milestone=?,date_achieved=?,photo=?,testimonial=?,display_order=?,status=? WHERE id=?")
            ->execute([$pupil_name, $class, $milestone, $date_achieved, $photo, $testimonial, $display_order, $status, $id]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Achievement updated.'];
        header('Location: hifdh.php'); exit;
    }
    $item = array_merge($item, compact('pupil_name','class','milestone','date_achieved','testimonial','display_order','status','photo'));
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-edit"></i> Edit Ḥifẓ Achievement</h1>
    <a href="hifdh.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Pupil Name <span class="required">*</span></label>
            <input type="text" name="pupil_name" class="form-control" required maxlength="150"
                   value="<?php echo htmlspecialchars($item['pupil_name']); ?>">
        </div>
        <div class="form-group">
            <label>Class / Year</label>
            <input type="text" name="class" class="form-control" maxlength="30"
                   value="<?php echo htmlspecialchars($item['class'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Milestone <span class="required">*</span></label>
            <select name="milestone" class="form-control">
                <?php foreach ($milestoneLabels as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo $item['milestone'] === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date Achieved <span class="required">*</span></label>
            <input type="date" name="date_achieved" class="form-control" required
                   value="<?php echo htmlspecialchars($item['date_achieved']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label>Congratulatory Note</label>
        <textarea name="testimonial" class="form-control" rows="3"><?php echo htmlspecialchars($item['testimonial'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Photo</label>
            <?php if (!empty($item['photo']) && file_exists('../uploads/' . $item['photo'])): ?>
            <div class="mb-2">
                <img src="../uploads/<?php echo htmlspecialchars($item['photo']); ?>"
                     alt="Current photo" style="width:64px;height:64px;border-radius:50%;object-fit:cover;border:2px solid #EA1B27;">
                <div class="form-check mt-1">
                    <input type="checkbox" class="form-check-input" name="remove_photo" id="remove_photo" value="1">
                    <label class="form-check-label text-danger" for="remove_photo">Remove current photo</label>
                </div>
            </div>
            <?php endif; ?>
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
            <small class="form-text">Upload a new photo to replace the current one.</small>
        </div>
        <div class="form-group">
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" min="0"
                   value="<?php echo (int)$item['display_order']; ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo $item['status'] === 'active'   ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $item['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Update Achievement</button>
        <a href="hifdh.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

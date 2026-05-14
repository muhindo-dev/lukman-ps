<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'hifdh';
$pageTitle   = 'Add Ḥifẓ Achievement';

$pdo   = getDBConnection();
$error = '';
$milestones = ['juz1','juz5','juz10','juz15','juz20','half_hifdh','full_hifdh'];
$milestoneLabels = [
    'juz1'=>"Juz' 1",'juz5'=>"Juz' 5",'juz10'=>"Juz' 10",'juz15'=>"Juz' 15",
    'juz20'=>"Juz' 20",'half_hifdh'=>'Half Ḥifẓ (15 Juz\')','full_hifdh'=>'Full Ḥifẓ (Complete Qur\'an)',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pupil_name   = trim(strip_tags($_POST['pupil_name']   ?? ''));
    $class        = trim(strip_tags($_POST['class']        ?? ''));
    $milestone    = in_array($_POST['milestone'] ?? '', $milestones) ? $_POST['milestone'] : 'juz1';
    $date_achieved= $_POST['date_achieved'] ?? date('Y-m-d');
    $testimonial  = trim(strip_tags($_POST['testimonial']  ?? ''));
    $display_order= (int)($_POST['display_order'] ?? 0);
    $status       = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$pupil_name)    $error = 'Pupil name is required.';
    if (!$date_achieved) $error = 'Date achieved is required.';

    // Handle photo upload
    $photo = '';
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
                $photo = $filename;
            } else {
                $error = 'Failed to save photo.';
            }
        }
    }

    if (!$error) {
        $pdo->prepare("INSERT INTO hifdh_achievements (pupil_name, class, milestone, date_achieved, photo, testimonial, display_order, status) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$pupil_name, $class, $milestone, $date_achieved, $photo, $testimonial, $display_order, $status]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => htmlspecialchars($pupil_name) . '\'s achievement recorded.'];
        header('Location: hifdh.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add Ḥifẓ Achievement</h1>
    <a href="hifdh.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Pupil Name <span class="required">*</span></label>
            <input type="text" name="pupil_name" class="form-control" required maxlength="150"
                   placeholder="e.g. Ahmad Sserunjogi"
                   value="<?php echo htmlspecialchars($_POST['pupil_name'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Class / Year</label>
            <input type="text" name="class" class="form-control" maxlength="30"
                   placeholder="e.g. P.7 or Class of 2025"
                   value="<?php echo htmlspecialchars($_POST['class'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Milestone <span class="required">*</span></label>
            <select name="milestone" class="form-control">
                <?php foreach ($milestoneLabels as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo ($_POST['milestone'] ?? 'juz1') === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date Achieved <span class="required">*</span></label>
            <input type="date" name="date_achieved" class="form-control" required
                   value="<?php echo htmlspecialchars($_POST['date_achieved'] ?? date('Y-m-d')); ?>">
        </div>
    </div>
    <div class="form-group">
        <label>Congratulatory Note <small class="text-muted">(optional — shown on the Wall of Fame)</small></label>
        <textarea name="testimonial" class="form-control" rows="3"
                  placeholder="e.g. May Allah accept your efforts and make the Qur'an a guide for your life."><?php echo htmlspecialchars($_POST['testimonial'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Pupil Photo <small class="text-muted">(optional, JPG/PNG/WebP, max 3 MB)</small></label>
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp">
        </div>
        <div class="form-group">
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" min="0"
                   value="<?php echo (int)($_POST['display_order'] ?? 0); ?>">
            <small class="form-text">Lower = shown first within the same milestone.</small>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo ($_POST['status'] ?? 'active') === 'active'   ? 'selected' : ''; ?>>Active (visible)</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive (hidden)</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Achievement</button>
        <a href="hifdh.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'notices';
$pageTitle   = 'Add Notice';

$pdo   = getDBConnection();
$error = '';
$types = ['info','warning','urgent','event'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title      = trim(strip_tags($_POST['title']      ?? ''));
    $content    = trim(strip_tags($_POST['content']    ?? ''));
    $type       = in_array($_POST['type'] ?? '', $types) ? $_POST['type'] : 'info';
    $start_date = $_POST['start_date'] ?? date('Y-m-d');
    $end_date   = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
    $is_pinned  = isset($_POST['is_pinned']) ? 1 : 0;
    $status     = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$title)      $error = 'Title is required.';
    if (!$start_date) $error = 'Start date is required.';

    if (!$error) {
        $pdo->prepare("INSERT INTO notices (title, content, type, start_date, end_date, is_pinned, status) VALUES (?,?,?,?,?,?,?)")
            ->execute([$title, $content, $type, $start_date, $end_date, $is_pinned, $status]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Notice added successfully.'];
        header('Location: notices.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add Notice</h1>
    <a href="notices.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST">
    <div class="form-group">
        <label>Title <span class="required">*</span></label>
        <input type="text" name="title" class="form-control" required
               value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
               placeholder="e.g. Term 2 timetable now available">
    </div>
    <div class="form-group">
        <label>Content / Details</label>
        <textarea name="content" class="form-control" rows="4"
                  placeholder="Optional extra detail shown when expanded…"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control">
                <?php foreach ($types as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo ($_POST['type'] ?? 'info') === $t ? 'selected' : ''; ?>>
                    <?php echo ucfirst($t); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Start Date <span class="required">*</span></label>
            <input type="date" name="start_date" class="form-control" required
                   value="<?php echo htmlspecialchars($_POST['start_date'] ?? date('Y-m-d')); ?>">
        </div>
        <div class="form-group">
            <label>End Date <small class="text-muted">(optional)</small></label>
            <input type="date" name="end_date" class="form-control"
                   value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
            <small class="form-text">Leave blank to show indefinitely.</small>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="form-group d-flex align-items-end">
            <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" value="1"
                       <?php echo isset($_POST['is_pinned']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="is_pinned">
                    <i class="fas fa-thumbtack me-1 text-warning"></i> Pin to top of notice strip
                </label>
            </div>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Notice</button>
        <a href="notices.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

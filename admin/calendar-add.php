<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'calendar';
$pageTitle   = 'Add Calendar Event';

$pdo   = getDBConnection();
$error = '';
$eventTypes = ['term','holiday','exam','event','meeting','other'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim(strip_tags($_POST['title']       ?? ''));
    $term      = in_array($_POST['term']??'', ['1','2','3']) ? (int)$_POST['term'] : null;
    $startDate = $_POST['start_date'] ?? '';
    $endDate   = $_POST['end_date']   ?? '';
    $desc      = trim(strip_tags($_POST['description'] ?? ''));
    $eventType = in_array($_POST['event_type']??'',$eventTypes) ? $_POST['event_type'] : 'event';
    $status    = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$title)     $error = 'Title is required.';
    if (!$startDate) $error = 'Start date is required.';

    if (!$error) {
        $pdo->prepare("INSERT INTO school_calendar (title, term, start_date, end_date, description, event_type, status) VALUES (?,?,?,?,?,?,?)")
            ->execute([$title, $term, $startDate, $endDate ?: null, $desc, $eventType, $status]);
        $_SESSION['alert'] = ['type'=>'success','message'=>'Calendar event added.'];
        header('Location: calendar.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add Calendar Event</h1>
    <a href="calendar.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST">
    <div class="form-group">
        <label>Title <span class="required">*</span></label>
        <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Term</label>
            <select name="term" class="form-control">
                <option value="">— Not term-specific —</option>
                <?php foreach ([1,2,3] as $t): ?>
                <option value="<?php echo $t; ?>" <?php echo ($_POST['term'] ?? '') == $t ? 'selected' : ''; ?>>Term <?php echo $t; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Event Type</label>
            <select name="event_type" class="form-control">
                <?php foreach ($eventTypes as $et): ?>
                <option value="<?php echo $et; ?>" <?php echo ($_POST['event_type'] ?? 'event') === $et ? 'selected' : ''; ?>><?php echo ucfirst($et); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Start Date <span class="required">*</span></label>
            <input type="date" name="start_date" class="form-control" required value="<?php echo htmlspecialchars($_POST['start_date'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($_POST['end_date'] ?? ''); ?>">
            <small class="form-text">Leave blank for single-day events.</small>
        </div>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Event</button>
        <a href="calendar.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

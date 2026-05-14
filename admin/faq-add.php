<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'faq';
$pageTitle   = 'Add FAQ';

$pdo   = getDBConnection();
$error = '';
$categories = ['general','admissions','fees','boarding','curriculum','ple'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim(strip_tags($_POST['question']       ?? ''));
    $answer   = trim($_POST['answer']                    ?? '');
    $category = in_array($_POST['category']??'',$categories) ? $_POST['category'] : 'general';
    $order    = (int)($_POST['display_order']            ?? 0);
    $status   = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$question) $error = 'Question is required.';
    if (!$answer)   $error = 'Answer is required.';

    if (!$error) {
        $pdo->prepare("INSERT INTO faq_items (question, answer, category, display_order, status) VALUES (?,?,?,?,?)")
            ->execute([$question, $answer, $category, $order, $status]);
        $_SESSION['alert'] = ['type'=>'success','message'=>'FAQ item added.'];
        header('Location: faq.php'); exit;
    }
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add FAQ Item</h1>
    <a href="faq.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST">
    <div class="form-group">
        <label>Question <span class="required">*</span></label>
        <input type="text" name="question" class="form-control" required value="<?php echo htmlspecialchars($_POST['question'] ?? ''); ?>">
    </div>
    <div class="form-group">
        <label>Answer <span class="required">*</span></label>
        <textarea name="answer" class="form-control" rows="6" required><?php echo htmlspecialchars($_POST['answer'] ?? ''); ?></textarea>
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
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" min="0" value="<?php echo (int)($_POST['display_order'] ?? 0); ?>">
            <small class="form-text">Lower number = shown first within category.</small>
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo ($_POST['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo ($_POST['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save FAQ</button>
        <a href="faq.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

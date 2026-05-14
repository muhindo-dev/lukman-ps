<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'faq';
$pageTitle   = 'Edit FAQ';

$pdo = getDBConnection();
$id  = (int)($_GET['id'] ?? 0);

$r = $pdo->prepare("SELECT * FROM faq_items WHERE id = ?");
$r->execute([$id]);
$faq = $r->fetch(PDO::FETCH_ASSOC);
if (!$faq) { header('Location: faq.php'); exit; }

$categories = ['general','admissions','fees','boarding','curriculum','ple'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question = trim(strip_tags($_POST['question'] ?? ''));
    $answer   = trim($_POST['answer']              ?? '');
    $category = in_array($_POST['category']??'',$categories) ? $_POST['category'] : 'general';
    $order    = (int)($_POST['display_order']      ?? 0);
    $status   = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    if (!$question) $error = 'Question is required.';
    if (!$answer)   $error = 'Answer is required.';

    if (!$error) {
        $pdo->prepare("UPDATE faq_items SET question=?, answer=?, category=?, display_order=?, status=? WHERE id=?")
            ->execute([$question, $answer, $category, $order, $status, $id]);
        $_SESSION['alert'] = ['type'=>'success','message'=>'FAQ item updated.'];
        header('Location: faq.php'); exit;
    }

    $faq = array_merge($faq, compact('question','answer','category','order','status'));
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-edit"></i> Edit FAQ Item</h1>
    <a href="faq.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST">
    <div class="form-group">
        <label>Question <span class="required">*</span></label>
        <input type="text" name="question" class="form-control" required value="<?php echo htmlspecialchars($faq['question']); ?>">
    </div>
    <div class="form-group">
        <label>Answer <span class="required">*</span></label>
        <textarea name="answer" class="form-control" rows="6" required><?php echo htmlspecialchars($faq['answer']); ?></textarea>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Category</label>
            <select name="category" class="form-control">
                <?php foreach ($categories as $c): ?>
                <option value="<?php echo $c; ?>" <?php echo $faq['category'] === $c ? 'selected' : ''; ?>><?php echo ucfirst($c); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Display Order</label>
            <input type="number" name="display_order" class="form-control" min="0" value="<?php echo (int)$faq['display_order']; ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="active"   <?php echo $faq['status'] === 'active'   ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $faq['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Update FAQ</button>
        <a href="faq.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

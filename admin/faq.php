<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'faq';
$pageTitle   = 'FAQ';

$pdo = getDBConnection();

// Toggle status
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $r = $pdo->prepare("SELECT status FROM faq_items WHERE id = ?");
    $r->execute([$tid]);
    $cur = $r->fetchColumn();
    $new = $cur === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE faq_items SET status = ? WHERE id = ?")->execute([$new, $tid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Status updated.'];
    header('Location: faq.php'); exit;
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM faq_items WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'FAQ item deleted.'];
    header('Location: faq.php'); exit;
}

$category = $_GET['category'] ?? '';
$status   = $_GET['status']   ?? '';
$search   = trim($_GET['q']   ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 25;
$offset   = ($page - 1) * $perPage;

$where = []; $params = [];
if ($category) { $where[] = 'category = ?'; $params[] = $category; }
if ($status)   { $where[] = 'status = ?';   $params[] = $status; }
if ($search)   { $where[] = '(question LIKE ? OR answer LIKE ?)';
                 $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM faq_items $whereSQL");
$total->execute($params);
$totalRows  = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$rows = $pdo->prepare("SELECT * FROM faq_items $whereSQL ORDER BY category, display_order ASC, id ASC LIMIT $perPage OFFSET $offset");
$rows->execute($params);
$faqs = $rows->fetchAll(PDO::FETCH_ASSOC);

$statsQ = $pdo->query("SELECT status, COUNT(*) c FROM faq_items GROUP BY status");
$stats  = $statsQ->fetchAll(PDO::FETCH_KEY_PAIR);
$categories = ['general','admissions','fees','boarding','curriculum','ple'];

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div><h1><i class="fas fa-question-circle"></i> FAQ</h1></div>
    <a href="faq-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add FAQ</a>
</div>

<div class="stats-bar">
    <div class="stat-chip"><span><?php echo $stats['active'] ?? 0; ?></span> Active</div>
    <div class="stat-chip warning"><span><?php echo $stats['inactive'] ?? 0; ?></span> Inactive</div>
    <div class="stat-chip"><span><?php echo $totalRows; ?></span> Shown</div>
</div>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="q" class="filter-input" placeholder="Search questions..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="category" class="filter-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $category===$c?'selected':''; ?>><?php echo ucfirst($c); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active"   <?php echo $status==='active'?'selected':''; ?>>Active</option>
            <option value="inactive" <?php echo $status==='inactive'?'selected':''; ?>>Inactive</option>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Filter</button>
        <a href="faq.php" class="filter-reset">Reset</a>
    </form>
</div>

<div class="table-wrapper">
<?php if ($faqs): ?>
<table class="admin-table">
    <thead>
        <tr><th>#</th><th>Question</th><th>Category</th><th>Order</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($faqs as $i => $faq): ?>
    <tr>
        <td><?php echo $offset + $i + 1; ?></td>
        <td>
            <strong><?php echo htmlspecialchars($faq['question']); ?></strong>
            <div class="text-muted small"><?php echo htmlspecialchars(substr(strip_tags($faq['answer']), 0, 80)) . (strlen($faq['answer']) > 80 ? '…' : ''); ?></div>
        </td>
        <td><span class="badge-info"><?php echo ucfirst(htmlspecialchars($faq['category'] ?? '')); ?></span></td>
        <td><?php echo (int)$faq['display_order']; ?></td>
        <td>
            <?php if ($faq['status'] === 'active'): ?>
            <span class="badge-success">Active</span>
            <?php else: ?>
            <span class="badge-danger">Inactive</span>
            <?php endif; ?>
        </td>
        <td>
            <div class="action-btns">
                <a href="faq-edit.php?id=<?php echo $faq['id']; ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="?toggle=<?php echo $faq['id']; ?>" class="btn-toggle" title="Toggle" onclick="return confirm('Toggle status?')"><i class="fas fa-toggle-<?php echo $faq['status']==='active'?'on':'off'; ?>"></i></a>
                <a href="?delete=<?php echo $faq['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this FAQ item?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-question-circle fa-3x"></i>
    <p>No FAQ items found.</p>
    <a href="faq-add.php" class="btn-add-new">Add First FAQ</a>
</div>
<?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?php echo $p; ?>&q=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&status=<?php echo urlencode($status); ?>" class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

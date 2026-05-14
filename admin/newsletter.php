<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'newsletter';
$pageTitle   = 'Newsletter Subscribers';

$pdo = getDBConnection();

// Unsubscribe (admin action)
if (isset($_GET['unsub'])) {
    $uid = (int)$_GET['unsub'];
    $pdo->prepare("UPDATE newsletter_subscribers SET status = 'unsubscribed' WHERE id = ?")->execute([$uid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Subscriber marked as unsubscribed.'];
    header('Location: newsletter.php'); exit;
}

// Resubscribe
if (isset($_GET['resub'])) {
    $rid = (int)$_GET['resub'];
    $pdo->prepare("UPDATE newsletter_subscribers SET status = 'active' WHERE id = ?")->execute([$rid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Subscriber reactivated.'];
    header('Location: newsletter.php'); exit;
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Subscriber deleted.'];
    header('Location: newsletter.php'); exit;
}

// CSV export
if (isset($_GET['export'])) {
    $all = $pdo->query("SELECT email, name, status, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="newsletter-subscribers-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Email', 'Name', 'Status', 'Subscribed At']);
    foreach ($all as $row) fputcsv($out, $row);
    fclose($out); exit;
}

// Filters
$status = $_GET['status'] ?? '';
$search = trim($_GET['q'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25; $offset = ($page - 1) * $perPage;

$where = []; $params = [];
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($search) { $where[] = '(email LIKE ? OR name LIKE ?)';
               $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total      = $pdo->prepare("SELECT COUNT(*) FROM newsletter_subscribers $whereSQL");
$total->execute($params);
$totalRows  = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$rows = $pdo->prepare("SELECT * FROM newsletter_subscribers $whereSQL ORDER BY subscribed_at DESC LIMIT $perPage OFFSET $offset");
$rows->execute($params);
$subscribers = $rows->fetchAll(PDO::FETCH_ASSOC);

$statsQ = $pdo->query("SELECT status, COUNT(*) c FROM newsletter_subscribers GROUP BY status");
$stats  = $statsQ->fetchAll(PDO::FETCH_KEY_PAIR);

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div><h1><i class="fas fa-envelope-open-text"></i> Newsletter Subscribers</h1></div>
    <a href="?export=1" class="btn-export"><i class="fas fa-file-csv"></i> Export CSV</a>
</div>

<div class="stats-bar">
    <div class="stat-chip"><span><?php echo $stats['active'] ?? 0; ?></span> Active</div>
    <div class="stat-chip warning"><span><?php echo $stats['unsubscribed'] ?? 0; ?></span> Unsubscribed</div>
</div>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="q" class="filter-input" placeholder="Search email or name..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="status" class="filter-select">
            <option value="">All</option>
            <option value="active"       <?php echo $status==='active'?'selected':''; ?>>Active</option>
            <option value="unsubscribed" <?php echo $status==='unsubscribed'?'selected':''; ?>>Unsubscribed</option>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Filter</button>
        <a href="newsletter.php" class="filter-reset">Reset</a>
    </form>
</div>

<div class="table-wrapper">
<?php if ($subscribers): ?>
<table class="admin-table">
    <thead>
        <tr><th>#</th><th>Email</th><th>Name</th><th>Status</th><th>Subscribed</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($subscribers as $i => $sub): ?>
    <tr>
        <td><?php echo $offset + $i + 1; ?></td>
        <td><?php echo htmlspecialchars($sub['email']); ?></td>
        <td><?php echo htmlspecialchars($sub['name'] ?? '—'); ?></td>
        <td>
            <?php if ($sub['status'] === 'active'): ?>
            <span class="badge-success">Active</span>
            <?php else: ?>
            <span class="badge-danger">Unsubscribed</span>
            <?php endif; ?>
        </td>
        <td><?php echo date('d M Y', strtotime($sub['subscribed_at'])); ?></td>
        <td>
            <div class="action-btns">
                <?php if ($sub['status'] === 'active'): ?>
                <a href="?unsub=<?php echo $sub['id']; ?>" class="btn-toggle" title="Unsubscribe" onclick="return confirm('Mark as unsubscribed?')"><i class="fas fa-user-slash"></i></a>
                <?php else: ?>
                <a href="?resub=<?php echo $sub['id']; ?>" class="btn-edit" title="Reactivate"><i class="fas fa-user-check"></i></a>
                <?php endif; ?>
                <a href="?delete=<?php echo $sub['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this subscriber permanently?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-envelope-open-text fa-3x"></i>
    <p>No subscribers yet.</p>
</div>
<?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?php echo $p; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>

<style>
.btn-export { background: #17a2b8; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: .9rem; }
</style>
<?php include 'includes/footer.php'; ?>

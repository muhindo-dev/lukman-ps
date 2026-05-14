<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'downloads';
$pageTitle   = 'Downloads';

$pdo = getDBConnection();

// Toggle status
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $r = $pdo->prepare("SELECT status FROM downloads WHERE id = ?");
    $r->execute([$tid]);
    $cur = $r->fetchColumn();
    $new = $cur === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE downloads SET status = ? WHERE id = ?")->execute([$new, $tid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Status updated.'];
    header('Location: downloads.php'); exit;
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $r2 = $pdo->prepare("SELECT file_path FROM downloads WHERE id = ?");
    $r2->execute([$did]);
    $fp = $r2->fetchColumn();
    if ($fp && file_exists('../uploads/' . $fp)) @unlink('../uploads/' . $fp);
    $pdo->prepare("DELETE FROM downloads WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Download deleted.'];
    header('Location: downloads.php'); exit;
}

// --- Filters ---
$status   = $_GET['status']   ?? '';
$category = $_GET['category'] ?? '';
$search   = trim($_GET['q']   ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

$where = []; $params = [];
if ($status)   { $where[] = 'd.status = ?';   $params[] = $status; }
if ($category) { $where[] = 'd.category = ?'; $params[] = $category; }
if ($search)   { $where[] = '(d.title LIKE ? OR d.description LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM downloads d $whereSQL");
$total->execute($params);
$totalRows = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$rows = $pdo->prepare("SELECT * FROM downloads d $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$rows->execute($params);
$downloads = $rows->fetchAll(PDO::FETCH_ASSOC);

$stats = $pdo->query("SELECT status, COUNT(*) c FROM downloads GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);
$totalDownloads = $pdo->query("SELECT SUM(download_count) FROM downloads")->fetchColumn();

$categories = ['fees','circulars','academic','forms','routine','general'];

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div>
        <h1><i class="fas fa-download"></i> Downloads</h1>
    </div>
    <a href="downloads-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Download</a>
</div>

<div class="stats-bar">
    <div class="stat-chip"><span><?php echo $stats['active'] ?? 0; ?></span> Active</div>
    <div class="stat-chip warning"><span><?php echo $stats['inactive'] ?? 0; ?></span> Inactive</div>
    <div class="stat-chip"><span><?php echo (int)$totalDownloads; ?></span> Total Downloads</div>
</div>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="q" class="filter-input" placeholder="Search files..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active" <?php echo $status==='active'?'selected':''; ?>>Active</option>
            <option value="inactive" <?php echo $status==='inactive'?'selected':''; ?>>Inactive</option>
        </select>
        <select name="category" class="filter-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $category===$c?'selected':''; ?>><?php echo ucfirst($c); ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Filter</button>
        <a href="downloads.php" class="filter-reset">Reset</a>
    </form>
</div>

<div class="table-wrapper">
<?php if ($downloads): ?>
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Category</th>
            <th>Type</th>
            <th>Size</th>
            <th>Downloads</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($downloads as $i => $d): ?>
    <tr>
        <td><?php echo $offset + $i + 1; ?></td>
        <td>
            <strong><?php echo htmlspecialchars($d['title']); ?></strong>
            <?php if ($d['description']): ?>
            <div class="text-muted small"><?php echo htmlspecialchars(substr($d['description'], 0, 60)) . (strlen($d['description']) > 60 ? '…' : ''); ?></div>
            <?php endif; ?>
        </td>
        <td><span class="badge-info"><?php echo ucfirst(htmlspecialchars($d['category'] ?? '')); ?></span></td>
        <td><?php echo strtoupper(htmlspecialchars($d['file_type'] ?? '')); ?></td>
        <td><?php echo $d['file_size'] ? round($d['file_size']/1024, 1) . ' KB' : '—'; ?></td>
        <td><?php echo (int)$d['download_count']; ?></td>
        <td>
            <?php if ($d['status'] === 'active'): ?>
            <span class="badge-success">Active</span>
            <?php else: ?>
            <span class="badge-danger">Inactive</span>
            <?php endif; ?>
        </td>
        <td><?php echo date('d M Y', strtotime($d['created_at'])); ?></td>
        <td>
            <div class="action-btns">
                <a href="downloads-edit.php?id=<?php echo $d['id']; ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <?php if ($d['file_path']): ?>
                <a href="../uploads/<?php echo htmlspecialchars($d['file_path']); ?>" target="_blank" class="btn-view" title="View file"><i class="fas fa-eye"></i></a>
                <?php endif; ?>
                <a href="?toggle=<?php echo $d['id']; ?>" class="btn-toggle" title="Toggle status" onclick="return confirm('Toggle status?')"><i class="fas fa-toggle-<?php echo $d['status']==='active'?'on':'off'; ?>"></i></a>
                <a href="?delete=<?php echo $d['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this download permanently?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-download fa-3x"></i>
    <p>No downloads found.</p>
    <a href="downloads-add.php" class="btn-add-new">Add First Download</a>
</div>
<?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?php echo $p; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&category=<?php echo urlencode($category); ?>" class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

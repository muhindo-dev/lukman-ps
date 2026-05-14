<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentAdmin = getCurrentAdmin();
$currentPage  = 'notices';
$pageTitle    = 'Notices';

$pdo = getDBConnection();

// Handle toggle active/inactive
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $row = $pdo->prepare("SELECT status FROM notices WHERE id = ?");
    $row->execute([(int)$_GET['toggle']]);
    $cur  = $row->fetchColumn();
    $next = ($cur === 'active') ? 'inactive' : 'active';
    $pdo->prepare("UPDATE notices SET status = ? WHERE id = ?")->execute([$next, (int)$_GET['toggle']]);
    header('Location: notices.php'); exit;
}

// Handle pin toggle
if (isset($_GET['pin']) && is_numeric($_GET['pin'])) {
    $row = $pdo->prepare("SELECT is_pinned FROM notices WHERE id = ?");
    $row->execute([(int)$_GET['pin']]);
    $cur  = (int)$row->fetchColumn();
    $pdo->prepare("UPDATE notices SET is_pinned = ? WHERE id = ?")->execute([$cur ? 0 : 1, (int)$_GET['pin']]);
    header('Location: notices.php'); exit;
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $pdo->prepare("DELETE FROM notices WHERE id = ?")->execute([(int)$_GET['delete']]);
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Notice deleted.'];
    header('Location: notices.php'); exit;
}

// Filters & pagination
$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$status  = $_GET['status'] ?? '';
$search  = trim($_GET['search'] ?? '');

$conditions = [];
$params     = [];
if ($status) { $conditions[] = "status = ?"; $params[] = $status; }
if ($search) { $conditions[] = "(title LIKE ? OR content LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total      = $pdo->prepare("SELECT COUNT(*) FROM notices $where"); $total->execute($params); $total = $total->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM notices $where ORDER BY is_pinned DESC, start_date DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

$activeCount   = $pdo->query("SELECT COUNT(*) FROM notices WHERE status='active'")->fetchColumn();
$inactiveCount = $pdo->query("SELECT COUNT(*) FROM notices WHERE status='inactive'")->fetchColumn();
$pinnedCount   = $pdo->query("SELECT COUNT(*) FROM notices WHERE is_pinned=1")->fetchColumn();

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): ?>
<div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
    <i class="fas fa-<?php echo $_SESSION['alert']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
    <?php echo $_SESSION['alert']['message']; ?>
</div>
<?php unset($_SESSION['alert']); endif; ?>

<div class="content-header-compact">
    <h1><i class="fas fa-bullhorn"></i> Notices</h1>
    <a href="notices-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Notice</a>
</div>

<div class="stats-bar mb-3">
    <span class="stat-chip"><i class="fas fa-check-circle text-success me-1"></i><?php echo $activeCount; ?> Active</span>
    <span class="stat-chip"><i class="fas fa-times-circle text-secondary me-1"></i><?php echo $inactiveCount; ?> Inactive</span>
    <span class="stat-chip"><i class="fas fa-thumbtack text-warning me-1"></i><?php echo $pinnedCount; ?> Pinned</span>
    <span class="stat-chip"><i class="fas fa-list me-1"></i><?php echo $total; ?> Total</span>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar mb-3">
    <div class="row g-2">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search notices…" value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="notices.php" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
    </div>
</form>

<div class="table-responsive">
<table class="admin-table">
    <thead>
        <tr>
            <th style="width:30%">Title</th>
            <th>Type</th>
            <th>Dates</th>
            <th>Pinned</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($items)): ?>
    <tr><td colspan="6" class="text-center text-muted py-4">No notices found.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
    <?php
        $typeColors = ['info'=>'primary','warning'=>'warning','urgent'=>'danger','event'=>'success'];
        $typeColor  = $typeColors[$item['type']] ?? 'secondary';
        $isExpired  = $item['end_date'] && $item['end_date'] < date('Y-m-d');
    ?>
    <tr class="<?php echo $isExpired ? 'opacity-50' : ''; ?>">
        <td>
            <strong><?php echo htmlspecialchars($item['title']); ?></strong>
            <?php if ($isExpired): ?><br><small class="text-danger"><i class="fas fa-clock me-1"></i>Expired</small><?php endif; ?>
            <?php if ($item['content']): ?><br><small class="text-muted"><?php echo htmlspecialchars(mb_substr(strip_tags($item['content']), 0, 60)) . (mb_strlen(strip_tags($item['content'])) > 60 ? '…' : ''); ?></small><?php endif; ?>
        </td>
        <td><span class="badge bg-<?php echo $typeColor; ?>"><?php echo ucfirst($item['type']); ?></span></td>
        <td>
            <small>
                <i class="fas fa-calendar me-1 text-muted"></i><?php echo date('d M Y', strtotime($item['start_date'])); ?>
                <?php if ($item['end_date']): ?>
                <br><i class="fas fa-calendar-check me-1 text-muted"></i><?php echo date('d M Y', strtotime($item['end_date'])); ?>
                <?php endif; ?>
            </small>
        </td>
        <td>
            <a href="notices.php?pin=<?php echo $item['id']; ?>" class="btn btn-xs <?php echo $item['is_pinned'] ? 'btn-warning' : 'btn-outline-secondary'; ?>" title="<?php echo $item['is_pinned'] ? 'Unpin' : 'Pin'; ?>">
                <i class="fas fa-thumbtack"></i>
            </a>
        </td>
        <td>
            <a href="notices.php?toggle=<?php echo $item['id']; ?>"
               class="status-badge <?php echo $item['status'] == 'active' ? 'status-published' : 'status-draft'; ?>">
                <?php echo ucfirst($item['status']); ?>
            </a>
        </td>
        <td>
            <div class="action-btns">
                <a href="notices-edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="notices.php?delete=<?php echo $item['id']; ?>"
                   class="btn-action btn-delete"
                   title="Delete"
                   onclick="return confirm('Delete this notice?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-wrap mt-3">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>"
       class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

</div>
<?php include 'includes/footer.php'; ?>

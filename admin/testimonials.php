<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentAdmin = getCurrentAdmin();
$currentPage  = 'testimonials';
$pageTitle    = 'Testimonials';

$pdo = getDBConnection();

// Handle status toggle via GET
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $row = $pdo->prepare("SELECT status FROM testimonials WHERE id = ?");
    $row->execute([(int)$_GET['toggle']]);
    $cur = $row->fetchColumn();
    $next = ($cur === 'approved') ? 'pending' : 'approved';
    $pdo->prepare("UPDATE testimonials SET status = ? WHERE id = ?")->execute([$next, (int)$_GET['toggle']]);
    header('Location: testimonials.php'); exit;
}

// Handle delete via GET
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $row = $pdo->prepare("SELECT photo FROM testimonials WHERE id = ?");
    $row->execute([(int)$_GET['delete']]);
    $photo = $row->fetchColumn();
    if ($photo && file_exists('../uploads/' . $photo)) @unlink('../uploads/' . $photo);
    $pdo->prepare("DELETE FROM testimonials WHERE id = ?")->execute([(int)$_GET['delete']]);
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Testimonial deleted.'];
    header('Location: testimonials.php'); exit;
}

// Pagination & filter
$perPage  = 20;
$page     = max(1, (int)($_GET['page'] ?? 1));
$status   = $_GET['status'] ?? '';
$search   = trim($_GET['search'] ?? '');

$conditions = [];
$params     = [];
if ($status) { $conditions[] = "status = ?"; $params[] = $status; }
if ($search) { $conditions[] = "(name LIKE ? OR content LIKE ? OR role LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
$where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$total      = $pdo->prepare("SELECT COUNT(*) FROM testimonials $where"); $total->execute($params); $total = $total->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM testimonials $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

// Counts for badges
$pending  = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'pending'")->fetchColumn();
$approved = $pdo->query("SELECT COUNT(*) FROM testimonials WHERE status = 'approved'")->fetchColumn();

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
    <h1><i class="fas fa-quote-right"></i> Testimonials</h1>
    <a href="testimonials-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Testimonial</a>
</div>

<!-- Stats bar -->
<div class="stats-bar mb-3">
    <span class="stat-chip"><i class="fas fa-check-circle text-success me-1"></i><?php echo $approved; ?> Approved</span>
    <span class="stat-chip <?php echo $pending > 0 ? 'stat-chip-warn' : ''; ?>"><i class="fas fa-clock <?php echo $pending > 0 ? 'text-warning' : ''; ?> me-1"></i><?php echo $pending; ?> Pending</span>
    <span class="stat-chip"><i class="fas fa-list me-1"></i><?php echo $total; ?> Total</span>
</div>

<!-- Filters -->
<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search name, content..." value="<?php echo htmlspecialchars($search); ?>" class="form-control-sm">
        <select name="status" class="form-select-sm">
            <option value="">All Statuses</option>
            <option value="approved"  <?php echo $status === 'approved'  ? 'selected' : ''; ?>>Approved</option>
            <option value="pending"   <?php echo $status === 'pending'   ? 'selected' : ''; ?>>Pending</option>
            <option value="rejected"  <?php echo $status === 'rejected'  ? 'selected' : ''; ?>>Rejected</option>
        </select>
        <button type="submit" class="btn-filter">Filter</button>
        <?php if ($search || $status): ?><a href="testimonials.php" class="btn-clear">Clear</a><?php endif; ?>
    </form>
</div>

<?php if (empty($items)): ?>
<div class="empty-state"><i class="fas fa-quote-right"></i><p>No testimonials found.</p></div>
<?php else: ?>
<div class="table-wrapper">
<table class="admin-table">
    <thead>
        <tr>
            <th>Person</th>
            <th>Role</th>
            <th>Rating</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $t): ?>
    <tr>
        <td>
            <?php if ($t['photo']): ?>
            <img src="../uploads/<?php echo htmlspecialchars($t['photo']); ?>" alt="" style="width:36px;height:36px;border-radius:50%;object-fit:cover;margin-right:8px;vertical-align:middle;">
            <?php endif; ?>
            <span><?php echo htmlspecialchars($t['name']); ?></span>
        </td>
        <td><?php echo htmlspecialchars($t['role'] ?? '—'); ?></td>
        <td>
            <?php $r = (int)($t['rating'] ?? 0); for ($i=1;$i<=5;$i++) echo '<i class="fas fa-star" style="color:' . ($i<=$r ? '#EA1B27' : '#ddd') . ';font-size:0.75rem;"></i>'; ?>
        </td>
        <td>
            <?php
            $badge = ['approved' => 'badge-success', 'pending' => 'badge-warning', 'rejected' => 'badge-danger'];
            ?>
            <span class="<?php echo $badge[$t['status']] ?? 'badge-secondary'; ?>"><?php echo ucfirst($t['status']); ?></span>
        </td>
        <td><?php echo date('d M Y', strtotime($t['created_at'])); ?></td>
        <td>
            <div class="action-btns">
                <a href="testimonials-edit.php?id=<?php echo $t['id']; ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="testimonials.php?toggle=<?php echo $t['id']; ?>" class="btn-toggle" title="Toggle Status"><i class="fas fa-toggle-<?php echo $t['status'] === 'approved' ? 'on text-success' : 'off text-secondary'; ?>"></i></a>
                <a href="testimonials.php?delete=<?php echo $t['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this testimonial?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($i=1;$i<=$totalPages;$i++): ?>
    <a href="?page=<?php echo $i; ?>&status=<?php echo urlencode($status); ?>&search=<?php echo urlencode($search); ?>" class="page-btn <?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

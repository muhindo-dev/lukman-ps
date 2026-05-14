<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'admission-inquiries';
$pageTitle   = 'Admission Inquiries';

$pdo = getDBConnection();

// Update status via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $uid = (int)$_POST['inquiry_id'];
    $ns  = $_POST['new_status'] ?? '';
    $validStatuses = ['new','contacted','visited','enrolled','declined'];
    if ($uid && in_array($ns, $validStatuses)) {
        $pdo->prepare("UPDATE admission_inquiries SET status = ? WHERE id = ?")->execute([$ns, $uid]);
        $_SESSION['alert'] = ['type'=>'success','message'=>'Status updated.'];
    }
    header('Location: admission-inquiries.php'); exit;
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM admission_inquiries WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Inquiry deleted.'];
    header('Location: admission-inquiries.php'); exit;
}

// CSV export
if (isset($_GET['export'])) {
    $all = $pdo->query("SELECT * FROM admission_inquiries ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="admission-inquiries-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    if ($all) {
        fputcsv($out, array_keys($all[0]));
        foreach ($all as $row) fputcsv($out, $row);
    }
    fclose($out); exit;
}

// Filters
$status   = $_GET['status']   ?? '';
$class    = $_GET['class']    ?? '';
$search   = trim($_GET['q']   ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 20;
$offset   = ($page - 1) * $perPage;

$where = []; $params = [];
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($class)  { $where[] = 'class_applying = ?'; $params[] = $class; }
if ($search) { $where[] = '(parent_name LIKE ? OR parent_email LIKE ? OR child_name LIKE ?)';
               $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("SELECT COUNT(*) FROM admission_inquiries $whereSQL");
$total->execute($params);
$totalRows = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$rows = $pdo->prepare("SELECT * FROM admission_inquiries $whereSQL ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$rows->execute($params);
$inquiries = $rows->fetchAll(PDO::FETCH_ASSOC);

$statusCounts = $pdo->query("SELECT status, COUNT(*) c FROM admission_inquiries GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR);

$statusColors = [
    'new'       => 'badge-warning',
    'contacted' => 'badge-info',
    'visited'   => 'badge-primary',
    'enrolled'  => 'badge-success',
    'declined'  => 'badge-danger',
];
$validStatuses = ['new','contacted','visited','enrolled','declined'];
$classes = ['P1','P2','P3','P4','P5','P6','P7'];

// Get viewed inquiry for modal
$viewId     = (int)($_GET['view'] ?? 0);
$viewRecord = null;
if ($viewId) {
    $vr = $pdo->prepare("SELECT * FROM admission_inquiries WHERE id = ?");
    $vr->execute([$viewId]);
    $viewRecord = $vr->fetch(PDO::FETCH_ASSOC);
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div>
        <h1><i class="fas fa-user-graduate"></i> Admission Inquiries</h1>
    </div>
    <a href="?export=1" class="btn-export"><i class="fas fa-file-csv"></i> Export CSV</a>
</div>

<div class="stats-bar">
    <?php foreach ($statusColors as $s => $cls): ?>
    <div class="stat-chip <?php echo str_replace('badge-','',$cls); ?>"><span><?php echo $statusCounts[$s] ?? 0; ?></span> <?php echo ucfirst($s); ?></div>
    <?php endforeach; ?>
</div>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <input type="text" name="q" class="filter-input" placeholder="Search name, email..." value="<?php echo htmlspecialchars($search); ?>">
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <?php foreach ($validStatuses as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $status===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="class" class="filter-select">
            <option value="">All Classes</option>
            <?php foreach ($classes as $c): ?>
            <option value="<?php echo $c; ?>" <?php echo $class===$c?'selected':''; ?>><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Filter</button>
        <a href="admission-inquiries.php" class="filter-reset">Reset</a>
    </form>
</div>

<div class="table-wrapper">
<?php if ($inquiries): ?>
<table class="admin-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Parent / Guardian</th>
            <th>Child</th>
            <th>Class</th>
            <th>Boarding</th>
            <th>Contact</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($inquiries as $i => $inq): ?>
    <tr>
        <td><?php echo $offset + $i + 1; ?></td>
        <td>
            <strong><?php echo htmlspecialchars($inq['parent_name']); ?></strong>
        </td>
        <td><?php echo htmlspecialchars($inq['child_name']); ?></td>
        <td><?php echo htmlspecialchars($inq['class_applying'] ?? '—'); ?></td>
        <td><?php echo ucfirst(htmlspecialchars($inq['boarding_type'] ?? '—')); ?></td>
        <td>
            <div><?php echo htmlspecialchars($inq['parent_email']); ?></div>
            <div class="text-muted small"><?php echo htmlspecialchars($inq['parent_phone'] ?? ''); ?></div>
        </td>
        <td>
            <form method="POST" style="display:inline">
                <input type="hidden" name="inquiry_id" value="<?php echo $inq['id']; ?>">
                <input type="hidden" name="update_status" value="1">
                <select name="new_status" class="form-control form-control-sm status-inline" onchange="this.form.submit()">
                    <?php foreach ($validStatuses as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $inq['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </td>
        <td><?php echo date('d M Y', strtotime($inq['created_at'])); ?></td>
        <td>
            <div class="action-btns">
                <a href="?view=<?php echo $inq['id']; ?>" class="btn-view" title="View details"><i class="fas fa-eye"></i></a>
                <a href="?delete=<?php echo $inq['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this inquiry?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-user-graduate fa-3x"></i>
    <p>No admission inquiries yet.</p>
</div>
<?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?php echo $p; ?>&q=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>&class=<?php echo urlencode($class); ?>" class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<?php if ($viewRecord): ?>
<!-- Detail Modal -->
<div class="modal-overlay" id="detailModal" style="display:flex">
    <div class="modal-box" style="max-width:600px;width:100%">
        <div class="modal-header">
            <h3><i class="fas fa-user-graduate me-2"></i>Inquiry Details</h3>
            <a href="admission-inquiries.php" class="modal-close"><i class="fas fa-times"></i></a>
        </div>
        <div class="modal-body">
            <table class="detail-table">
                <tr><th>Parent / Guardian</th><td><?php echo htmlspecialchars($viewRecord['parent_name']); ?></td></tr>
                <tr><th>Email</th><td><a href="mailto:<?php echo htmlspecialchars($viewRecord['parent_email']); ?>"><?php echo htmlspecialchars($viewRecord['parent_email']); ?></a></td></tr>
                <tr><th>Phone</th><td><?php echo htmlspecialchars($viewRecord['parent_phone'] ?? '—'); ?></td></tr>
                <tr><th>Child's Name</th><td><?php echo htmlspecialchars($viewRecord['child_name']); ?></td></tr>
                <tr><th>Date of Birth</th><td><?php echo $viewRecord['child_dob'] ? date('d M Y', strtotime($viewRecord['child_dob'])) : '—'; ?></td></tr>
                <tr><th>Gender</th><td><?php echo ucfirst(htmlspecialchars($viewRecord['child_gender'] ?? '—')); ?></td></tr>
                <tr><th>Current School</th><td><?php echo htmlspecialchars($viewRecord['current_school'] ?? '—'); ?></td></tr>
                <tr><th>Applying for Class</th><td><?php echo htmlspecialchars($viewRecord['class_applying'] ?? '—'); ?></td></tr>
                <tr><th>Boarding Type</th><td><?php echo ucfirst(htmlspecialchars($viewRecord['boarding_type'] ?? '—')); ?></td></tr>
                <tr><th>How Heard</th><td><?php echo htmlspecialchars($viewRecord['how_heard'] ?? '—'); ?></td></tr>
                <tr><th>Status</th><td><span class="<?php echo $statusColors[$viewRecord['status']] ?? ''; ?>"><?php echo ucfirst($viewRecord['status']); ?></span></td></tr>
                <tr><th>Submitted</th><td><?php echo date('d M Y H:i', strtotime($viewRecord['created_at'])); ?></td></tr>
            </table>
            <?php if ($viewRecord['message']): ?>
            <div class="mt-3"><strong>Message:</strong><p><?php echo nl2br(htmlspecialchars($viewRecord['message'])); ?></p></div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <a href="admission-inquiries.php" class="btn-cancel">Close</a>
            <a href="mailto:<?php echo htmlspecialchars($viewRecord['parent_email']); ?>" class="btn-save"><i class="fas fa-envelope me-1"></i> Email Parent</a>
        </div>
    </div>
</div>
<?php endif; ?>
</div>

<style>
.status-inline { width: auto; display: inline-block; padding: 2px 6px; font-size: .8rem; }
.detail-table { width: 100%; border-collapse: collapse; }
.detail-table th, .detail-table td { padding: 8px 12px; border-bottom: 1px solid #eee; text-align: left; }
.detail-table th { width: 40%; color: #555; font-weight: 600; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index:999; align-items:center; justify-content:center; }
.modal-box { background: #fff; border-radius: 8px; overflow: hidden; }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; background: var(--primary); color: #fff; }
.modal-header h3 { margin: 0; font-size: 1.1rem; }
.modal-close { color: #fff; font-size: 1.2rem; }
.modal-body { padding: 20px; max-height: 60vh; overflow-y: auto; }
.modal-footer { display: flex; gap: 10px; justify-content: flex-end; padding: 14px 20px; border-top: 1px solid #eee; }
.btn-export { background: #17a2b8; color: #fff; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: .9rem; }
.btn-view { background: #17a2b8; color: #fff; padding: 4px 8px; border-radius: 4px; text-decoration: none; }
</style>
<?php include 'includes/footer.php'; ?>

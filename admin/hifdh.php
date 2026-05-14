<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentAdmin = getCurrentAdmin();
$currentPage  = 'hifdh';
$pageTitle    = 'Ḥifẓ Achievements';

$pdo = getDBConnection();

// Toggle status
if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $row = $pdo->prepare("SELECT status FROM hifdh_achievements WHERE id = ?");
    $row->execute([(int)$_GET['toggle']]);
    $cur = $row->fetchColumn();
    $next = $cur === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE hifdh_achievements SET status = ? WHERE id = ?")->execute([$next, (int)$_GET['toggle']]);
    header('Location: hifdh.php'); exit;
}

// Delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $row = $pdo->prepare("SELECT photo FROM hifdh_achievements WHERE id = ?");
    $row->execute([(int)$_GET['delete']]);
    $photo = $row->fetchColumn();
    if ($photo && file_exists('../uploads/' . $photo)) @unlink('../uploads/' . $photo);
    $pdo->prepare("DELETE FROM hifdh_achievements WHERE id = ?")->execute([(int)$_GET['delete']]);
    $_SESSION['alert'] = ['type' => 'success', 'message' => 'Achievement deleted.'];
    header('Location: hifdh.php'); exit;
}

// Filters
$perPage = 25;
$page    = max(1, (int)($_GET['page'] ?? 1));
$ms      = $_GET['milestone'] ?? '';
$search  = trim($_GET['search'] ?? '');

$conds = []; $params = [];
if ($ms)     { $conds[] = "milestone = ?";                             $params[] = $ms; }
if ($search) { $conds[] = "(pupil_name LIKE ? OR class LIKE ?)";       $params[] = "%$search%"; $params[] = "%$search%"; }
$where = $conds ? 'WHERE ' . implode(' AND ', $conds) : '';

$total      = $pdo->prepare("SELECT COUNT(*) FROM hifdh_achievements $where"); $total->execute($params); $total = $total->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT * FROM hifdh_achievements $where ORDER BY
    FIELD(milestone,'full_hifdh','half_hifdh','juz20','juz15','juz10','juz5','juz1'), date_achieved DESC
    LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$items = $stmt->fetchAll();

$milestoneLabels = [
    'full_hifdh' => 'Full Ḥifẓ ☆',  'half_hifdh' => 'Half Ḥifẓ',
    'juz20' => "Juz' 20", 'juz15' => "Juz' 15", 'juz10' => "Juz' 10",
    'juz5'  => "Juz' 5",  'juz1'  => "Juz' 1",
];
$milestoneColors = [
    'full_hifdh'=>'success','half_hifdh'=>'warning','juz20'=>'warning',
    'juz15'=>'secondary','juz10'=>'secondary','juz5'=>'info','juz1'=>'info',
];

// Counts
$fullCount  = $pdo->query("SELECT COUNT(*) FROM hifdh_achievements WHERE milestone='full_hifdh' AND status='active'")->fetchColumn();
$halfCount  = $pdo->query("SELECT COUNT(*) FROM hifdh_achievements WHERE milestone='half_hifdh' AND status='active'")->fetchColumn();
$totalActive= $pdo->query("SELECT COUNT(*) FROM hifdh_achievements WHERE status='active'")->fetchColumn();

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
    <h1><i class="fas fa-trophy"></i> Ḥifẓ Achievements</h1>
    <a href="hifdh-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Achievement</a>
</div>

<div class="stats-bar mb-3">
    <span class="stat-chip" style="background:linear-gradient(135deg,#EA1B27,#c4151f);color:#fff;"><i class="fas fa-trophy me-1"></i><?php echo $fullCount; ?> Full Ḥifẓ</span>
    <span class="stat-chip"><i class="fas fa-star me-1 text-warning"></i><?php echo $halfCount; ?> Half Ḥifẓ</span>
    <span class="stat-chip"><i class="fas fa-users me-1"></i><?php echo $totalActive; ?> Active</span>
    <span class="stat-chip"><i class="fas fa-list me-1"></i><?php echo $total; ?> Total</span>
</div>

<!-- Filters -->
<form method="GET" class="filter-bar mb-3">
    <div class="row g-2">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search name or class…" value="<?php echo htmlspecialchars($search); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <select name="milestone" class="form-select form-select-sm">
                <option value="">All Milestones</option>
                <?php foreach ($milestoneLabels as $k => $v): ?>
                <option value="<?php echo $k; ?>" <?php echo $ms === $k ? 'selected' : ''; ?>><?php echo $v; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
            <a href="hifdh.php" class="btn btn-sm btn-outline-secondary">Reset</a>
        </div>
        <div class="col-auto ms-auto">
            <a href="../quran.php" target="_blank" class="btn btn-sm btn-outline-success">
                <i class="fas fa-external-link-alt me-1"></i>View Public Page
            </a>
        </div>
    </div>
</form>

<div class="table-responsive">
<table class="admin-table">
    <thead>
        <tr>
            <th>Pupil</th>
            <th>Class</th>
            <th>Milestone</th>
            <th>Date Achieved</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($items)): ?>
    <tr><td colspan="6" class="text-center text-muted py-4">No achievements found.</td></tr>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
    <tr>
        <td>
            <?php if (!empty($item['photo']) && file_exists('../uploads/' . $item['photo'])): ?>
            <img src="../uploads/<?php echo htmlspecialchars($item['photo']); ?>" style="width:36px;height:36px;border-radius:50%;object-fit:cover;vertical-align:middle;margin-right:8px;">
            <?php endif; ?>
            <strong><?php echo htmlspecialchars($item['pupil_name']); ?></strong>
        </td>
        <td><?php echo htmlspecialchars($item['class'] ?? '—'); ?></td>
        <td>
            <span class="badge bg-<?php echo $milestoneColors[$item['milestone']] ?? 'secondary'; ?>">
                <?php echo $milestoneLabels[$item['milestone']] ?? $item['milestone']; ?>
            </span>
        </td>
        <td><small><?php echo date('d M Y', strtotime($item['date_achieved'])); ?></small></td>
        <td>
            <a href="hifdh.php?toggle=<?php echo $item['id']; ?>"
               class="status-badge <?php echo $item['status'] === 'active' ? 'status-published' : 'status-draft'; ?>">
                <?php echo ucfirst($item['status']); ?>
            </a>
        </td>
        <td>
            <div class="action-btns">
                <a href="hifdh-edit.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="hifdh.php?delete=<?php echo $item['id']; ?>"
                   class="btn-action btn-delete" title="Delete"
                   onclick="return confirm('Delete this achievement?')"><i class="fas fa-trash"></i></a>
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
    <a href="?page=<?php echo $i; ?>&milestone=<?php echo urlencode($ms); ?>&search=<?php echo urlencode($search); ?>"
       class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

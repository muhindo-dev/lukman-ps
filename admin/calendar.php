<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'calendar';
$pageTitle   = 'School Calendar';

$pdo = getDBConnection();

// Toggle status
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $r = $pdo->prepare("SELECT status FROM school_calendar WHERE id = ?");
    $r->execute([$tid]);
    $cur = $r->fetchColumn();
    $new = $cur === 'active' ? 'inactive' : 'active';
    $pdo->prepare("UPDATE school_calendar SET status = ? WHERE id = ?")->execute([$new, $tid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Status updated.'];
    header('Location: calendar.php'); exit;
}

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM school_calendar WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Calendar event deleted.'];
    header('Location: calendar.php'); exit;
}

$term   = $_GET['term']   ?? '';
$type   = $_GET['type']   ?? '';
$status = $_GET['status'] ?? '';
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25; $offset = ($page - 1) * $perPage;

$where = []; $params = [];
if ($term)   { $where[] = 'term = ?';         $params[] = $term; }
if ($type)   { $where[] = 'event_type = ?';   $params[] = $type; }
if ($status) { $where[] = 'status = ?';       $params[] = $status; }
$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total      = $pdo->prepare("SELECT COUNT(*) FROM school_calendar $whereSQL");
$total->execute($params);
$totalRows  = (int)$total->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$rows       = $pdo->prepare("SELECT * FROM school_calendar $whereSQL ORDER BY start_date ASC LIMIT $perPage OFFSET $offset");
$rows->execute($params);
$events     = $rows->fetchAll(PDO::FETCH_ASSOC);

$eventTypes = ['term','holiday','exam','event','meeting','other'];
$typeColors = [
    'term'    => 'badge-success',
    'holiday' => 'badge-warning',
    'exam'    => 'badge-danger',
    'event'   => 'badge-info',
    'meeting' => 'badge-primary',
    'other'   => '',
];

$statsQ = $pdo->query("SELECT status, COUNT(*) c FROM school_calendar GROUP BY status");
$stats  = $statsQ->fetchAll(PDO::FETCH_KEY_PAIR);

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div><h1><i class="fas fa-calendar-alt"></i> School Calendar</h1></div>
    <a href="calendar-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Event</a>
</div>

<div class="stats-bar">
    <div class="stat-chip"><span><?php echo $stats['active'] ?? 0; ?></span> Active</div>
    <div class="stat-chip warning"><span><?php echo $stats['inactive'] ?? 0; ?></span> Inactive</div>
    <div class="stat-chip"><span><?php echo $totalRows; ?></span> Shown</div>
</div>

<div class="filter-bar">
    <form method="GET" class="filter-form">
        <select name="term" class="filter-select">
            <option value="">All Terms</option>
            <?php foreach ([1,2,3] as $t): ?>
            <option value="<?php echo $t; ?>" <?php echo $term==(string)$t?'selected':''; ?>>Term <?php echo $t; ?></option>
            <?php endforeach; ?>
        </select>
        <select name="type" class="filter-select">
            <option value="">All Types</option>
            <?php foreach ($eventTypes as $et): ?>
            <option value="<?php echo $et; ?>" <?php echo $type===$et?'selected':''; ?>><?php echo ucfirst($et); ?></option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="active"   <?php echo $status==='active'?'selected':''; ?>>Active</option>
            <option value="inactive" <?php echo $status==='inactive'?'selected':''; ?>>Inactive</option>
        </select>
        <button type="submit" class="filter-btn"><i class="fas fa-search"></i> Filter</button>
        <a href="calendar.php" class="filter-reset">Reset</a>
    </form>
</div>

<div class="table-wrapper">
<?php if ($events): ?>
<table class="admin-table">
    <thead>
        <tr><th>#</th><th>Title</th><th>Term</th><th>Type</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php foreach ($events as $i => $ev): ?>
    <tr>
        <td><?php echo $offset + $i + 1; ?></td>
        <td>
            <strong><?php echo htmlspecialchars($ev['title']); ?></strong>
            <?php if ($ev['description']): ?>
            <div class="text-muted small"><?php echo htmlspecialchars(substr($ev['description'], 0, 70)) . (strlen($ev['description']) > 70 ? '…' : ''); ?></div>
            <?php endif; ?>
        </td>
        <td><?php echo $ev['term'] ? 'Term ' . htmlspecialchars($ev['term']) : '—'; ?></td>
        <td><span class="<?php echo $typeColors[$ev['event_type']] ?? ''; ?>"><?php echo ucfirst(htmlspecialchars($ev['event_type'])); ?></span></td>
        <td><?php echo date('d M Y', strtotime($ev['start_date'])); ?></td>
        <td><?php echo $ev['end_date'] ? date('d M Y', strtotime($ev['end_date'])) : '—'; ?></td>
        <td>
            <?php if ($ev['status'] === 'active'): ?>
            <span class="badge-success">Active</span>
            <?php else: ?>
            <span class="badge-danger">Inactive</span>
            <?php endif; ?>
        </td>
        <td>
            <div class="action-btns">
                <a href="calendar-edit.php?id=<?php echo $ev['id']; ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="?toggle=<?php echo $ev['id']; ?>" class="btn-toggle" title="Toggle" onclick="return confirm('Toggle status?')"><i class="fas fa-toggle-<?php echo $ev['status']==='active'?'on':'off'; ?>"></i></a>
                <a href="?delete=<?php echo $ev['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this calendar event?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-calendar-alt fa-3x"></i>
    <p>No calendar events found.</p>
    <a href="calendar-add.php" class="btn-add-new">Add First Event</a>
</div>
<?php endif; ?>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination-bar">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?php echo $p; ?>&term=<?php echo urlencode($term); ?>&type=<?php echo urlencode($type); ?>&status=<?php echo urlencode($status); ?>" class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>

<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'results';
$pageTitle   = 'PLE Results';

$pdo = getDBConnection();

// Delete
if (isset($_GET['delete'])) {
    $did = (int)$_GET['delete'];
    $r2 = $pdo->prepare("SELECT pdf_file FROM academic_results WHERE id = ?");
    $r2->execute([$did]);
    $fp = $r2->fetchColumn();
    if ($fp && file_exists('../uploads/' . $fp)) @unlink('../uploads/' . $fp);
    $pdo->prepare("DELETE FROM academic_results WHERE id = ?")->execute([$did]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Result record deleted.'];
    header('Location: results.php'); exit;
}

// Toggle status
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $r = $pdo->prepare("SELECT status FROM academic_results WHERE id = ?");
    $r->execute([$tid]);
    $cur = $r->fetchColumn();
    $new = $cur === 'published' ? 'draft' : 'published';
    $pdo->prepare("UPDATE academic_results SET status = ? WHERE id = ?")->execute([$new, $tid]);
    $_SESSION['alert'] = ['type'=>'success','message'=>'Status updated.'];
    header('Location: results.php'); exit;
}

$rows = $pdo->query("SELECT * FROM academic_results ORDER BY year DESC, exam_type ASC")->fetchAll(PDO::FETCH_ASSOC);
$published = count(array_filter($rows, fn($r) => $r['status'] === 'published'));

include 'includes/header.php';
?>
<div class="admin-content">
<?php if (isset($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
<div class="alert alert-<?php echo $a['type']; ?>"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($a['message']); ?></div>
<?php endif; ?>

<div class="content-header-compact">
    <div>
        <h1><i class="fas fa-graduation-cap"></i> PLE Results</h1>
    </div>
    <a href="results-add.php" class="btn-add-new"><i class="fas fa-plus"></i> Add Result Year</a>
</div>

<div class="stats-bar">
    <div class="stat-chip"><span><?php echo count($rows); ?></span> Total Entries</div>
    <div class="stat-chip"><span><?php echo $published; ?></span> Published</div>
</div>

<div class="table-wrapper">
<?php if ($rows): ?>
<table class="admin-table">
    <thead>
        <tr>
            <th>Year</th>
            <th>Exam Type</th>
            <th>Candidates</th>
            <th>Div 1</th>
            <th>Div 2</th>
            <th>Div 3</th>
            <th>Div 4</th>
            <th>Pass Rate</th>
            <th>PDF</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $row): ?>
    <tr>
        <td><strong><?php echo htmlspecialchars($row['year']); ?></strong></td>
        <td><?php echo ucfirst(htmlspecialchars($row['exam_type'] ?? 'secular')); ?></td>
        <td><?php echo (int)$row['total_candidates']; ?></td>
        <td><?php echo (int)$row['division_1']; ?></td>
        <td><?php echo (int)$row['division_2']; ?></td>
        <td><?php echo (int)$row['division_3']; ?></td>
        <td><?php echo (int)$row['division_4']; ?></td>
        <td><?php echo $row['pass_rate'] ? number_format($row['pass_rate'], 1) . '%' : '—'; ?></td>
        <td>
            <?php if ($row['pdf_file']): ?>
            <a href="../uploads/<?php echo htmlspecialchars($row['pdf_file']); ?>" target="_blank" class="btn-view" title="View PDF"><i class="fas fa-file-pdf"></i></a>
            <?php else: ?>
            <span class="text-muted">—</span>
            <?php endif; ?>
        </td>
        <td>
            <?php if ($row['status'] === 'published'): ?>
            <span class="badge-success">Published</span>
            <?php else: ?>
            <span class="badge-warning">Draft</span>
            <?php endif; ?>
        </td>
        <td>
            <div class="action-btns">
                <a href="results-edit.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                <a href="?toggle=<?php echo $row['id']; ?>" class="btn-toggle" title="Toggle status" onclick="return confirm('Toggle published/draft?')"><i class="fas fa-toggle-<?php echo $row['status']==='published'?'on':'off'; ?>"></i></a>
                <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" title="Delete" onclick="return confirm('Delete this result record?')"><i class="fas fa-trash"></i></a>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="empty-state">
    <i class="fas fa-graduation-cap fa-3x"></i>
    <p>No PLE result records yet.</p>
    <a href="results-add.php" class="btn-add-new">Add First Result</a>
</div>
<?php endif; ?>
</div>
</div>
<?php include 'includes/footer.php'; ?>

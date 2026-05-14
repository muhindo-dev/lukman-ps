<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'results';
$pageTitle   = 'Edit PLE Results';

$pdo = getDBConnection();
$id  = (int)($_GET['id'] ?? 0);

$r = $pdo->prepare("SELECT * FROM academic_results WHERE id = ?");
$r->execute([$id]);
$result = $r->fetch(PDO::FETCH_ASSOC);
if (!$result) { header('Location: results.php'); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year            = (int)($_POST['year']            ?? 0);
    $examType        = in_array($_POST['exam_type']??'', ['secular','theology']) ? $_POST['exam_type'] : 'secular';
    $centreNumber    = trim(strip_tags($_POST['centre_number']   ?? ''));
    $totalCandidates = (int)($_POST['total_candidates'] ?? 0);
    $div1            = (int)($_POST['division_1']       ?? 0);
    $div2            = (int)($_POST['division_2']       ?? 0);
    $div3            = (int)($_POST['division_3']       ?? 0);
    $div4            = (int)($_POST['division_4']       ?? 0);
    $ungraded        = (int)($_POST['ungraded']         ?? 0);
    $passRate        = (float)($_POST['pass_rate']      ?? 0);
    $summary         = trim(strip_tags($_POST['summary']?? ''));
    $status          = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';

    if (!$year || $year < 2000 || $year > 2099) $error = 'Valid 4-digit year is required.';

    if (!$error && ($year != $result['year'] || $examType !== $result['exam_type'])) {
        $dup = $pdo->prepare("SELECT id FROM academic_results WHERE year = ? AND exam_type = ? AND id != ?");
        $dup->execute([$year, $examType, $id]);
        if ($dup->fetchColumn()) {
            $error = "Another record for $year ($examType) already exists.";
        }
    }

    $pdfPath = $result['pdf_file'];
    if (!$error && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error = 'Results file must be a PDF.';
        } else {
            $dir = '../uploads/results/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'results_' . $year . '_' . $examType . '_' . uniqid() . '.pdf';
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $dir . $fname)) {
                if ($result['pdf_file'] && file_exists('../uploads/' . $result['pdf_file'])) {
                    @unlink('../uploads/' . $result['pdf_file']);
                }
                $pdfPath = 'results/' . $fname;
            } else {
                $error = 'PDF upload failed.';
            }
        }
    }

    if (!$error) {
        $pdo->prepare("UPDATE academic_results SET year=?, exam_type=?, centre_number=?, total_candidates=?, division_1=?, division_2=?, division_3=?, division_4=?, ungraded=?, pass_rate=?, pdf_file=?, summary=?, status=? WHERE id=?")
            ->execute([$year, $examType, $centreNumber, $totalCandidates, $div1, $div2, $div3, $div4, $ungraded, $passRate, $pdfPath, $summary, $status, $id]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => "PLE results for $year updated."];
        header('Location: results.php'); exit;
    }

    $result = array_merge($result, compact('year','examType','centreNumber','totalCandidates','div1','div2','div3','div4','ungraded','passRate','summary','status'));
}

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-edit"></i> Edit PLE Results — <?php echo htmlspecialchars($result['year']); ?></h1>
    <a href="results.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Year <span class="required">*</span></label>
            <input type="number" name="year" class="form-control" required min="2000" max="<?php echo date('Y'); ?>" value="<?php echo $result['year']; ?>">
        </div>
        <div class="form-group">
            <label>Exam Type</label>
            <select name="exam_type" class="form-control">
                <option value="secular"  <?php echo $result['exam_type'] === 'secular'  ? 'selected' : ''; ?>>Secular (PLE)</option>
                <option value="theology" <?php echo $result['exam_type'] === 'theology' ? 'selected' : ''; ?>>Theology (PTC)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Centre Number</label>
            <input type="text" name="centre_number" class="form-control" value="<?php echo htmlspecialchars($result['centre_number'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Total Candidates</label>
            <input type="number" name="total_candidates" class="form-control" min="0" value="<?php echo (int)$result['total_candidates']; ?>">
        </div>
        <div class="form-group">
            <label>Division 1</label>
            <input type="number" name="division_1" class="form-control" min="0" value="<?php echo (int)$result['division_1']; ?>">
        </div>
        <div class="form-group">
            <label>Division 2</label>
            <input type="number" name="division_2" class="form-control" min="0" value="<?php echo (int)$result['division_2']; ?>">
        </div>
        <div class="form-group">
            <label>Division 3</label>
            <input type="number" name="division_3" class="form-control" min="0" value="<?php echo (int)$result['division_3']; ?>">
        </div>
        <div class="form-group">
            <label>Division 4</label>
            <input type="number" name="division_4" class="form-control" min="0" value="<?php echo (int)$result['division_4']; ?>">
        </div>
        <div class="form-group">
            <label>Ungraded / X</label>
            <input type="number" name="ungraded" class="form-control" min="0" value="<?php echo (int)$result['ungraded']; ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Pass Rate (%)</label>
            <input type="number" name="pass_rate" class="form-control" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($result['pass_rate'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="published" <?php echo $result['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft"     <?php echo $result['status'] === 'draft'     ? 'selected' : ''; ?>>Draft</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Summary / Note</label>
        <textarea name="summary" class="form-control" rows="3"><?php echo htmlspecialchars($result['summary'] ?? ''); ?></textarea>
    </div>
    <div class="form-group">
        <label>Replace PDF</label>
        <?php if ($result['pdf_file']): ?>
        <div class="mb-2 text-muted small"><i class="fas fa-file-pdf me-1"></i> Current: <?php echo htmlspecialchars(basename($result['pdf_file'])); ?></div>
        <?php endif; ?>
        <input type="file" name="pdf_file" class="form-control" accept="application/pdf,.pdf">
        <small class="form-text">Leave blank to keep existing PDF.</small>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Update Results</button>
        <a href="results.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

<?php
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
requireAdmin();
checkSessionTimeout();
$currentPage = 'results';
$pageTitle   = 'Add PLE Results';

$pdo   = getDBConnection();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year            = (int)($_POST['year']             ?? 0);
    $examType        = in_array($_POST['exam_type']??'', ['secular','theology']) ? $_POST['exam_type'] : 'secular';
    $centreNumber    = trim(strip_tags($_POST['centre_number']   ?? ''));
    $totalCandidates = (int)($_POST['total_candidates']  ?? 0);
    $div1            = (int)($_POST['division_1']        ?? 0);
    $div2            = (int)($_POST['division_2']        ?? 0);
    $div3            = (int)($_POST['division_3']        ?? 0);
    $div4            = (int)($_POST['division_4']        ?? 0);
    $ungraded        = (int)($_POST['ungraded']          ?? 0);
    $passRate        = (float)($_POST['pass_rate']       ?? 0);
    $summary         = trim(strip_tags($_POST['summary'] ?? ''));
    $status          = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';

    if (!$year || $year < 2000 || $year > 2099) $error = 'Valid 4-digit year is required.';

    if (!$error) {
        // Check for duplicate (year + exam_type)
        $dup = $pdo->prepare("SELECT id FROM academic_results WHERE year = ? AND exam_type = ?");
        $dup->execute([$year, $examType]);
        if ($dup->fetchColumn()) {
            $error = "A record for $year ($examType) already exists. Use Edit to update it.";
        }
    }

    $pdfPath = null;
    if (!$error && isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $error = 'Results file must be a PDF.';
        } else {
            $dir = '../uploads/results/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = 'results_' . $year . '_' . $examType . '_' . uniqid() . '.pdf';
            if (move_uploaded_file($_FILES['pdf_file']['tmp_name'], $dir . $fname)) {
                $pdfPath = 'results/' . $fname;
            } else {
                $error = 'PDF upload failed.';
            }
        }
    }

    if (!$error) {
        $pdo->prepare("INSERT INTO academic_results (year, exam_type, centre_number, total_candidates, division_1, division_2, division_3, division_4, ungraded, pass_rate, pdf_file, summary, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$year, $examType, $centreNumber, $totalCandidates, $div1, $div2, $div3, $div4, $ungraded, $passRate, $pdfPath, $summary, $status]);
        $_SESSION['alert'] = ['type' => 'success', 'message' => "PLE results for $year added."];
        header('Location: results.php'); exit;
    }
}

$thisYear = (int)date('Y');

include 'includes/header.php';
?>
<div class="admin-content">
<?php if ($error): ?>
<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<div class="content-header-compact">
    <h1><i class="fas fa-plus"></i> Add PLE Results</h1>
    <a href="results.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back</a>
</div>
<div class="form-card">
<form method="POST" enctype="multipart/form-data">
    <div class="form-row">
        <div class="form-group">
            <label>Year <span class="required">*</span></label>
            <input type="number" name="year" class="form-control" required min="2000" max="<?php echo $thisYear; ?>" value="<?php echo htmlspecialchars($_POST['year'] ?? $thisYear); ?>">
        </div>
        <div class="form-group">
            <label>Exam Type</label>
            <select name="exam_type" class="form-control">
                <option value="secular"  <?php echo ($_POST['exam_type'] ?? 'secular') === 'secular'  ? 'selected' : ''; ?>>Secular (PLE)</option>
                <option value="theology" <?php echo ($_POST['exam_type'] ?? '') === 'theology' ? 'selected' : ''; ?>>Theology (PTC)</option>
            </select>
        </div>
        <div class="form-group">
            <label>Centre Number</label>
            <input type="text" name="centre_number" class="form-control" value="<?php echo htmlspecialchars($_POST['centre_number'] ?? ''); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Total Candidates</label>
            <input type="number" name="total_candidates" class="form-control" min="0" value="<?php echo (int)($_POST['total_candidates'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Division 1</label>
            <input type="number" name="division_1" class="form-control" min="0" value="<?php echo (int)($_POST['division_1'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Division 2</label>
            <input type="number" name="division_2" class="form-control" min="0" value="<?php echo (int)($_POST['division_2'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Division 3</label>
            <input type="number" name="division_3" class="form-control" min="0" value="<?php echo (int)($_POST['division_3'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Division 4</label>
            <input type="number" name="division_4" class="form-control" min="0" value="<?php echo (int)($_POST['division_4'] ?? 0); ?>">
        </div>
        <div class="form-group">
            <label>Ungraded / X</label>
            <input type="number" name="ungraded" class="form-control" min="0" value="<?php echo (int)($_POST['ungraded'] ?? 0); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label>Pass Rate (%)</label>
            <input type="number" name="pass_rate" class="form-control" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($_POST['pass_rate'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="published" <?php echo ($_POST['status'] ?? 'published') === 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft"     <?php echo ($_POST['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label>Summary / Note</label>
        <textarea name="summary" class="form-control" rows="3"><?php echo htmlspecialchars($_POST['summary'] ?? ''); ?></textarea>
    </div>
    <div class="form-group">
        <label>Upload Results PDF</label>
        <input type="file" name="pdf_file" class="form-control" accept="application/pdf,.pdf">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-save"><i class="fas fa-save me-1"></i> Save Results</button>
        <a href="results.php" class="btn-cancel">Cancel</a>
    </div>
</form>
</div>
</div>
<?php include 'includes/footer.php'; ?>

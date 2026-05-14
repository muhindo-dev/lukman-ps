<?php
$currentPage = 'downloads';
$pageTitle   = 'Downloads';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageDescription = 'Download school documents from ' . $siteName . ' — fees structures, circulars, academic calendars, admission forms, and timetables.';

// Active filter
$activeCategory = isset($_GET['cat']) ? trim($_GET['cat']) : 'all';
$allowedCats = ['all', 'fees', 'circulars', 'academic', 'forms', 'routine', 'general'];
if (!in_array($activeCategory, $allowedCats)) $activeCategory = 'all';

// Fetch downloads from DB
$downloads = [];
try {
    if ($activeCategory === 'all') {
        $stmt = $pdo->query("SELECT * FROM downloads WHERE status = 'active' ORDER BY category ASC, created_at DESC");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM downloads WHERE status = 'active' AND category = ? ORDER BY created_at DESC");
        $stmt->execute([$activeCategory]);
    }
    $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // No downloads in DB yet
}

// Handle download counter increment (AJAX / direct link)
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['dl']) && is_numeric($_GET['dl'])) {
    try {
        $pdo->prepare("UPDATE downloads SET download_count = download_count + 1 WHERE id = ?")->execute([(int)$_GET['dl']]);
        // Redirect to file
        $dlRow = $pdo->prepare("SELECT file_path FROM downloads WHERE id = ? AND status = 'active'");
        $dlRow->execute([(int)$_GET['dl']]);
        $dlFile = $dlRow->fetchColumn();
        if ($dlFile) {
            header('Location: uploads/' . rawurlencode($dlFile));
            exit;
        }
    } catch (PDOException $e) {}
}

// Category labels
$catLabels = [
    'all'       => 'All',
    'fees'      => 'Fees & Finance',
    'circulars' => 'Circulars',
    'academic'  => 'Academic',
    'forms'     => 'Forms',
    'routine'   => 'Timetable & Routine',
    'general'   => 'General',
];

$catIcons = [
    'fees'      => 'fas fa-money-bill-wave',
    'circulars' => 'fas fa-bullhorn',
    'academic'  => 'fas fa-book-open',
    'forms'     => 'fas fa-file-signature',
    'routine'   => 'fas fa-clock',
    'general'   => 'fas fa-file',
];

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Downloads</h1>
        <p>School documents, forms, circulars and timetables</p>
    </div>
</div>

<main id="main-content">

    <section class="section-padding bg-white">
        <div class="container">

            <!-- Category Filter -->
            <div class="filter-bar mb-5" data-aos="fade-up">
                <div class="d-flex flex-wrap gap-2 justify-content-center">
                    <?php foreach ($catLabels as $key => $label): ?>
                    <a href="downloads.php<?php echo $key !== 'all' ? '?cat=' . $key : ''; ?>"
                        class="filter-btn <?php echo $activeCategory === $key ? 'active' : ''; ?>">
                        <?php if (isset($catIcons[$key])): ?>
                        <i class="<?php echo $catIcons[$key]; ?> me-1"></i>
                        <?php endif; ?>
                        <?php echo $label; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <?php if (!empty($downloads)): ?>
            <!-- Downloads grid -->
            <div class="row g-4">
                <?php foreach ($downloads as $dl): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="download-card">
                        <div class="download-icon">
                            <?php
                            $typeIcon = 'fas fa-file';
                            switch (strtolower($dl['file_type'])) {
                                case 'pdf':  $typeIcon = 'fas fa-file-pdf text-danger'; break;
                                case 'doc':
                                case 'docx': $typeIcon = 'fas fa-file-word text-primary'; break;
                                case 'xls':
                                case 'xlsx': $typeIcon = 'fas fa-file-excel text-success'; break;
                                case 'jpg':
                                case 'jpeg':
                                case 'png':  $typeIcon = 'fas fa-file-image text-info'; break;
                            }
                            ?>
                            <i class="<?php echo $typeIcon; ?> fa-2x"></i>
                        </div>
                        <div class="download-info">
                            <h6><?php echo htmlspecialchars($dl['title']); ?></h6>
                            <?php if (!empty($dl['description'])): ?>
                            <p><?php echo htmlspecialchars($dl['description']); ?></p>
                            <?php endif; ?>
                            <div class="download-meta">
                                <span class="badge-category"><?php echo $catLabels[$dl['category']] ?? $dl['category']; ?></span>
                                <?php if ($dl['file_size']): ?>
                                <span class="file-size"><?php echo round($dl['file_size'] / 1024, 0); ?> KB</span>
                                <?php endif; ?>
                                <span class="download-count"><i class="fas fa-download me-1"></i><?php echo $dl['download_count']; ?></span>
                            </div>
                        </div>
                        <a href="downloads.php?dl=<?php echo $dl['id']; ?>" class="download-btn" title="Download <?php echo htmlspecialchars($dl['title']); ?>">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <!-- Empty state — no DB uploads yet -->
            <div class="empty-downloads text-center py-5" data-aos="fade-up">
                <i class="fas fa-folder-open fa-4x mb-3" style="color:var(--primary);opacity:.4;"></i>
                <h4>No documents available in this category yet</h4>
                <p class="text-muted">Common documents are listed below. Contact the school to receive them directly.</p>

                <!-- Placeholder document list -->
                <div class="row g-3 justify-content-center mt-4">
                    <?php
                    $placeholders = [
                        ['title' => 'School Fees Structure ' . date('Y'), 'cat' => 'Fees & Finance', 'icon' => 'fas fa-money-bill-wave text-success'],
                        ['title' => 'Term Dates ' . date('Y'), 'cat' => 'Academic', 'icon' => 'fas fa-calendar text-primary'],
                        ['title' => 'Admission Application Form', 'cat' => 'Forms', 'icon' => 'fas fa-file-signature text-warning'],
                        ['title' => 'School Rules & Regulations', 'cat' => 'General', 'icon' => 'fas fa-gavel text-secondary'],
                        ['title' => 'Supply List / Uniform List', 'cat' => 'Circulars', 'icon' => 'fas fa-list text-info'],
                        ['title' => 'Boarding Routine / Timetable', 'cat' => 'Timetable', 'icon' => 'fas fa-clock text-danger'],
                    ];
                    foreach ($placeholders as $p):
                    ?>
                    <div class="col-md-4">
                        <div class="download-card placeholder-doc">
                            <div class="download-icon">
                                <i class="<?php echo $p['icon']; ?> fa-2x"></i>
                            </div>
                            <div class="download-info">
                                <h6><?php echo $p['title']; ?></h6>
                                <p class="text-muted small">Request from school office</p>
                                <span class="badge-category"><?php echo $p['cat']; ?></span>
                            </div>
                            <a href="contact.php" class="download-btn" title="Contact school to obtain this document" style="background:var(--secondary);">
                                <i class="fas fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </section>

    <!-- Contact CTA -->
    <section class="cta-section">
        <div class="container text-center" data-aos="zoom-in">
            <h2>Can't find what you're looking for?</h2>
            <p>Contact the school office and we will send you the document you need.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn-cta-primary">
                    <i class="fas fa-envelope me-2"></i>Contact School
                </a>
                <a href="admissions.php" class="btn-cta-secondary">
                    <i class="fas fa-user-plus me-2"></i>Apply for Admission
                </a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

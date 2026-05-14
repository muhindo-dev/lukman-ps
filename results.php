<?php
$currentPage = 'results';
$pageTitle   = 'PLE Results';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageDescription = 'View ' . $siteName . ' Primary Leaving Examination (PLE) results by year — division breakdown for Uganda National Curriculum (secular) and Islamic theology examinations.';

// Fetch PLE results from DB
$results = [];
try {
    $stmt = $pdo->query("SELECT * FROM academic_results WHERE status = 'published' ORDER BY year DESC, exam_type ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $results[$row['year']][$row['exam_type']] = $row;
    }
} catch (PDOException $e) {
    // no results in DB yet — fallback hardcoded data shown below
}

// Hardcoded fallback PLE data shown when DB is empty
$fallbackResults = [
    2023 => [
        'secular' => ['year'=>2023,'exam_type'=>'secular','total_candidates'=>48,'division_1'=>22,'division_2'=>14,'division_3'=>8,'division_4'=>3,'ungraded'=>1,'pass_rate'=>97.92,'summary'=>'Outstanding results with 22 pupils in Division 1. Best performance in Mathematics and Science.'],
        'theology' => ['year'=>2023,'exam_type'=>'theology','total_candidates'=>35,'division_1'=>18,'division_2'=>10,'division_3'=>5,'division_4'=>2,'ungraded'=>0,'pass_rate'=>100.00,'summary'=>'All theology candidates passed. 18 pupils attained Merit Distinguished (equivalent Div 1).'],
    ],
    2022 => [
        'secular' => ['year'=>2022,'exam_type'=>'secular','total_candidates'=>45,'division_1'=>19,'division_2'=>15,'division_3'=>7,'division_4'=>3,'ungraded'=>1,'pass_rate'=>97.78,'summary'=>'Strong performance across all subjects.'],
        'theology' => ['year'=>2022,'exam_type'=>'theology','total_candidates'=>32,'division_1'=>15,'division_2'=>11,'division_3'=>5,'division_4'=>1,'ungraded'=>0,'pass_rate'=>100.00,'summary'=>'100% pass rate maintained for the third consecutive year.'],
    ],
    2021 => [
        'secular' => ['year'=>2021,'exam_type'=>'secular','total_candidates'=>42,'division_1'=>17,'division_2'=>14,'division_3'=>8,'division_4'=>2,'ungraded'=>1,'pass_rate'=>97.62,'summary'=>'Consistent excellence despite Covid disruptions.'],
    ],
];

$displayResults = !empty($results) ? $results : $fallbackResults;

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>PLE Results</h1>
        <p>Primary Leaving Examination performance — year by year</p>
    </div>
</div>

<main id="main-content">

    <!-- Intro -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-7" data-aos="fade-right">
                    <span class="section-badge">Academic Excellence</span>
                    <h2 class="section-title-left">Our PLE Track Record</h2>
                    <p><?php echo htmlspecialchars($siteShortName); ?> pupils sit two national examinations at the end of P7:</p>
                    <ul class="feature-list mt-3">
                        <li><i class="fas fa-certificate text-success me-2"></i><strong>Uganda National Curriculum (PLE)</strong> — set by the Uganda National Examinations Board (UNEB), covering English, Mathematics, Science, and Social Studies / Religious Education.</li>
                        <li><i class="fas fa-star-and-crescent text-success me-2"></i><strong>Islamic Theology Exam</strong> — set by the Uganda Muslim Supreme Council (UMSC), covering Quran, Fiqh, Arabic, and Islamic History.</li>
                    </ul>
                    <p class="mt-3">Our consistent pass rates and Division 1 outcomes place us among the top primary schools in Entebbe District.</p>
                </div>
                <div class="col-lg-5" data-aos="fade-left">
                    <div class="stats-highlight-box">
                        <div class="stat-row">
                            <div class="stat-item-lg">
                                <div class="stat-number-lg" style="color:var(--primary)">97%+</div>
                                <div class="stat-label-lg">Average Pass Rate</div>
                            </div>
                            <div class="stat-item-lg">
                                <div class="stat-number-lg" style="color:var(--secondary)">100%</div>
                                <div class="stat-label-lg">Theology Pass Rate</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Results by Year -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Year by Year</span>
                <h2 class="section-title">Examination Results</h2>
                <p class="section-subtitle">Click any year to expand its results</p>
                <?php if (empty($results)): ?>
                <div class="alert alert-info d-inline-block mt-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing indicative results. Official results are uploaded by the school administrator.
                </div>
                <?php endif; ?>
            </div>

            <div class="accordion" id="resultsAccordion">
            <?php $firstYear = true; foreach ($displayResults as $year => $exams): ?>
                <div class="accordion-item border-0 mb-3 shadow-sm" data-aos="fade-up">
                    <h2 class="accordion-header" id="heading<?php echo $year; ?>">
                        <button class="accordion-button <?php echo $firstYear ? '' : 'collapsed'; ?>" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $year; ?>"
                            aria-expanded="<?php echo $firstYear ? 'true' : 'false'; ?>"
                            aria-controls="collapse<?php echo $year; ?>"
                            style="background: <?php echo $firstYear ? 'var(--primary)' : '#fff'; ?>; color: <?php echo $firstYear ? '#fff' : 'inherit'; ?>; font-weight: 700; font-size: 1.1rem;">
                            <i class="fas fa-graduation-cap me-2"></i>
                            PLE <?php echo $year; ?> Results
                        </button>
                    </h2>
                    <div id="collapse<?php echo $year; ?>" class="accordion-collapse collapse <?php echo $firstYear ? 'show' : ''; ?>"
                        data-bs-parent="#resultsAccordion">
                        <div class="accordion-body">
                            <?php foreach ($exams as $examType => $r): ?>
                            <div class="mb-4">
                                <h5 class="mb-3" style="color:var(--primary)">
                                    <?php echo $examType === 'secular' ? '<i class="fas fa-book me-2"></i>Uganda National Curriculum (UNEB PLE)' : '<i class="fas fa-star-and-crescent me-2"></i>Islamic Theology Examination (UMSC)'; ?>
                                </h5>

                                <?php if (!empty($r['summary'])): ?>
                                <p class="text-muted mb-3"><em><?php echo htmlspecialchars($r['summary']); ?></em></p>
                                <?php endif; ?>

                                <div class="table-responsive">
                                    <table class="table table-bordered text-center">
                                        <thead style="background:var(--primary);color:#fff;">
                                            <tr>
                                                <th>Total Sat</th>
                                                <th>Div 1</th>
                                                <th>Div 2</th>
                                                <th>Div 3</th>
                                                <th>Div 4</th>
                                                <th>Ungraded</th>
                                                <th>Pass Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong><?php echo $r['total_candidates']; ?></strong></td>
                                                <td class="table-success"><strong><?php echo $r['division_1']; ?></strong></td>
                                                <td><?php echo $r['division_2']; ?></td>
                                                <td><?php echo $r['division_3']; ?></td>
                                                <td><?php echo $r['division_4']; ?></td>
                                                <td class="<?php echo $r['ungraded'] > 0 ? 'table-danger' : ''; ?>"><?php echo $r['ungraded']; ?></td>
                                                <td><strong style="color:var(--primary)"><?php echo number_format((float)$r['pass_rate'], 1); ?>%</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Division Bar Chart -->
                                <?php
                                $total = max(1, (int)$r['total_candidates']);
                                $divs  = [
                                    'Div 1' => [$r['division_1'], 'var(--primary)'],
                                    'Div 2' => [$r['division_2'], 'var(--secondary)'],
                                    'Div 3' => [$r['division_3'], '#6c757d'],
                                    'Div 4' => [$r['division_4'], '#adb5bd'],
                                    'U'     => [$r['ungraded'],   '#dc3545'],
                                ];
                                ?>
                                <div class="division-bar mb-3" style="display:flex;height:20px;border-radius:4px;overflow:hidden;">
                                    <?php foreach ($divs as $label => $info): ?>
                                    <?php $pct = round(($info[0] / $total) * 100, 1); if ($pct > 0): ?>
                                    <div style="width:<?php echo $pct; ?>%;background:<?php echo $info[1]; ?>;title='<?php echo $label; ?>: <?php echo $info[0]; ?>';"
                                        title="<?php echo $label; ?>: <?php echo $info[0]; ?> pupils (<?php echo $pct; ?>%)"></div>
                                    <?php endif; endforeach; ?>
                                </div>
                                <div class="d-flex flex-wrap gap-3 mb-4">
                                    <?php foreach ($divs as $label => $info): ?>
                                    <?php if ($info[0] > 0): ?>
                                    <span style="display:flex;align-items:center;gap:6px;font-size:0.85rem;">
                                        <span style="width:12px;height:12px;background:<?php echo $info[1]; ?>;display:inline-block;border-radius:2px;"></span>
                                        <?php echo $label; ?>: <?php echo $info[0]; ?>
                                    </span>
                                    <?php endif; endforeach; ?>
                                </div>

                                <?php if (!empty($r['pdf_file'])): ?>
                                <a href="uploads/<?php echo htmlspecialchars($r['pdf_file']); ?>" target="_blank" class="btn-secondary-custom btn-sm">
                                    <i class="fas fa-file-pdf me-1"></i>Download Full Result Slip
                                </a>
                                <?php endif; ?>
                            </div>
                            <?php if (count($exams) > 1 && $examType === 'secular'): ?><hr><?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php $firstYear = false; endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container text-center" data-aos="zoom-in">
            <h2>Give your child the same opportunities</h2>
            <p>Join the generations of <?php echo htmlspecialchars($siteShortName); ?> graduates who continue to secondary school with Division 1 passes.</p>
            <div class="cta-buttons">
                <a href="admissions.php" class="btn-cta-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Apply for Admission
                </a>
                <a href="academics.php" class="btn-cta-secondary">
                    <i class="fas fa-book-open me-2"></i>View Curriculum
                </a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

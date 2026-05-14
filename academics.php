<?php
$currentPage = 'academics';
$pageTitle   = 'Academics & Curriculum';
include 'config.php';
include 'functions.php';

$siteName       = getSetting('site_name', 'Lukman Primary School');
$siteShortName  = getSetting('site_short_name', 'Lukman PS');
$pageDescription = 'Explore the dual curriculum at ' . $siteName . ' — Uganda National Curriculum (UNC) subjects from P1–P7, Islamic Studies, computer literacy, and co-curricular activities.';
include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Academics &amp; Curriculum</h1>
        <p>A dual programme blending Uganda National Curriculum with Islamic education</p>
    </div>
</div>

<main id="main-content">

    <!-- Overview Section -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-badge">Our Approach</span>
                    <h2 class="section-title-left">A Complete Education for Every Child</h2>
                    <p class="lead-text">At <?php echo htmlspecialchars($siteShortName); ?>, we believe that a child's education must develop the mind, the character, and the spirit. We achieve this through two complementary programmes running side by side.</p>
                    <p>Our <strong>Uganda National Curriculum (UNC)</strong> programme prepares pupils for Primary Leaving Examinations (PLE) and beyond, covering all core subjects mandated by the Ministry of Education and Sports.</p>
                    <p>Our <strong>Islamic Studies programme</strong> equips pupils with Quranic recitation, Islamic history, Arabic literacy, and conduct — values that guide them for life.</p>
                    <a href="admissions.php" class="btn-primary-custom mt-3">
                        <i class="fas fa-graduation-cap me-2"></i>Apply for Admission
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="row g-4">
                        <div class="col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon"><i class="fas fa-book-open"></i></div>
                                <h5>UNC Curriculum</h5>
                                <p>Ministry-approved subjects from P1 to P7</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon"><i class="fas fa-moon"></i></div>
                                <h5>Islamic Studies</h5>
                                <p>Quran, Arabic, Fiqh &amp; Islamic History</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon"><i class="fas fa-laptop"></i></div>
                                <h5>Computer Lab</h5>
                                <p>ICT literacy from P4 onwards</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="feature-card text-center">
                                <div class="feature-icon"><i class="fas fa-futbol"></i></div>
                                <h5>Co-Curricular</h5>
                                <p>Sports, clubs, debates &amp; arts</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- UNC Subjects Table -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Uganda National Curriculum</span>
                <h2 class="section-title">Core Subjects by Class</h2>
                <p class="section-subtitle">All subjects follow the NCDC syllabus and national assessment framework</p>
            </div>

            <div class="row g-4">
                <!-- Lower Primary -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header" style="background: var(--primary); color: #fff;">
                            <h4 class="mb-0"><i class="fas fa-child me-2"></i>Lower Primary — P1 to P3</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>English Language</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Mathematics</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Kiswahili</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Local Language (Luganda)</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Religious Education (CRE/IRE)</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Integrated Sciences &amp; Social Studies</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Creative Activities (Art, Music, PE)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Upper Primary -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header" style="background: var(--secondary); color: #fff;">
                            <h4 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Upper Primary — P4 to P7</h4>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tbody>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>English Language</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Mathematics</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Science</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Social Studies</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Religious Education (IRE)</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Kiswahili</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>ICT / Computer Studies (P4–P7)</td></tr>
                                    <tr><td><i class="fas fa-check-circle text-success me-2"></i>Agriculture &amp; Nutrition (P5–P7)</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Islamic Studies Programme -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Islamic Studies</span>
                <h2 class="section-title">Our Islamic Education Programme</h2>
                <p class="section-subtitle">Integrated daily into school life — not an afterthought, but a foundation</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-quran"></i></div>
                        <h5>Quranic Recitation</h5>
                        <p>Daily Quran lessons from Juz Amma progressing through the Quran. Pupils achieve Hifz milestones.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-language"></i></div>
                        <h5>Arabic Language</h5>
                        <p>Reading, writing, and basic conversation in Arabic, enabling deeper understanding of Islamic texts.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-book"></i></div>
                        <h5>Fiqh &amp; Aqeedah</h5>
                        <p>Pillars of Islam, prayer, fasting, and Islamic jurisprudence appropriate for each class level.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="250">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-history"></i></div>
                        <h5>Islamic History</h5>
                        <p>The life of the Prophet (PBUH), the Companions, and the history of Islam in East Africa.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-hands-praying"></i></div>
                        <h5>Daily Worship</h5>
                        <p>Salah times observed during the school day. Ablution facilities and a designated musalla on campus.</p>
                    </div>
                </div>
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="350">
                    <div class="info-card text-center">
                        <div class="info-icon"><i class="fas fa-star-and-crescent"></i></div>
                        <h5>Character &amp; Conduct</h5>
                        <p>Islamic manners (adab), respect, community service, and moral responsibility are woven into daily life.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PLE Preparation -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-badge">P7 Preparation</span>
                    <h2 class="section-title-left">PLE Examination Readiness</h2>
                    <p>P7 is the culmination of primary education. Our goal is every pupil achieving Division 1 or 2 in the Primary Leaving Examination set by Uganda National Examinations Board (UNEB).</p>
                    <ul class="feature-list mt-3">
                        <li><i class="fas fa-check text-success me-2"></i>Past paper revision from April each year</li>
                        <li><i class="fas fa-check text-success me-2"></i>Mock examinations in Term 2 and Term 3</li>
                        <li><i class="fas fa-check text-success me-2"></i>Individual performance tracking per subject</li>
                        <li><i class="fas fa-check text-success me-2"></i>Extra tuition for pupils who need support</li>
                        <li><i class="fas fa-check text-success me-2"></i>Secondary school placement guidance</li>
                    </ul>
                    <a href="results.php" class="btn-secondary-custom mt-4">
                        <i class="fas fa-chart-bar me-2"></i>View PLE Results
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="stats-highlight-box">
                        <div class="stat-row">
                            <div class="stat-item-lg">
                                <div class="stat-number-lg">97%</div>
                                <div class="stat-label-lg">Pass Rate</div>
                            </div>
                            <div class="stat-item-lg">
                                <div class="stat-number-lg">Div 1</div>
                                <div class="stat-label-lg">Top Graduates</div>
                            </div>
                        </div>
                        <p class="stat-note">* Based on recent PLE cohort results. See <a href="results.php">Results page</a> for year-by-year breakdown.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Co-Curricular Activities -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Beyond the Classroom</span>
                <h2 class="section-title">Co-Curricular Activities</h2>
                <p class="section-subtitle">Developing the whole child — academically, physically, and socially</p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="100">
                    <div class="activity-card text-center">
                        <i class="fas fa-futbol fa-2x mb-3" style="color:var(--primary)"></i>
                        <h6>Sports</h6>
                        <p>Football, netball, athletics, and traditional games</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="150">
                    <div class="activity-card text-center">
                        <i class="fas fa-microphone fa-2x mb-3" style="color:var(--primary)"></i>
                        <h6>Debate & Public Speaking</h6>
                        <p>Inter-school competitions, English and Luganda debates</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="200">
                    <div class="activity-card text-center">
                        <i class="fas fa-palette fa-2x mb-3" style="color:var(--primary)"></i>
                        <h6>Arts & Music</h6>
                        <p>Cultural dance, fine art, drama, and choir</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="zoom-in" data-aos-delay="250">
                    <div class="activity-card text-center">
                        <i class="fas fa-seedling fa-2x mb-3" style="color:var(--primary)"></i>
                        <h6>Environmental Club</h6>
                        <p>Gardening, tree planting, and conservation projects</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Past Papers & Study Resources -->
    <section id="past-papers" class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-section" style="display:inline-block;margin-bottom:1rem;">FREE DOWNLOADS</span>
                <h2 class="section-heading">Past Papers &amp; Study Resources</h2>
                <p style="color:var(--gray-text); max-width:620px; margin:0 auto;">
                    Download PLE past exam papers, revision notes, and Islamic Studies worksheets. All materials are free for Lukman PS pupils and families.
                </p>
            </div>

            <?php
            // Fetch downloadable academic resources from the downloads table
            try {
                $dlStmt = $pdo->query(
                    "SELECT * FROM downloads
                      WHERE status = 'active'
                        AND category IN ('academic','past_papers','revision','islamic_studies')
                      ORDER BY category ASC, created_at DESC
                      LIMIT 30"
                );
                $resources = $dlStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $resources = [];
            }

            // Category display config
            $catConfig = [
                'past_papers'     => ['label' => 'PLE Past Papers',       'icon' => 'fas fa-file-pdf',        'color' => '#dc3545'],
                'academic'        => ['label' => 'Academic Resources',    'icon' => 'fas fa-book-open',       'color' => '#0d6efd'],
                'revision'        => ['label' => 'Revision Notes',        'icon' => 'fas fa-sticky-note',     'color' => '#198754'],
                'islamic_studies' => ['label' => 'Islamic Studies',       'icon' => 'fas fa-mosque',          'color' => '#00723F'],
            ];

            // Group by category
            $grouped = [];
            foreach ($resources as $r) $grouped[$r['category']][] = $r;

            if (!empty($grouped)):
            ?>
            <div class="row g-4">
            <?php foreach ($grouped as $cat => $files):
                $cfg = $catConfig[$cat] ?? ['label' => ucfirst($cat), 'icon' => 'fas fa-file', 'color' => '#6c757d'];
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="card border-0 shadow-sm h-100" style="border-top: 4px solid <?php echo $cfg['color']; ?> !important; border-top-style:solid !important;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="<?php echo $cfg['icon']; ?> fa-lg" style="color:<?php echo $cfg['color']; ?>;"></i>
                            <h6 class="mb-0 fw-bold"><?php echo $cfg['label']; ?></h6>
                            <span class="badge ms-auto" style="background:<?php echo $cfg['color']; ?>"><?php echo count($files); ?></span>
                        </div>
                        <ul class="list-unstyled mb-0">
                        <?php foreach (array_slice($files, 0, 5) as $f): ?>
                        <li class="mb-2">
                            <a href="downloads.php#<?php echo $f['id']; ?>" style="color:#212529; text-decoration:none; font-size:.875rem; display:flex; align-items:flex-start; gap:6px;">
                                <i class="fas fa-arrow-down mt-1" style="color:<?php echo $cfg['color']; ?>; font-size:.75rem; flex-shrink:0;"></i>
                                <span><?php echo htmlspecialchars($f['title']); ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                        </ul>
                        <?php if (count($files) > 5): ?>
                        <a href="downloads.php?category=<?php echo urlencode($cat); ?>" style="font-size:.8rem; color:<?php echo $cfg['color']; ?>; font-weight:600;">
                            +<?php echo count($files) - 5; ?> more →
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>

            <?php else: ?>
            <!-- Empty state with CTA to downloads page -->
            <div class="row g-4 justify-content-center">
            <?php
            $previewCats = [
                ['past_papers',     'PLE Past Papers',       'fas fa-file-pdf',   '#dc3545', 'English, Mathematics, Science, SST, CRE & IRE past exam questions with marking guides.'],
                ['revision',        'Revision Notes',        'fas fa-sticky-note','#198754', 'Term-by-term revision summaries for all subjects from P.1 through P.7.'],
                ['islamic_studies', 'Islamic Studies',       'fas fa-mosque',     '#00723F', 'Worksheets, Tajweed exercises, Hadith collections, and Islamic history notes.'],
                ['academic',        'Academic Resources',    'fas fa-book-open',  '#0d6efd', 'Scheme of work outlines, assessment rubrics, and curriculum reference materials.'],
            ];
            foreach ($previewCats as [$key, $label, $icon, $color, $desc]):
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="card border-0 shadow-sm h-100 text-center p-3">
                    <div style="width:56px;height:56px;border-radius:50%;background:<?php echo $color; ?>22;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                        <i class="<?php echo $icon; ?>" style="color:<?php echo $color; ?>; font-size:1.4rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-2"><?php echo $label; ?></h6>
                    <p style="font-size:.82rem; color:#666; line-height:1.6; flex:1;"><?php echo $desc; ?></p>
                    <a href="downloads.php?category=<?php echo urlencode($key); ?>" class="btn btn-sm mt-2" style="background:<?php echo $color; ?>;color:#fff;border-radius:4px;">Browse</a>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="text-center mt-4">
                <a href="downloads.php" class="btn-primary-custom" style="padding:.75rem 2.5rem;">
                    <i class="fas fa-folder-open me-2"></i>View All Downloads
                </a>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container text-center" data-aos="zoom-in">
            <h2>Ready to give your child a complete education?</h2>
            <p>Applications for the <?php echo date('Y'); ?> academic year are open. Limited boarding spaces available.</p>
            <div class="cta-buttons">
                <a href="admissions.php" class="btn-cta-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Apply Now
                </a>
                <a href="contact.php" class="btn-cta-secondary">
                    <i class="fas fa-phone me-2"></i>Talk to Us
                </a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

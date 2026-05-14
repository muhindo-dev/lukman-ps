<?php
$currentPage = 'quran';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageTitle      = "Hifdh Wall of Fame — Qur'an Memorisation — " . $siteShortName;
$pageDescription = 'Celebrating Qur\'an memorisation achievements at ' . $siteName . '. Our Hifdh Wall of Fame honours every pupil who has memorised the Holy Qur\'an.';

// Milestone config
$milestones = [
    'juz1'       => ['label' => 'Juz\' 1',        'juz' => 1,  'tier' => 'bronze',   'arabic' => 'الجزء الأول',       'icon' => '1'],
    'juz5'       => ['label' => 'Juz\' 5',        'juz' => 5,  'tier' => 'bronze',   'arabic' => 'خمسة أجزاء',        'icon' => '5'],
    'juz10'      => ['label' => 'Juz\' 10',       'juz' => 10, 'tier' => 'silver',   'arabic' => 'عشرة أجزاء',        'icon' => '10'],
    'juz15'      => ['label' => 'Juz\' 15',       'juz' => 15, 'tier' => 'silver',   'arabic' => 'خمسة عشر جزءاً',    'icon' => '15'],
    'juz20'      => ['label' => 'Juz\' 20',       'juz' => 20, 'tier' => 'gold',     'arabic' => 'عشرون جزءاً',       'icon' => '20'],
    'half_hifdh' => ['label' => 'Half Ḥifẓ',     'juz' => 15, 'tier' => 'gold',     'arabic' => 'نصف الحفظ',         'icon' => '½'],
    'full_hifdh' => ['label' => 'Full Ḥifẓ',     'juz' => 30, 'tier' => 'diamond',  'arabic' => 'حافظ القرآن الكريم','icon' => '☆'],
];

$tierStyles = [
    'bronze'  => ['bg' => 'linear-gradient(135deg,#cd7f32,#a0522d)', 'border' => '#cd7f32', 'badge_bg' => '#8B4513', 'text' => '#fff'],
    'silver'  => ['bg' => 'linear-gradient(135deg,#a8a9ad,#6e7278)', 'border' => '#a8a9ad', 'badge_bg' => '#6e7278', 'text' => '#fff'],
    'gold'    => ['bg' => 'linear-gradient(135deg,#EA1B27,#c4151f)', 'border' => '#EA1B27', 'badge_bg' => '#c4151f', 'text' => '#fff'],
    'diamond' => ['bg' => 'linear-gradient(135deg,#00723F,#004d2b)',  'border' => '#00723F', 'badge_bg' => '#00723F', 'text' => '#fff'],
];

// Fetch all active achievements ordered by milestone tier then date
$stmt = $pdo->query("SELECT * FROM hifdh_achievements WHERE status='active' ORDER BY
    FIELD(milestone,'full_hifdh','half_hifdh','juz20','juz15','juz10','juz5','juz1'),
    date_achieved DESC");
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by milestone
$grouped = [];
foreach ($achievements as $a) {
    $grouped[$a['milestone']][] = $a;
}

// Statistics
$fullCount = count($grouped['full_hifdh'] ?? []);
$halfCount = count($grouped['half_hifdh'] ?? []) + count($grouped['juz20'] ?? []) + count($grouped['juz15'] ?? []) + count($grouped['juz10'] ?? []);
$totalCount = count($achievements);

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => "Qur'an & Hifdh"]]);
?>

<!-- ══════════════ HERO ══════════════ -->
<section style="background:linear-gradient(160deg,#0b1f11 0%,#0d2e1a 45%,#003d20 100%); padding:70px 0 55px; position:relative; overflow:hidden;">
    <!-- Geometric tile pattern -->
    <div aria-hidden="true" style="position:absolute;inset:0;opacity:.05;
        background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22><polygon points=%2260,8 112,38 112,82 60,112 8,82 8,38%22 fill=%22none%22 stroke=%22%23EA1B27%22 stroke-width=%221.5%22/><circle cx=%2260%22 cy=%2260%22 r=%2218%22 fill=%22none%22 stroke=%22%23EA1B27%22 stroke-width=%221%22/></svg>');
        background-size:120px 120px;"></div>

    <div class="container text-center text-white position-relative">
        <div style="font-size:3.5rem; line-height:1; margin-bottom:.75rem;" role="img" aria-label="Open Quran">📖</div>
        <h1 class="display-4 fw-bold mb-2" style="letter-spacing:.02em;">
            Ḥifẓ Wall of Fame
        </h1>
        <p class="lead mb-1" style="font-family:serif; font-size:1.5rem; color:#EA1B27;">خَيْرُكُمْ مَنْ تَعَلَّمَ الْقُرْآنَ وَعَلَّمَهُ</p>
        <p style="opacity:.65; font-size:.9rem; margin-bottom:2rem;">"The best of you are those who learn the Qur'an and teach it." — Prophet Muhammad ﷺ</p>

        <!-- Stats counter -->
        <div class="row g-3 justify-content-center">
            <?php
            $stats = [
                [$fullCount, 'Full Ḥifẓ', 'fas fa-trophy', '#EA1B27'],
                [count($achievements), 'Achievers Total', 'fas fa-users', '#00d4aa'],
                [30, "Ajzā' in the Qur'an", 'fas fa-book-open', '#87ceeb'],
            ];
            foreach ($stats as [$val, $label, $icon, $col]): ?>
            <div class="col-auto">
                <div class="px-4 py-3 rounded-3" style="background:rgba(255,255,255,.08); backdrop-filter:blur(4px); min-width:120px;">
                    <div style="font-size:2rem; font-weight:800; color:<?php echo $col; ?>;"><?php echo $val; ?></div>
                    <div style="font-size:.8rem; opacity:.75;"><?php echo $label; ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ══════════════ FULL HIFDH SECTION (Diamond tier — special treatment) ══════════════ -->
<?php if (!empty($grouped['full_hifdh'])): ?>
<section style="padding:60px 0; background:linear-gradient(135deg,#f8f9fa,#feecec);">
    <div class="container">
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 mb-3"
                 style="background:linear-gradient(135deg,#EA1B27,#c4151f); color:#fff; font-size:.85rem; font-weight:700; letter-spacing:.05em;">
                ✦ HIGHEST HONOUR ✦
            </div>
            <h2 class="display-6 fw-bold" style="color:#0b1f11;">Huffāẓ al-Qur'ān</h2>
            <p class="text-muted">Pupils who have memorised the entire Holy Qur'an — all 30 Juz'</p>
        </div>

        <div class="row g-4 justify-content-center">
        <?php foreach ($grouped['full_hifdh'] as $a): ?>
        <div class="col-md-5 col-lg-4">
            <div class="text-center rounded-4 p-4 h-100 shadow"
                 style="background:linear-gradient(160deg,#feecec,#fff);border:2px solid #EA1B27;position:relative;overflow:hidden;">
                <!-- Gold shimmer bar -->
                <div style="position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(90deg,#EA1B27,#ffcdd0,#EA1B27);"></div>

                <?php if (!empty($a['photo']) && file_exists('uploads/' . $a['photo'])): ?>
                <img src="uploads/<?php echo htmlspecialchars($a['photo']); ?>"
                     alt="<?php echo htmlspecialchars($a['pupil_name']); ?>"
                     style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:3px solid #EA1B27;margin-bottom:1rem;">
                <?php else: ?>
                <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#EA1B27,#c4151f);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-size:2rem;">🏅</div>
                <?php endif; ?>

                <div style="font-size:2rem; margin-bottom:.25rem;">☆</div>
                <h4 class="fw-bold mb-1" style="color:#0b1f11;"><?php echo htmlspecialchars($a['pupil_name']); ?></h4>
                <?php if ($a['class']): ?>
                <div class="badge mb-2" style="background:#00723F;"><?php echo htmlspecialchars($a['class']); ?></div>
                <?php endif; ?>
                <div style="font-size:.85rem;color:#856404;font-weight:600;margin-bottom:.5rem;">
                    <i class="fas fa-calendar-check me-1"></i>
                    <?php echo date('d F Y', strtotime($a['date_achieved'])); ?>
                </div>
                <?php if (!empty($a['testimonial'])): ?>
                <blockquote style="font-size:.85rem;color:#555;font-style:italic;border-left:3px solid #EA1B27;padding-left:.75rem;text-align:left;margin:0;">
                    "<?php echo htmlspecialchars($a['testimonial']); ?>"
                </blockquote>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════ ALL OTHER MILESTONES ══════════════ -->
<?php
$otherOrder = ['half_hifdh','juz20','juz15','juz10','juz5','juz1'];
$hasOther   = false;
foreach ($otherOrder as $mk) { if (!empty($grouped[$mk])) { $hasOther = true; break; } }
if ($hasOther):
?>
<section style="padding:55px 0; background:#fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold" style="color:#0b1f11;">Memorisation Milestones</h2>
            <p class="text-muted">Every Juz' memorised is a step closer to becoming a Ḥāfiẓ</p>
        </div>

        <!-- Progress ladder graphic -->
        <div class="d-flex justify-content-center gap-1 mb-5 flex-wrap">
        <?php
        $ladderSteps = ['juz1'=>1,'juz5'=>5,'juz10'=>10,'juz15'=>15,'juz20'=>20,'half_hifdh'=>'½','full_hifdh'=>'30'];
        foreach ($ladderSteps as $mk => $label):
            $tier    = $milestones[$mk]['tier'];
            $ts      = $tierStyles[$tier];
            $achieved = count($grouped[$mk] ?? []);
        ?>
        <div class="text-center" title="<?php echo $milestones[$mk]['label']; ?> — <?php echo $achieved; ?> pupil(s)">
            <div style="width:44px;height:44px;border-radius:50%;background:<?php echo $ts['bg']; ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:.85rem;margin:0 auto 4px;box-shadow:0 2px 8px rgba(0,0,0,.15);">
                <?php echo $label; ?>
            </div>
            <div style="font-size:.65rem;color:#999;"><?php echo $achieved ?: '–'; ?></div>
        </div>
        <?php endforeach; ?>
        </div>

        <?php foreach ($otherOrder as $mk):
            if (empty($grouped[$mk])) continue;
            $ms  = $milestones[$mk];
            $ts  = $tierStyles[$ms['tier']];
        ?>
        <!-- Milestone group -->
        <div class="mb-5">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:48px;height:48px;border-radius:50%;background:<?php echo $ts['bg']; ?>;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:1rem;flex-shrink:0;">
                    <?php echo $ms['icon']; ?>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold" style="color:#0b1f11;"><?php echo $ms['label']; ?></h4>
                    <div lang="ar" style="font-family:serif;font-size:1.1rem;color:#666;direction:rtl;display:inline;"><?php echo $ms['arabic']; ?></div>
                </div>
                <span class="badge ms-auto" style="background:<?php echo $ts['badge_bg']; ?>; font-size:.8rem;"><?php echo count($grouped[$mk]); ?> pupil<?php echo count($grouped[$mk]) > 1 ? 's' : ''; ?></span>
            </div>

            <div class="row g-3">
            <?php foreach ($grouped[$mk] as $a): ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
                <div class="rounded-3 p-3 h-100 shadow-sm text-center"
                     style="background:#fafafa;border-left:4px solid <?php echo $ts['border']; ?>;">
                    <?php if (!empty($a['photo']) && file_exists('uploads/' . $a['photo'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($a['photo']); ?>"
                         alt="<?php echo htmlspecialchars($a['pupil_name']); ?>"
                         style="width:60px;height:60px;border-radius:50%;object-fit:cover;border:2px solid <?php echo $ts['border']; ?>;margin-bottom:.5rem;">
                    <?php else: ?>
                    <div style="width:54px;height:54px;border-radius:50%;background:<?php echo $ts['bg']; ?>;display:flex;align-items:center;justify-content:center;margin:0 auto .5rem;font-size:1.2rem;">📗</div>
                    <?php endif; ?>
                    <div class="fw-bold" style="color:#212529;"><?php echo htmlspecialchars($a['pupil_name']); ?></div>
                    <?php if ($a['class']): ?><div class="badge mb-1" style="background:<?php echo $ts['badge_bg']; ?>; font-size:.7rem;"><?php echo htmlspecialchars($a['class']); ?></div><?php endif; ?>
                    <div style="font-size:.78rem;color:#888;"><i class="fas fa-calendar me-1"></i><?php echo date('M Y', strtotime($a['date_achieved'])); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- ══════════════ HIFDH PROGRAMME INFO ══════════════ -->
<section style="padding:55px 0; background:linear-gradient(135deg,#0b1f11,#0d2e1a); color:#fff;">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-3" style="color:#EA1B27;">Our Ḥifẓ Programme</h2>
                <p style="opacity:.85; line-height:1.8; margin-bottom:1.25rem;">
                    At Lukman Primary School, Qur'an memorisation is woven into the daily timetable from Primary One. Pupils benefit from dedicated one-on-one Tajweed sessions with qualified Qur'an teachers, ensuring correct pronunciation and deep spiritual connection alongside their secular studies.
                </p>
                <ul style="opacity:.8; line-height:2.2; padding-left:1.2rem; list-style:none;">
                    <li><i class="fas fa-check-circle me-2" style="color:#EA1B27;"></i>Daily 45-minute Qur'an recitation period</li>
                    <li><i class="fas fa-check-circle me-2" style="color:#EA1B27;"></i>Individual Tajweed correction with qualified teachers</li>
                    <li><i class="fas fa-check-circle me-2" style="color:#EA1B27;"></i>Term-end Qur'an revision tests and milestone certificates</li>
                    <li><i class="fas fa-check-circle me-2" style="color:#EA1B27;"></i>Public recognition at end-of-year prize giving</li>
                    <li><i class="fas fa-check-circle me-2" style="color:#EA1B27;"></i>Parents notified at every Juz' milestone achieved</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <!-- Progress bar for Juz' levels -->
                <div class="rounded-3 p-4" style="background:rgba(255,255,255,.07);">
                    <h5 class="fw-bold mb-3" style="color:#EA1B27;">Your child's journey to full Ḥifẓ</h5>
                    <?php
                    $levels = [
                        ['juz1', 'Juz\' 1 (Getting started)', 3],
                        ['juz5', '5 Juz\' (Building momentum)', 17],
                        ['juz10','10 Juz\' (One-third done)',  33],
                        ['juz15','15 Juz\' (Halfway milestone)',50],
                        ['juz20','20 Juz\' (Two-thirds done)', 67],
                        ['half_hifdh','Half Ḥifẓ (50% complete)', 50],
                        ['full_hifdh','Full Ḥifẓ 🏆',           100],
                    ];
                    foreach ($levels as [$key, $label, $pct]):
                        $tier  = $milestones[$key]['tier'];
                        $count = count($grouped[$key] ?? []);
                        $col   = $tierStyles[$tier]['border'];
                    ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span style="font-size:.82rem;"><?php echo $label; ?></span>
                            <span style="font-size:.82rem; color:#EA1B27;"><?php echo $count > 0 ? $count . ' pupil' . ($count>1?'s':'') : ''; ?></span>
                        </div>
                        <div style="height:8px;background:rgba(255,255,255,.12);border-radius:4px;overflow:hidden;">
                            <div style="width:<?php echo $pct; ?>%;height:100%;background:<?php echo $col; ?>;border-radius:4px;"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CTA ══════════════ -->
<section id="cta" class="section-pad-sm">
    <div class="container text-center">
        <h2 class="section-heading" style="color:var(--white);">Give Your Child the Gift of the Qur'an</h2>
        <p style="color:rgba(255,255,255,.9); margin-bottom:2rem;">Enrol at Lukman Primary School and set your child on the Ḥifẓ journey from Primary One.</p>
        <a href="admissions.php" class="btn-green-custom" style="padding:.8rem 2rem;"><i class="fas fa-graduation-cap me-2"></i>Apply for Admission</a>
        <a href="prayer-times.php" style="display:inline-block;background:transparent;color:var(--white);border:2px solid var(--white);padding:.8rem 2rem;font-weight:600;border-radius:6px;text-decoration:none;margin-left:.5rem;"><i class="fas fa-clock me-2"></i>Prayer Timetable</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

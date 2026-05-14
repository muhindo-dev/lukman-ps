<?php
$currentPage = 'team';
$pageTitle   = 'Our Team';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');

$pageDescription = 'Meet the dedicated leadership, teaching staff, and support team of ' . $siteName . ' — educators committed to nurturing excellence.';
include 'includes/header.php';

// Fetch all active team members
$stmt = $pdo->query("SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order ASC, name ASC");
$allMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by department
$departments = [];
foreach ($allMembers as $member) {
    $dept = $member['department'] ?? 'teaching';
    $departments[$dept][] = $member;
}

// Department display config
$deptMeta = [
    'management'      => ['label' => 'School Management',  'icon' => 'fas fa-landmark'],
    'teaching'        => ['label' => 'Teaching Staff',      'icon' => 'fas fa-chalkboard-teacher'],
    'support'         => ['label' => 'Support Staff',       'icon' => 'fas fa-hands-helping'],
    'student_leaders' => ['label' => 'Student Leaders',     'icon' => 'fas fa-star'],
];
$deptOrder = ['management', 'teaching', 'support', 'student_leaders'];
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Our Team</h1>
        <p>Meet the dedicated people behind every child's success at <?php echo htmlspecialchars($siteName); ?></p>
    </div>
</div>

<!-- Team Listing -->
<section class="section-pad">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <span class="badge-section">OUR LEADERSHIP</span>
            <h2>The People Who Make It Happen</h2>
            <p class="subtitle">Dedicated educators and mentors committed to nurturing young minds with excellence and faith.</p>
        </div>

        <!-- Department Filter -->
        <div class="gal-toolbar" style="justify-content:center; margin-bottom:2rem;" data-aos="fade-up">
            <div class="gal-filters">
                <button class="btn gal-filter active" data-dept="all">All Staff</button>
                <?php foreach ($deptOrder as $dKey):
                    if (!isset($departments[$dKey])) continue;
                ?>
                <button class="btn gal-filter" data-dept="<?php echo $dKey; ?>"><?php echo $deptMeta[$dKey]['label']; ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Department Sections -->
        <?php foreach ($deptOrder as $dKey):
            if (!isset($departments[$dKey])) continue;
            $meta    = $deptMeta[$dKey];
            $members = $departments[$dKey];
        ?>
        <div class="tp-dept-section" data-department="<?php echo $dKey; ?>">
            <div class="tp-dept-label" data-aos="fade-up">
                <i class="<?php echo $meta['icon']; ?>"></i> <?php echo $meta['label']; ?>
            </div>
            <div class="row g-4">
                <?php foreach ($members as $idx => $member):
                    $memberPhoto = trim((string)($member['photo'] ?? ''));
                    $memberPhotoUrl = '';
                    if ($memberPhoto !== '') {
                        $memberPhotoUrl = (strpos($memberPhoto, '/') === false)
                            ? 'uploads/team/' . $memberPhoto
                            : 'uploads/' . ltrim($memberPhoto, '/');
                    }
                ?>
                <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo $idx * 60; ?>">
                    <article class="team-home-card">
                        <div class="team-home-photo-wrap">
                            <div class="team-home-photo">
                                <?php if ($memberPhotoUrl !== ''): ?>
                                <img src="<?php echo htmlspecialchars($memberPhotoUrl); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>" loading="lazy">
                                <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                                <?php endif; ?>
                            </div>
                            <span class="team-home-badge"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $member['department'] ?? 'teaching'))); ?></span>
                        </div>
                        <div class="team-home-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <p><?php echo htmlspecialchars($member['position']); ?></p>
                            <?php if (!empty($member['qualification'])): ?>
                            <div class="team-home-qual"><i class="fas fa-certificate"></i> <?php echo htmlspecialchars($member['qualification']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($member['bio'])): ?>
                            <p style="margin:0.5rem 0 0; font-size:0.82rem; color:var(--gray-text); line-height:1.6; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;"><?php echo htmlspecialchars($member['bio']); ?></p>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (empty($allMembers)): ?>
        <div class="text-center" style="padding:3rem 0;">
            <i class="fas fa-users" style="font-size:3rem; color:var(--gray-border); margin-bottom:1rem; display:block;"></i>
            <h3 style="color:var(--gray-text);">Team information coming soon</h3>
            <p style="color:var(--gray-text);">We are updating our team profiles. Please check back later.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA -->
<section id="cta" class="section-pad-sm">
    <div class="container text-center">
        <h2 class="section-heading" style="color:var(--white);">Want to Be Part of Our Team?</h2>
        <p style="color:rgba(255,255,255,0.9);font-size:1.1rem;margin-bottom:2rem;max-width:550px;margin-left:auto;margin-right:auto;">We're always looking for passionate educators and professionals to join the <?php echo htmlspecialchars($siteShortName); ?> family.</p>
        <a href="contact.php" class="btn-outline-custom" style="border-color:#fff;color:#fff;">
            <i class="fas fa-paper-plane me-2"></i>Get in Touch
        </a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btns = document.querySelectorAll('[data-dept]');
    var sections = document.querySelectorAll('.tp-dept-section');
    var filterBtns = document.querySelectorAll('.gal-filters .gal-filter');

    filterBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterBtns.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            var dept = btn.getAttribute('data-dept');
            sections.forEach(function(sec) {
                sec.style.display = (dept === 'all' || sec.getAttribute('data-department') === dept) ? '' : 'none';
            });
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>

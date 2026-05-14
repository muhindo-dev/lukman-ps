<?php
$currentPage   = 'about';
$pageTitle     = 'About Us';
include 'config.php';
include 'functions.php';

$siteName         = getSetting('site_name', 'Lukman Primary School');
$siteShortName    = getSetting('site_short_name', 'Lukman PS');
$missionStatement = getSetting('mission_statement', 'To provide quality, affordable education rooted in both the Uganda national curriculum and Islamic values, nurturing every child to reach their full academic and moral potential.');
$visionStatement  = getSetting('vision_statement', 'To be the leading primary school in Uganda, recognised for academic excellence, character development, and inclusive education for every child.');
$foundingYear     = getSetting('founding_year', '1997');

$pageDescription = 'Learn about ' . $siteName . ' — our history, mission, vision, leadership team, and commitment to quality education in Entebbe, Uganda.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'About Us']]);

$stmt        = $pdo->query("SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order ASC");
$teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalStudents  = getSetting('total_students', '500');
$totalTeachers  = getSetting('total_teachers', '30');
$yearsOfService = date('Y') - (int)$foundingYear;
?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>About <?php echo htmlspecialchars($siteName); ?></h1>
            <p>Nurturing excellence in education since <?php echo htmlspecialchars($foundingYear); ?></p>
        </div>
    </div>

    <!-- About Overview -->
    <section class="section-pad">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="badge-section" style="display:inline-block;margin-bottom:1rem;">OUR STORY</span>
                    <h2 class="section-heading" style="margin-bottom:1.5rem;">Lukman Primary School</h2>
                    <p style="color:var(--gray-text);line-height:1.8;font-size:1.05rem;margin-bottom:1.5rem;">
                        Lukman Primary School was established in <?php echo htmlspecialchars($foundingYear); ?> in Entebbe, Uganda, with a founding vision of offering every child an opportunity to grow — academically, morally, and socially. We believe that education is the greatest gift a community can give its children.
                    </p>
                    <p style="color:var(--gray-text);line-height:1.8;font-size:1.05rem;margin-bottom:1.5rem;">
                        We offer a dual curriculum that integrates the Uganda National Curriculum with Islamic studies, Quran recitation, and Arabic language. This prepares our pupils not just for Uganda's Primary Leaving Examinations (PLE), but for a life of purpose, integrity, and service.
                    </p>
                    <p style="color:var(--gray-text);line-height:1.8;font-size:1.05rem;">
                        With experienced teachers, modern learning facilities, and a caring boarding environment, Lukman Primary School is continuously recognised as one of the leading primary schools in the Entebbe area.
                    </p>
                </div>
                <div class="col-lg-6">
                    <div class="content-block" style="text-align:center;">
                        <i class="fas fa-school" style="font-size:3rem;color:var(--primary);margin-bottom:1.5rem;display:block;"></i>
                        <div class="row g-3">
                            <div class="col-6">
                                <div style="background:var(--white);border-radius:12px;padding:1.5rem;">
                                    <div class="stat-value"><?php echo htmlspecialchars($yearsOfService); ?>+</div>
                                    <div style="font-size:0.85rem;color:var(--gray-text);font-weight:600;">Years of Service</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:var(--white);border-radius:12px;padding:1.5rem;">
                                    <div class="stat-value"><?php echo htmlspecialchars($totalStudents); ?>+</div>
                                    <div style="font-size:0.85rem;color:var(--gray-text);font-weight:600;">Enrolled Pupils</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:var(--white);border-radius:12px;padding:1.5rem;">
                                    <div class="stat-value"><?php echo htmlspecialchars($totalTeachers); ?>+</div>
                                    <div style="font-size:0.85rem;color:var(--gray-text);font-weight:600;">Qualified Teachers</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="background:var(--white);border-radius:12px;padding:1.5rem;">
                                    <div class="stat-value">2</div>
                                    <div style="font-size:0.85rem;color:var(--gray-text);font-weight:600;">Curricula Offered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="section-pad" style="background:var(--gray-light);">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">OUR PURPOSE</span>
                <h2>Mission &amp; Vision</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="mission-card">
                        <div class="mission-icon"><i class="fas fa-bullseye"></i></div>
                        <h3>Our Mission</h3>
                        <p><?php echo htmlspecialchars($missionStatement); ?></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="mission-card">
                        <div class="mission-icon"><i class="fas fa-eye"></i></div>
                        <h3>Our Vision</h3>
                        <p><?php echo htmlspecialchars($visionStatement); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="section-pad">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">WHAT WE STAND FOR</span>
                <h2>Our Core Values</h2>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-book-open"></i></div>
                        <h3>Academic Excellence</h3>
                        <p>We hold every pupil to the highest academic standard, consistently producing top PLE results in the Entebbe region.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-mosque"></i></div>
                        <h3>Islamic Values</h3>
                        <p>Our integrated Islamic curriculum nurtures faith, discipline, and character alongside academic learning.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-users"></i></div>
                        <h3>Inclusivity</h3>
                        <p>We welcome children from all backgrounds, creating a diverse and supportive learning community.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-shield-alt"></i></div>
                        <h3>Safety &amp; Care</h3>
                        <p>Our boarding programme provides a safe, nurturing home-away-from-home for every resident pupil.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-lightbulb"></i></div>
                        <h3>Innovation</h3>
                        <p>We invest in modern teaching aids, e-learning tools, and technology to keep our pupils prepared for the future.</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service-card text-center">
                        <div class="service-icon mx-auto"><i class="fas fa-handshake"></i></div>
                        <h3>Partnership</h3>
                        <p>We actively involve parents, the local community, and development partners in shaping the school's growth.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What We Offer -->
    <section class="section-pad" style="background:var(--gray-light);">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">AT A GLANCE</span>
                <h2>What We Offer</h2>
                <p class="subtitle">A well-rounded learning experience from Baby Class to Primary 7</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div style="background:var(--white);border-radius:12px;padding:2rem;">
                        <h4 style="color:var(--primary);font-weight:700;margin-bottom:1rem;"><i class="fas fa-graduation-cap me-2"></i>Uganda National Curriculum (UNC)</h4>
                        <ul style="list-style:none;padding:0;color:var(--gray-text);line-height:2;">
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>English, Mathematics, Science, SST</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Local Language (Luganda)</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Religious Education (Islam &amp; CRE)</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Physical Education &amp; Music</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>ICT / Computer Studies (P4&ndash;P7)</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background:var(--white);border-radius:12px;padding:2rem;">
                        <h4 style="color:var(--secondary);font-weight:700;margin-bottom:1rem;"><i class="fas fa-book me-2"></i>Islamic Studies Curriculum</h4>
                        <ul style="list-style:none;padding:0;color:var(--gray-text);line-height:2;">
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Quran Recitation (Tajweed)</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Arabic Language (Beginner&ndash;Intermediate)</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Islamic Studies &amp; Fiqh</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Seerah (Prophet's Biography)</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Adab &amp; Character Building</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background:var(--white);border-radius:12px;padding:2rem;">
                        <h4 style="color:var(--primary);font-weight:700;margin-bottom:1rem;"><i class="fas fa-bed me-2"></i>Boarding Facilities</h4>
                        <ul style="list-style:none;padding:0;color:var(--gray-text);line-height:2;">
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Separate boys and girls dormitories</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Supervised study evenings</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Three balanced meals daily</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>24-hour security and welfare staff</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--primary);"></i>Mosque on campus for daily prayers</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background:var(--white);border-radius:12px;padding:2rem;">
                        <h4 style="color:var(--secondary);font-weight:700;margin-bottom:1rem;"><i class="fas fa-running me-2"></i>Co-Curricular Activities</h4>
                        <ul style="list-style:none;padding:0;color:var(--gray-text);line-height:2;">
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Football, Netball, Athletics</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Drama, Debate &amp; Public Speaking</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Art, Craft &amp; Music Clubs</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Environmental &amp; Community Service</li>
                            <li><i class="fas fa-check-circle me-2" style="color:var(--secondary);"></i>Annual Inter-House Sports Day</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team -->
    <?php if (!empty($teamMembers)): ?>
    <section id="team" class="section-pad">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">OUR LEADERSHIP</span>
                <h2>Meet Our Team</h2>
                <p class="subtitle">Dedicated educators and administrators committed to every child's success</p>
            </div>
            <div class="row g-4 justify-content-center">
                <?php foreach ($teamMembers as $member): ?>
                <div class="col-lg-3 col-md-6">
                    <div class="team-card">
                        <div class="team-photo">
                            <?php
                            $memberPhoto = trim((string)($member['photo'] ?? ''));
                            $memberPhotoUrl = '';
                            if ($memberPhoto !== '') {
                                $memberPhotoUrl = (strpos($memberPhoto, '/') === false)
                                    ? 'uploads/team/' . $memberPhoto
                                    : 'uploads/' . ltrim($memberPhoto, '/');
                            }
                            ?>
                            <?php if ($memberPhotoUrl !== ''): ?>
                            <img src="<?php echo htmlspecialchars($memberPhotoUrl); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                            <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                        </div>
                        <div class="team-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <p><?php echo htmlspecialchars($member['position']); ?></p>
                            <?php if (!empty($member['bio'])): ?>
                            <p style="color:var(--gray-text);font-size:0.85rem;margin-top:0.5rem;font-weight:400;line-height:1.6;">
                                <?php echo htmlspecialchars(substr($member['bio'], 0, 150)); ?>...
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section id="cta" class="section-pad-sm">
        <div class="container text-center">
            <h2 class="section-heading" style="color:var(--white);">Ready to Enrol Your Child?</h2>
            <p style="color:rgba(255,255,255,0.9);font-size:1.1rem;margin-bottom:2rem;max-width:600px;margin-left:auto;margin-right:auto;">Join the Lukman Primary School family and give your child the gift of quality, values-centred education.</p>
            <div class="cta-inline-btns" style="justify-content:center;">
                <a href="admissions.php" class="btn-green-custom" style="padding:0.8rem 2rem;"><i class="fas fa-user-plus me-2"></i>Apply Now</a>
                <a href="contact.php" style="display:inline-block;background:transparent;color:var(--white);border:2px solid var(--white);padding:0.8rem 2rem;font-weight:600;border-radius:6px;text-decoration:none;"><i class="fas fa-envelope me-2"></i>Contact Us</a>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
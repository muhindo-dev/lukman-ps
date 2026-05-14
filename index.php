<?php
$currentPage = 'home';
$pageTitle = 'Home';
include 'config.php';
include 'functions.php';

// Get settings
$siteName = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'LPS');
$siteTagline = getSetting('site_tagline', 'Seek Knowledge and Attain Wisdom');
$siteDescription = getSetting('site_description', '');
$missionStatement = getSetting('mission_statement', '');
$visionStatement = getSetting('vision_statement', '');
$foundingYear   = (int) getSetting('founding_year', '1997');
$yearsOfService = date('Y') - $foundingYear;
$totalStudents  = getSetting('total_students', '1000');
$totalTeachers  = getSetting('total_teachers', '60');

$pageDescription = $siteDescription;
include 'includes/header.php';

// Fetch latest news
$stmt = $pdo->query("SELECT * FROM news_posts WHERE status = 'published' ORDER BY published_at DESC LIMIT 3");
$latestNews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch upcoming events
$stmt = $pdo->query("SELECT * FROM events WHERE status = 'upcoming' ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch team members
$stmt = $pdo->query("SELECT * FROM team_members WHERE status = 'active' ORDER BY display_order ASC LIMIT 4");
$teamMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch testimonials
$stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'active' ORDER BY display_order ASC LIMIT 3");
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main id="main-content">

    <!-- ═══════════════════════════════════════════
         HERO SECTION v2 — Split Layout with Real School Images
         ═══════════════════════════════════════════ -->
    <section id="hero">

        <!-- Decorative geometric layering -->
        <div class="hero-bg-layer"></div>
        <div class="hero-geo-ring hero-ring-1"></div>
        <div class="hero-geo-ring hero-ring-2"></div>

        <div class="hero-split-wrap">

            <!-- ── LEFT: Content Panel ── -->
            <div class="hero-left" data-aos="fade-right" data-aos-duration="900">

                <!-- School Logo -->
                <div class="hero-logo-box" data-aos="fade-down" data-aos-delay="100">
                    <img src="assets/images/lukman-transparent-bordered-logo.png"
                         alt="Lukman Primary School Crest"
                         class="hero-school-logo">
                </div>

                <!-- Location / accreditation chip -->
                <div class="hero-chip" data-aos="fade-up" data-aos-delay="150">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($siteShortName); ?> &mdash; Entebbe, Uganda &middot; Est. <?php echo $foundingYear; ?></span>
                </div>

                <!-- Headline -->
                <h1 class="hero-headline" data-aos="fade-up" data-aos-delay="200">
                    <?php echo htmlspecialchars($siteTagline); ?>
                    <span class="hero-headline-accent">with Purpose &amp; Faith</span>
                </h1>

                <!-- Lead copy -->
                <p class="hero-desc" data-aos="fade-up" data-aos-delay="280">
                    Located along the northern shores of Lake Victoria in Entebbe, we offer a dual
                    secular &amp; Islamic theology curriculum — equipping children with knowledge,
                    character and a love for learning.
                </p>

                <!-- CTA Buttons -->
                <div class="hero-cta-row" data-aos="fade-up" data-aos-delay="360">
                    <a href="admissions.php" class="hbtn hbtn-primary">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Apply for Admission</span>
                    </a>
                    <a href="about.php" class="hbtn hbtn-ghost">
                        <i class="fas fa-arrow-right"></i>
                        <span>Discover More</span>
                    </a>
                </div>

                <!-- Trust row -->
                <div class="hero-trust-row" data-aos="fade-up" data-aos-delay="440">
                    <div class="hero-trust-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Government Accredited</span>
                    </div>
                    <span class="trust-dot"></span>
                    <div class="hero-trust-item">
                        <i class="fas fa-trophy"></i>
                        <span>Top PLE Results</span>
                    </div>
                    <span class="trust-dot"></span>
                    <div class="hero-trust-item">
                        <i class="fas fa-mosque"></i>
                        <span>Islamic Values</span>
                    </div>
                </div>
            </div>

            <!-- ── RIGHT: Image Mosaic Panel ── -->
            <div class="hero-right" data-aos="fade-left" data-aos-duration="900" data-aos-delay="150">
                <div class="hero-mosaic">

                    <!-- Main large image -->
                    <div class="mosaic-main">
                        <img src="assets/images/students-in-class-min.jpg"
                             alt="Lukman PS students learning in class">
                        <div class="mosaic-main-badge">
                            <i class="fas fa-star"></i>
                            <span>Academic Excellence</span>
                        </div>
                    </div>

                    <!-- Small images stack -->
                    <div class="mosaic-side">
                        <div class="mosaic-cell mosaic-cell-a">
                            <img src="assets/images/lukman-ps-masjid.jpeg"
                                 alt="Lukman PS Mosque — Islamic education">
                            <div class="mosaic-cell-label"><i class="fas fa-mosque"></i> School Mosque</div>
                        </div>
                        <div class="mosaic-cell mosaic-cell-b">
                            <img src="assets/images/AY1A0802-min.jpg"
                                 alt="Sports and co-curricular at Lukman PS">
                            <div class="mosaic-cell-label"><i class="fas fa-running"></i> Sports &amp; Activities</div>
                        </div>
                        <div class="mosaic-cell mosaic-cell-c">
                            <img src="assets/images/AY1A0775-min.jpg"
                                 alt="Student leaders at Lukman PS">
                            <div class="mosaic-cell-label"><i class="fas fa-users"></i> Student Leaders</div>
                        </div>
                    </div>
                </div>

                <!-- Floating stat cards -->
                <div class="hero-float-card hfc-left" data-aos="zoom-in" data-aos-delay="600">
                    <div class="hfc-icon"><i class="fas fa-user-graduate"></i></div>
                    <div class="hfc-text">
                        <strong><?php echo number_format((int)$totalStudents); ?>+</strong>
                        <span>Students</span>
                    </div>
                </div>
                <div class="hero-float-card hfc-right" data-aos="zoom-in" data-aos-delay="700">
                    <div class="hfc-icon"><i class="fas fa-award"></i></div>
                    <div class="hfc-text">
                        <strong><?php echo $yearsOfService; ?>+ Yrs</strong>
                        <span>of Excellence</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="hero-stats-bar" data-aos="fade-up" data-aos-delay="500">
            <div class="hsb-item">
                <span class="hsb-num"><?php echo number_format((int)$totalStudents); ?>+</span>
                <span class="hsb-lbl">Students Enrolled</span>
            </div>
            <div class="hsb-sep"></div>
            <div class="hsb-item">
                <span class="hsb-num"><?php echo (int)$totalTeachers; ?>+</span>
                <span class="hsb-lbl">Qualified Teachers</span>
            </div>
            <div class="hsb-sep"></div>
            <div class="hsb-item">
                <span class="hsb-num"><?php echo $yearsOfService; ?>+</span>
                <span class="hsb-lbl">Years of Excellence</span>
            </div>
            <div class="hsb-sep"></div>
            <div class="hsb-item">
                <span class="hsb-num">2</span>
                <span class="hsb-lbl">Curriculum Streams</span>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         MISSION & VISION
         ══════════════════════════════════════════ -->
    <section id="mission">
        <div class="container">
            <div class="row align-items-center g-4">
                <!-- Left: Label + heading -->
                <div class="col-lg-5" data-aos="fade-right">
                    <span class="mv-label">WHO WE ARE</span>
                    <h2 class="mv-heading">Shaping Minds,<br>Building <em>Character</em></h2>
                    <p class="mv-lead">Since <?php echo $foundingYear; ?>, Lukman Primary School has blended academic excellence with Islamic values — nurturing confident learners who serve their communities.</p>
                </div>
                <!-- Right: Two cards stacked -->
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="80">
                    <div class="mv-card mv-card--mission">
                        <div class="mv-card-icon"><i class="fas fa-bullseye"></i></div>
                        <div class="mv-card-body">
                            <h3>Our Mission</h3>
                            <p><?php echo htmlspecialchars($missionStatement ?: 'To produce an integrated, effective and a balanced child who is academically and religiously sound.'); ?></p>
                        </div>
                    </div>
                    <div class="mv-card mv-card--vision">
                        <div class="mv-card-icon"><i class="fas fa-eye"></i></div>
                        <div class="mv-card-body">
                            <h3>Our Vision</h3>
                            <p><?php echo htmlspecialchars($visionStatement ?: 'A place where any child can be transformed into a productive citizen anywhere in the world.'); ?></p>
                        </div>
                    </div>
                    <div class="mv-card mv-card--motto">
                        <div class="mv-card-icon"><i class="fas fa-pen-fancy"></i></div>
                        <div class="mv-card-body">
                            <h3>School Motto</h3>
                            <p>Seek knowledge and attain wisdom.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         WHY CHOOSE US — Bento Grid Layout
         ══════════════════════════════════════════ -->
    <section id="why-us" class="wcu-section">

        <div class="container">

            <!-- Split header — left title, right desc -->
            <div class="wcu-split-header" data-aos="fade-up">
                <div class="wcu-split-left">
                    <span class="wcu-label-pill">Why Choose Us</span>
                    <h2 class="wcu-big-title">We Don't Just<br>Teach. We <em>Form</em>.</h2>
                </div>
                <div class="wcu-split-right">
                    <p>At Lukman Primary School we combine academic rigour, Islamic values, and a vibrant campus life — so every child graduates not only educated, but equipped for life.</p>
                    <a href="admissions.php" class="wcu-header-cta">
                        <span>Start Your Application</span>
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- ─── BENTO GRID ─── -->
            <div class="bento-grid" data-aos="fade-up" data-aos-delay="80">

                <!-- TILE A — Photo hero tile (2 cols × 2 rows) -->
                <div class="btile btile--photo btile--A">
                    <img src="assets/images/AY1A1203-min-1.jpg" alt="Students in class at Lukman PS" class="btile-bg-img">
                    <div class="btile-overlay"></div>
                    <div class="btile-content">
                        <div class="btile-super-stat">
                            <span class="bss-num"><?php echo number_format((int)$totalStudents); ?><sup>+</sup></span>
                            <span class="bss-label">Students</span>
                        </div>
                        <h3 class="btile-headline">Academic Excellence<br>at Its Core</h3>
                        <p class="btile-sub">Top national exam scores, critical thinking and a love for learning — every single year.</p>
                    </div>
                    <a href="academics.php" class="btile-corner-link">
                        Academics <i class="fas fa-arrow-up-right-from-square"></i>
                    </a>
                </div>

                <!-- TILE B — PLE stat (green solid) -->
                <div class="btile btile--B btile--green">
                    <div class="btile-content btile-content--center">
                        <span class="btile-overline">PLE Results</span>
                        <div class="btile-giant-text">
                            <span class="giant-pre">Div.</span>
                            <span class="giant-num">1</span>
                        </div>
                        <p class="btile-caption">Consistent Division 1 performance in Primary Leaving Examinations — year after year.</p>
                    </div>
                    <a href="results.php" class="btile-corner-link btile-corner-link--light">
                        Results <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- TILE C — Mosque photo -->
                <div class="btile btile--photo btile--C">
                    <img src="assets/images/lukman-mosque.jpg" alt="Lukman PS Mosque" class="btile-bg-img">
                    <div class="btile-overlay btile-overlay--dark"></div>
                    <div class="btile-content btile-content--bottom">
                        <span class="btile-icon-badge"><i class="fas fa-mosque"></i></span>
                        <h3 class="btile-headline btile-headline--sm">On-Campus<br>Masjid</h3>
                        <p class="btile-sub">Daily prayers, Quran studies &amp; qualified sheikhs — faith woven into every school day.</p>
                    </div>
                </div>

                <!-- TILE D — 24/7 Boarding (dark tile) -->
                <div class="btile btile--D btile--dark">
                    <div class="btile-content btile-content--center">
                        <div class="btile-giant-text btile-giant-text--white">
                            <span class="giant-num">24<span class="giant-slash">/</span>7</span>
                        </div>
                        <span class="btile-overline btile-overline--light">Safe Boarding</span>
                        <p class="btile-caption btile-caption--light">Supervised dorms, nutritious meals &amp; round-the-clock pastoral care.</p>
                    </div>
                    <a href="about.php#facilities" class="btile-corner-link btile-corner-link--light">
                        Facilities <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- TILE E — Dual curriculum (split visual) -->
                <div class="btile btile--E btile--split">
                    <div class="btile-split-left">
                        <i class="fas fa-book-open btile-split-icon"></i>
                        <span>National<br>Curriculum</span>
                    </div>
                    <div class="btile-split-divider">
                        <span class="split-plus">+</span>
                    </div>
                    <div class="btile-split-right">
                        <i class="fas fa-star-and-crescent btile-split-icon"></i>
                        <span>Islamic<br>Theology</span>
                    </div>
                    <div class="btile-split-footer">
                        <strong>Dual Curriculum</strong>
                        <a href="academics.php" class="btile-split-link">Explore <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- TILE F — Teachers stat (accent red) -->
                <div class="btile btile--F btile--red">
                    <div class="btile-content btile-content--center">
                        <span class="btile-overline btile-overline--light">Our Educators</span>
                        <div class="btile-giant-text btile-giant-text--white">
                            <span class="giant-num"><?php echo (int)$totalTeachers; ?><sup>+</sup></span>
                        </div>
                        <p class="btile-caption btile-caption--light">Government-certified, passionate teachers committed to every child's growth.</p>
                    </div>
                    <a href="team.php" class="btile-corner-link btile-corner-link--light">
                        Our Team <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- TILE G — Sports photo (2 cols × 1 row) -->
                <div class="btile btile--photo btile--G">
                    <img src="assets/images/AY1A0384-min.jpg" alt="Sports and activities at Lukman PS" class="btile-bg-img btile-bg-img--top">
                    <div class="btile-overlay btile-overlay--left"></div>
                    <div class="btile-content btile-content--left">
                        <h3 class="btile-headline">Beyond the<br>Classroom</h3>
                        <div class="btile-tags">
                            <span class="btile-tag"><i class="fas fa-running"></i> Sports</span>
                            <span class="btile-tag"><i class="fas fa-music"></i> Music</span>
                            <span class="btile-tag"><i class="fas fa-comments"></i> Debate</span>
                            <span class="btile-tag"><i class="fas fa-quran"></i> Quran</span>
                        </div>
                    </div>
                    <a href="student-life.php" class="btile-corner-link">
                        Student Life <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <!-- TILE H — Est. year (light green) -->
                <div class="btile btile--H btile--light-green">
                    <div class="btile-content btile-content--center">
                        <div class="btile-giant-text">
                            <span class="giant-pre giant-pre--dark">Est.</span>
                            <span class="giant-num giant-num--dark"><?php echo $foundingYear; ?></span>
                        </div>
                        <span class="btile-overline">Years of Excellence</span>
                        <p class="btile-caption"><?php echo $yearsOfService; ?>+ years shaping the leaders of tomorrow.</p>
                    </div>
                </div>

            </div><!-- /bento-grid -->
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         HEAD TEACHER'S WELCOME — Inspired by WP
         ══════════════════════════════════════════ -->
    <section id="welcome" class="ht-welcome-section">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="ht-photo-frame">
                        <img src="assets/images/hm.jpg" alt="Mr. Lubega Ibrahim – Head Teacher, Lukman Primary School">
                    </div>
                </div>
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="80">
                    <span class="ht-label">Head Teacher's Welcome</span>
                    <blockquote class="ht-quote">
                        "All praise is to the Almighty Allah by whose favour we continue to work. It is with great pleasure that I welcome you. Our website gives you a glimpse of life at our school."
                    </blockquote>
                    <p class="ht-desc">Located along the northern shores of Lake Victoria, our school provides a breathtaking view, a quiet, serene and spacious environment conducive for learning. Our dedicated staff has the qualification to reach and maintain academic expectations we set ourselves.</p>
                    <p class="ht-desc">Apart from the rich spiritual setting as a result of both secular and religious curriculum, we engage in co-curricular activities such as football, netball, volleyball, and bicycle riding. We have clubs like the English language club, debating club, science club, and Lukman Primary School Dawa Association club (LUPDA).</p>
                    <div class="ht-signature">
                        <strong>Mr. Lubega Ibrahim</strong>
                        <span>Head Teacher</span>
                    </div>
                    <a href="about.php" class="ht-read-more">Read more about our school <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         ABOUT SCHOOL / WHY LUKMAN — Rich Story
         ══════════════════════════════════════════ -->
    <section id="about-story" class="about-story-section">
        <div class="as-accent-strip"></div>
        <div class="container">
            <!-- Section header row -->
            <div class="as-section-header" data-aos="fade-up">
                <span class="as-label">Our Story</span>
                <div class="as-year-badge"><span class="as-year-num"><?php echo $foundingYear; ?></span><span class="as-year-text">Year Founded</span></div>
            </div>

            <div class="row g-5">
                <!-- Image Collage -->
                <div class="col-lg-5" data-aos="fade-right">
                    <div class="as-collage">
                        <div class="as-collage-main">
                            <img src="assets/images/Buildings-12-scaled.jpg" alt="Lukman Primary School campus" loading="lazy">
                        </div>
                        <div class="as-collage-top">
                            <img src="assets/images/AY1A0942-min.jpg" alt="Students learning" loading="lazy">
                        </div>
                        <div class="as-collage-bottom">
                            <img src="assets/images/lukman-mosque.jpg" alt="Lukman School Mosque" loading="lazy">
                        </div>
                        <div class="as-collage-frame"></div>
                    </div>
                </div>

                <!-- Text Content -->
                <div class="col-lg-7" data-aos="fade-left" data-aos-delay="80">
                    <h2 class="as-heading">Lukman Primary School is one of the <em>accredited primary schools</em> with a dual curriculum located in Entebbe&nbsp;&mdash;&nbsp;Wakiso,&nbsp;Uganda.</h2>

                    <div class="as-chips">
                        <span class="as-chip"><i class="fas fa-book-open"></i> Dual Curriculum</span>
                        <span class="as-chip"><i class="fas fa-mosque"></i> UMWA Project</span>
                        <span class="as-chip"><i class="fas fa-map-marker-alt"></i> Entebbe, Uganda</span>
                    </div>

                    <p class="as-body">We have clearly initiated a complete schooling programme, unique, that is meant to provide a holistic and overall development of our children, while embracing the spiritual (theology) and secular education provided for their nurturing for this world and in the hereafter, as reflected in our school mission.</p>
                    <p class="as-body">Lukman Primary School is one of the projects of <strong>Uganda Muslim Welfare Association</strong> (UMWA), a Muslim Non-government organization (NGO) registered in Uganda, to do Islamic charitable work. It was founded and registered in the year <?php echo $foundingYear; ?>.</p>

                    <div class="as-why-name">
                        <div class="as-quote-icon"><i class="fas fa-quote-left"></i></div>
                        <h3>Why the name <em>Lukman?</em></h3>
                        <p>"If Allah was to assign me with prophethood I would accept it and try to win His pleasure, but He enabled me to choose. I feared of being too weak for prophethood, so I chose wisdom."</p>
                        <cite class="as-cite">&mdash; Lukman Al-Hakim</cite>
                    </div>

                    <a href="about.php" class="as-cta">Discover More About Us <i class="fas fa-arrow-right ms-2"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════════════════════════
         SCHOOL GALLERY — Filtered Photo Showcase
         ══════════════════════════════════════════ -->
    <section id="gallery-home" class="gallery-home-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <span class="badge-section">SCHOOL GALLERY</span>
                <h2>Get a Little Closer</h2>
                <p class="subtitle">A glimpse into daily life, events and activities at Lukman Primary School</p>
            </div>

            <div class="gal-toolbar" data-aos="fade-up" data-aos-delay="60">
                <p class="gal-toolbar-note"><i class="fas fa-search-plus"></i> Tap any photo for zoom view</p>
                <div class="gal-filters" role="group" aria-label="Gallery categories">
                    <button type="button" class="btn gal-filter active" data-filter="all" aria-pressed="true">All</button>
                    <button type="button" class="btn gal-filter" data-filter="campus" aria-pressed="false">Campus</button>
                    <button type="button" class="btn gal-filter" data-filter="learning" aria-pressed="false">Learning</button>
                    <button type="button" class="btn gal-filter" data-filter="activities" aria-pressed="false">Activities</button>
                    <button type="button" class="btn gal-filter" data-filter="sports" aria-pressed="false">Sports</button>
                </div>
            </div>

            <!-- Gallery Grid -->
            <div class="row g-3 gal-grid" data-aos="fade-up" data-aos-delay="100">
                <!-- Row 1: 3 items (5-4-3) -->
                <div class="col-lg-5 col-md-6 gal-cell" data-category="activities">
                    <a href="assets/images/lukman-primary-school-student-with-buganda-prince.jpg" data-lightbox="gallery" data-title="Student with Buganda Prince">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/lukman-primary-school-student-with-buganda-prince.jpg" alt="Lukman PS student with Buganda prince" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Student with Buganda Prince</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 gal-cell" data-category="campus">
                    <a href="assets/images/HK2_9749-min.jpg" data-lightbox="gallery" data-title="Students at Assembly">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/HK2_9749-min.jpg" alt="Students at assembly" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Students at Assembly</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 gal-cell" data-category="learning">
                    <a href="assets/images/AY1A0277-min.jpg" data-lightbox="gallery" data-title="Classroom Activities">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/AY1A0277-min.jpg" alt="Classroom activities" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Classroom Activities</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Row 2: 4 equal items (3-3-3-3) -->
                <div class="col-lg-3 col-md-6 gal-cell" data-category="campus">
                    <a href="assets/images/Assembly-time-4-scaled.jpg" data-lightbox="gallery" data-title="School Assembly Time">
                        <div class="gal-card">
                            <img src="assets/images/Assembly-time-4-scaled.jpg" alt="School assembly time" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>School Assembly</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 gal-cell" data-category="learning">
                    <a href="assets/images/AY1A1480-min.jpg" data-lightbox="gallery" data-title="Focused Learning">
                        <div class="gal-card">
                            <img src="assets/images/AY1A1480-min.jpg" alt="Students learning" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Focused Learning</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 gal-cell" data-category="activities">
                    <a href="assets/images/AY1A1343-min.jpg" data-lightbox="gallery" data-title="Co-curricular Activities">
                        <div class="gal-card">
                            <img src="assets/images/AY1A1343-min.jpg" alt="Co-curricular activities" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Co-curricular Activities</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6 gal-cell" data-category="campus">
                    <a href="assets/images/teaching-staff-2-scaled.jpg" data-lightbox="gallery" data-title="Teaching Staff">
                        <div class="gal-card">
                            <img src="assets/images/teaching-staff-2-scaled.jpg" alt="Teaching staff at Lukman PS" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Teaching Staff</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Row 3: 3 items (3-5-4) -->
                <div class="col-lg-3 col-md-6 gal-cell" data-category="sports">
                    <a href="assets/images/sports-at-lukman-ps.jpg" data-lightbox="gallery" data-title="Sports at Lukman PS">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/sports-at-lukman-ps.jpg" alt="Sports activities" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Sports Activities</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-5 col-md-6 gal-cell" data-category="activities">
                    <a href="assets/images/lukman-primary-school-students-doing-drama.jpg" data-lightbox="gallery" data-title="Drama Performance">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/lukman-primary-school-students-doing-drama.jpg" alt="Students performing drama" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Drama Performance</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-lg-4 col-md-6 gal-cell" data-category="learning">
                    <a href="assets/images/AY1A0029-min.jpg" data-lightbox="gallery" data-title="Students in Session">
                        <div class="gal-card gal-card--lg">
                            <img src="assets/images/AY1A0029-min.jpg" alt="Students in learning session" loading="lazy">
                            <div class="gal-overlay">
                                <i class="fas fa-search-plus"></i>
                                <span>Students in Session</span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Photo count + CTA -->
            <div class="gal-footer" data-aos="fade-up" data-aos-delay="120">
                <span class="gal-count"><i class="fas fa-camera me-2"></i><span id="gal-visible-count">11</span> of 50+ Photos</span>
                <a href="gallery.php" class="gal-cta">
                    View Full Gallery <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- School Impact -->
    <section id="impact" class="impact-v2-section">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="badge-section">OUR IMPACT</span>
                <h2 class="section-title">Making a Difference</h2>
                <p class="section-subtitle">Building a legacy of academic excellence, moral development and leadership from Entebbe since <?php echo $foundingYear; ?>.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="60">
                    <article class="impact-v2-card impact-v2-card--feature">
                        <div class="impact-v2-icon"><i class="fas fa-award"></i></div>
                        <h5>Consistent Division 1</h5>
                        <p>Year-on-year top PLE performance — our P7 pupils consistently earn Division 1 passes in national examinations.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="120">
                    <article class="impact-v2-card impact-v2-card--feature">
                        <div class="impact-v2-icon"><i class="fas fa-book-quran"></i></div>
                        <h5>Ḥifẓ Programme</h5>
                        <p>Dedicated Quran memorisation track with certified sheikhs — multiple pupils achieve full Ḥifẓ every year.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="180">
                    <article class="impact-v2-card impact-v2-card--feature">
                        <div class="impact-v2-icon"><i class="fas fa-shield-alt"></i></div>
                        <h5>Safe Boarding</h5>
                        <p>24/7 supervised dormitories, nutritious meals, an on-call nurse, and round-the-clock security for every child.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="240">
                    <article class="impact-v2-card impact-v2-card--feature">
                        <div class="impact-v2-icon"><i class="fas fa-users"></i></div>
                        <h5>Strong Community</h5>
                        <p>An active alumni network and engaged parent community — the Lukman family continues long after graduation.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="home-team" class="team-home-section section-pad">
        <div class="container">
            <div class="team-home-header" data-aos="fade-up">
                <span class="badge-section">OUR LEADERSHIP</span>
                <h2>Meet Our Team</h2>
                <p>Dedicated educators and mentors committed to nurturing young minds with excellence and faith.</p>
            </div>

            <div class="row g-4 justify-content-center">
                <?php foreach ($teamMembers as $index => $member): ?>
                <div class="col-xl-3 col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo 80 + ($index * 60); ?>">
                    <article class="team-home-card">
                        <div class="team-home-photo-wrap">
                            <div class="team-home-photo">
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
                            <span class="team-home-badge"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $member['department'] ?? 'teaching'))); ?></span>
                        </div>
                        <div class="team-home-info">
                            <h4><?php echo htmlspecialchars($member['name']); ?></h4>
                            <p><?php echo htmlspecialchars($member['position']); ?></p>
                            <?php if (!empty($member['qualification'])): ?>
                            <div class="team-home-qual"><i class="fas fa-certificate"></i> <?php echo htmlspecialchars($member['qualification']); ?></div>
                            <?php endif; ?>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="team-home-cta" data-aos="fade-up" data-aos-delay="140">
                <a href="team.php" class="btn-outline-custom">
                    <i class="fas fa-users me-2"></i>View Full Team
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <?php if (!empty($testimonials)): ?>
    <section class="section-pad" style="background: var(--gray-light);">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <span class="badge-section">TESTIMONIALS</span>
                <h2>What Parents & Students Say</h2>
                <p class="subtitle">Hear from our school community about their experience at Lukman Primary School</p>
            </div>
            <div class="row g-4">
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card">
                        <i class="fas fa-quote-left testimonial-icon"></i>
                        <p class="testimonial-text"><?php echo htmlspecialchars($testimonial['content']); ?></p>
                        <div class="testimonial-author">
                            <strong><?php echo htmlspecialchars($testimonial['name']); ?></strong>
                            <?php if (!empty($testimonial['role'])): ?>
                            <span><?php echo htmlspecialchars($testimonial['role']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="testimonials.php" class="btn-outline-custom">
                    <i class="fas fa-star me-2"></i>View All Testimonials
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Latest News -->
    <?php if (!empty($latestNews)): ?>
    <section id="news" class="section-pad">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">LATEST UPDATES</span>
                <h2>News & Articles</h2>
                <p class="subtitle">Stay informed about school activities and achievements</p>
            </div>
            <div class="row g-4">
                <?php foreach ($latestNews as $news): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="news-card">
                        <?php if (!empty($news['featured_image'])): 
                            $newsImgPath = $news['featured_image'];
                            if (strpos($newsImgPath, '/') === false) {
                                $newsImgPath = 'news/' . $newsImgPath;
                            }
                        ?>
                        <div class="news-image">
                            <img src="uploads/<?php echo htmlspecialchars($newsImgPath); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php endif; ?>
                        <div class="news-content">
                            <div class="news-meta">
                                <span><i class="far fa-calendar me-1"></i><?php echo date('M d, Y', strtotime($news['published_at'])); ?></span>
                            </div>
                            <h4><a href="news-detail.php?id=<?php echo $news['id']; ?>"><?php echo htmlspecialchars($news['title']); ?></a></h4>
                            <p style="color: var(--gray-text); font-size: 0.95rem;">
                                <?php echo htmlspecialchars(substr(strip_tags($news['content'] ?? ''), 0, 120)); ?>...
                            </p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" style="color: var(--primary-blue); font-weight: 600; text-decoration: none; font-size: 0.9rem;">
                                Read More <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="news.php" class="btn-outline-custom">
                    View All News <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Upcoming Events -->
    <?php if (!empty($upcomingEvents)): ?>
    <section class="section-pad" style="background: var(--gray-light);">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">UPCOMING EVENTS</span>
                <h2>Join Our Events</h2>
                <p class="subtitle">Participate in school ceremonies, sports days, and community events</p>
            </div>
            <div class="row g-4">
                <?php foreach ($upcomingEvents as $event): ?>
                <div class="col-lg-4 col-md-6" data-aos="fade-up">
                    <article class="event-card">
                        <?php if (!empty($event['featured_image'])): ?>
                        <div class="event-card-img">
                            <img src="uploads/<?php echo htmlspecialchars($event['featured_image']); ?>"
                                 alt="<?php echo htmlspecialchars($event['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="event-card-body">
                            <div class="event-card-meta">
                                <div class="event-card-date">
                                    <span class="event-card-day"><?php echo date('d', strtotime($event['event_date'])); ?></span>
                                    <span class="event-card-month"><?php echo strtoupper(date('M', strtotime($event['event_date']))); ?></span>
                                </div>
                                <div class="event-card-title-wrap">
                                    <h4 class="event-card-title"><?php echo htmlspecialchars($event['title']); ?></h4>
                                    <?php if (!empty($event['location'])): ?>
                                    <p class="event-card-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="event-card-desc"><?php echo htmlspecialchars(substr(strip_tags($event['description'] ?? ''), 0, 100)); ?>&hellip;</p>
                            <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="event-card-link">
                                View Details <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </article>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="events.php" class="btn-primary-custom">
                    View All Events <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA Section -->
    <section id="cta" class="cta-home-section section-pad">
        <div class="container">
            <div class="cta-home-shell" data-aos="zoom-in">
                <div class="row g-4 align-items-center">
                    <div class="col-lg-8">
                        <div class="cta-home-chip"><i class="fas fa-graduation-cap"></i><span>Admissions Open</span></div>
                        <h2>Enroll Your Child Today</h2>
                        <p>Give your child the gift of quality education at Lukman Primary School with our dual curriculum, caring teachers, and values-driven learning environment.</p>
                        <div class="cta-home-points">
                            <span><i class="fas fa-check"></i> Structured learning pathways</span>
                            <span><i class="fas fa-check"></i> Holistic spiritual development</span>
                            <span><i class="fas fa-check"></i> Safe, serene campus in Entebbe</span>
                        </div>
                        <div class="cta-home-actions">
                            <a href="admissions.php" class="btn-green-custom cta-home-btn-primary">
                                <i class="fas fa-file-alt me-2"></i>Apply for Admission
                            </a>
                            <a href="contact.php" class="cta-home-btn-secondary">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <aside class="cta-home-contact-box">
                            <h3>Need Help?</h3>
                            <p>Our admissions office is ready to guide you through requirements, fees, and school visits.</p>
                            <a href="tel:+256753034553"><i class="fas fa-phone"></i> +256 753 034 553</a>
                            <a href="mailto:info@lukmanps.ac.ug"><i class="fas fa-envelope"></i> info@lukmanps.ac.ug</a>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

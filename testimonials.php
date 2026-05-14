<?php
$currentPage = 'testimonials';
$pageTitle   = 'Testimonials';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');

$pageDescription = 'Read what parents and former pupils say about Lukman Primary School — academic excellence, boarding life, and Islamic values education in Entebbe, Uganda.';

// Handle testimonial submission
$submitSuccess = false;
$submitError   = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_testimonial'])) {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $submitError = 'Invalid form token. Please refresh and try again.';
    } else {
        $name    = trim(strip_tags($_POST['name']    ?? ''));
        $role    = trim(strip_tags($_POST['role']    ?? ''));
        $content = trim(strip_tags($_POST['content'] ?? ''));
        $rating  = min(5, max(1, (int)($_POST['rating'] ?? 5)));

        if (!$name)    $submitError = 'Your name is required.';
        elseif (!$content || strlen($content) < 20) $submitError = 'Please write at least 20 characters for your testimonial.';
        else {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, role, content, rating, status, created_at) VALUES (?,?,?,?,'pending', NOW())");
            $stmt->execute([$name, $role, $content, $rating]);
            $submitSuccess = true;
        }
    }
}

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Testimonials']]);

// Fetch approved testimonials from DB
$stmt = $pdo->query("SELECT * FROM testimonials WHERE status = 'approved' ORDER BY display_order ASC, created_at DESC");
$dbTestimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fallback hardcoded testimonials if DB is empty
$fallbackTestimonials = [
    [
        'name'       => 'Hajjat Fatuma Nakibuuka',
        'position'   => 'Parent of P7 pupil',
        'content'    => 'My son sat PLE from Lukman and scored Division 1. The teachers are dedicated and the school environment keeps children focused. The Islamic studies alongside the national curriculum is exactly what I wanted for my child.',
        'rating'     => 5,
    ],
    [
        'name'       => 'Mr. Emmanuel Ssekandi',
        'position'   => 'Parent of two pupils',
        'content'    => 'We enrolled our daughter in Primary 3 three years ago. Her academic performance and discipline have improved tremendously. The boarding section is well managed and she is in safe hands.',
        'rating'     => 5,
    ],
    [
        'name'       => 'Asha Nampiima',
        'position'   => 'Former pupil (Class of 2020)',
        'content'    => 'I joined Lukman in P5 and went on to score Division 2 at PLE. The Quran lessons and the teachers who cared about us beyond books made a real difference in who I am today. I am proud to be a Lukman alumna.',
        'rating'     => 5,
    ],
    [
        'name'       => 'Alhaji Musa Kateregga',
        'position'   => 'Parent of three children',
        'content'    => 'All three of my children have passed through Lukman Primary School. The school has consistently produced top results while also grounding children in Islamic values. I recommend it to every parent in Entebbe.',
        'rating'     => 5,
    ],
    [
        'name'       => 'Mrs. Grace Namagembe',
        'position'   => 'Parent of P4 boarder',
        'content'    => 'I was worried about sending my daughter to board at such a young age. But the staff at Lukman are warm, professional, and keep us informed. She has settled beautifully and her reading has improved beyond expectations.',
        'rating'     => 5,
    ],
    [
        'name'       => 'Umar Wasswa',
        'position'   => 'Former pupil (Class of 2018)',
        'content'    => 'Now at secondary school, I can honestly say Lukman gave me the strongest foundation. The early morning Quran sessions, the strict academics, and the sense of community — I carry all of it with me.',
        'rating'     => 5,
    ],
];

$testimonials = !empty($dbTestimonials) ? $dbTestimonials : $fallbackTestimonials;
?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>What Parents &amp; Pupils Say</h1>
            <p>Real voices from the <?php echo htmlspecialchars($siteName); ?> community</p>
        </div>
    </div>

    <!-- Testimonials -->
    <section class="section-pad">
        <div class="container">
            <div class="text-center mb-5">
                <span class="badge-section">TESTIMONIALS</span>
                <h2 class="section-heading">Hear From Our Community</h2>
                <p style="color:var(--gray-text);max-width:600px;margin:0 auto;">Meet the parents, former pupils, and community members who trust Lukman Primary School with their children's futures.</p>
            </div>

            <div class="row g-4">
                <?php foreach ($testimonials as $t): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-card" style="background:var(--white);border-radius:12px;padding:2rem;height:100%;display:flex;flex-direction:column;box-shadow:0 2px 16px rgba(0,0,0,0.07);">
                        <!-- Stars -->
                        <div style="color:#FFC107;margin-bottom:1rem;font-size:1.1rem;">
                            <?php $rating = (int)($t['rating'] ?? 5);
                            for ($i=0;$i<$rating;$i++) echo '<i class="fas fa-star"></i> ';
                            ?>
                        </div>
                        <!-- Quote -->
                        <p style="color:var(--gray-text);line-height:1.8;font-size:0.97rem;flex:1;font-style:italic;">
                            &ldquo;<?php echo htmlspecialchars($t['content']); ?>&rdquo;
                        </p>
                        <!-- Author -->
                        <div style="display:flex;align-items:center;gap:0.75rem;margin-top:1.5rem;">
                            <?php if (!empty($t['photo'])): ?>
                            <img src="uploads/testimonials/<?php echo htmlspecialchars($t['photo']); ?>" alt="<?php echo htmlspecialchars($t['name']); ?>" style="width:48px;height:48px;border-radius:50%;object-fit:cover;">
                            <?php else: ?>
                            <div style="width:48px;height:48px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.1rem;">
                                <?php echo strtoupper(substr($t['name'],0,1)); ?>
                            </div>
                            <?php endif; ?>
                            <div>
                                <strong style="color:var(--dark-blue);display:block;"><?php echo htmlspecialchars($t['name']); ?></strong>
                                <span style="color:var(--gray-text);font-size:0.85rem;"><?php echo htmlspecialchars($t['position'] ?? ''); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="section-pad-sm" style="background:var(--gray-light);">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-lg-3 col-6">
                    <div>
                        <h3 class="stat-value">500+</h3>
                        <p style="color:var(--gray-text);font-weight:600;margin:0;">Enrolled Pupils</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div>
                        <h3 class="stat-value">20+</h3>
                        <p style="color:var(--gray-text);font-weight:600;margin:0;">Years of Excellence</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div>
                        <h3 class="stat-value">Div 1</h3>
                        <p style="color:var(--gray-text);font-weight:600;margin:0;">Consistent PLE Results</p>
                    </div>
                </div>
                <div class="col-lg-3 col-6">
                    <div>
                        <h3 class="stat-value">4.9</h3>
                        <p style="color:var(--gray-text);font-weight:600;margin:0;">Parent Satisfaction</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Submit Testimonial -->
    <section class="section-pad" id="share-story" style="background:var(--accent-light);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="text-center mb-4">
                        <span class="badge-section" style="display:inline-block;margin-bottom:1rem;">SHARE YOUR STORY</span>
                        <h2 class="section-heading">Has Lukman Made a Difference?</h2>
                        <p style="color:var(--gray-text);line-height:1.8;">
                            We love hearing from parents, former pupils, and community members. Share your experience below — all submissions are reviewed before publishing.
                        </p>
                    </div>

                    <?php if ($submitSuccess): ?>
                    <div class="alert alert-success text-center py-4" role="alert">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                        <strong>Thank you for your testimonial!</strong><br>
                        Your submission has been received and will appear on this page after review.
                    </div>
                    <?php else: ?>

                    <?php if ($submitError): ?>
                    <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($submitError); ?></div>
                    <?php endif; ?>

                    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-3">
                        <form method="POST" action="testimonials.php#share-story">
                            <?php echo csrfField(); ?>
                            <input type="hidden" name="submit_testimonial" value="1">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                           required maxlength="100"
                                           placeholder="e.g. Fatima Nakato"
                                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Your Role / Relationship</label>
                                    <input type="text" name="role" class="form-control form-control-lg"
                                           maxlength="100"
                                           placeholder="e.g. Parent of P.6 pupil"
                                           value="<?php echo htmlspecialchars($_POST['role'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your Rating</label>
                                <div class="d-flex gap-2 align-items-center" id="star-rating">
                                    <?php for ($s = 5; $s >= 1; $s--): ?>
                                    <input type="radio" class="d-none" name="rating" id="star<?php echo $s; ?>" value="<?php echo $s; ?>"
                                           <?php echo (($_POST['rating'] ?? 5) == $s) ? 'checked' : ''; ?>>
                                    <label for="star<?php echo $s; ?>" class="star-label fs-3" style="cursor:pointer;color:#ccc;" title="<?php echo $s; ?> star<?php echo $s > 1 ? 's' : ''; ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Your Testimonial <span class="text-danger">*</span></label>
                                <textarea name="content" class="form-control" rows="5" required minlength="20"
                                          placeholder="Tell us about your experience at Lukman Primary School…"><?php echo htmlspecialchars($_POST['content'] ?? ''); ?></textarea>
                                <div class="form-text text-muted">Minimum 20 characters. Your identity will be displayed as submitted.</div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn-primary-custom px-5" style="padding:.8rem 2.5rem;">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Testimonial
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <style>
    /* Star rating RTL trick — stars flow left but highlight left-to-right */
    #star-rating { flex-direction: row-reverse; justify-content: flex-end; }
    #star-rating input:checked ~ label,
    #star-rating label:hover,
    #star-rating label:hover ~ label { color: var(--secondary) !important; }
    </style>


    <!-- CTA -->
    <section id="cta" class="section-pad-sm">
        <div class="container text-center">
            <h2 class="section-heading" style="color:var(--white);">Join the Lukman Family</h2>
            <p style="color:rgba(255,255,255,0.9);margin-bottom:2rem;">Give your child the education they deserve. Enrol today.</p>
            <a href="admissions.php" class="btn-green-custom" style="padding:0.8rem 2rem;"><i class="fas fa-user-plus me-2"></i>Apply for Admission</a>
            <a href="contact.php" style="display:inline-block;background:transparent;color:var(--white);border:2px solid var(--white);padding:0.8rem 2rem;font-weight:600;border-radius:6px;text-decoration:none;margin-left:0.5rem;"><i class="fas fa-phone me-2"></i>Contact Us</a>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
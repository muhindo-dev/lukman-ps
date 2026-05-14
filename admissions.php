<?php
$currentPage = 'admissions';
$pageTitle   = 'Admissions';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageDescription = 'Apply for admission to ' . $siteName . ' in Entebbe, Uganda. Learn about requirements, fees, boarding options, and how to enrol your child from Baby Class through P7.';

// Handle form submission
$formSuccess = false;
$formErrors  = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
    // CSRF validation
    if (!validateCsrfToken()) {
        $formErrors[] = 'Invalid form submission. Please refresh the page and try again.';
    }
    $parentName    = trim(strip_tags($_POST['parent_name']   ?? ''));
    $parentEmail   = trim($_POST['parent_email']  ?? '');
    $parentPhone   = trim(strip_tags($_POST['parent_phone']  ?? ''));
    $childName     = trim(strip_tags($_POST['child_name']    ?? ''));
    $childDob      = trim($_POST['child_dob']      ?? '');
    $childGender   = trim($_POST['child_gender']   ?? '');
    $currentSchool = trim(strip_tags($_POST['current_school'] ?? ''));
    $classApplying = trim($_POST['class_applying'] ?? '');
    $boardingType  = trim($_POST['boarding_type']  ?? '');
    $howHeard      = trim(strip_tags($_POST['how_heard']     ?? ''));
    $message       = trim(strip_tags($_POST['message']       ?? ''));

    if (!$parentName)  $formErrors[] = 'Parent/guardian name is required.';
    if (!filter_var($parentEmail, FILTER_VALIDATE_EMAIL)) $formErrors[] = 'A valid email address is required.';
    if (!$parentPhone) $formErrors[] = 'Phone number is required.';
    if (!$childName)   $formErrors[] = "Child's name is required.";
    if (!$childDob)    $formErrors[] = "Child's date of birth is required.";
    if (!in_array($childGender, ['male', 'female'])) $formErrors[] = 'Please select gender.';
    $validClasses = ['Baby Class', 'Middle Class', 'Top Class', 'P1','P2','P3','P4','P5','P6','P7'];
    if (!in_array($classApplying, $validClasses)) $formErrors[] = 'Please select a class.';
    if (!in_array($boardingType, ['boarding', 'day'])) $formErrors[] = 'Please select boarding or day option.';

    // Honeypot — bots fill the hidden field; humans leave it empty
    if (!empty($_POST['website_url'])) {
        // Silently reject without revealing the honeypot
        $formSuccess = true;
        goto skip_save;
    }

    // Data consent
    if (empty($_POST['data_consent'])) $formErrors[] = 'Please accept the data processing consent.';

    if (empty($formErrors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO admission_inquiries
                (parent_name, parent_email, parent_phone, child_name, child_dob, child_gender,
                 current_school, class_applying, boarding_type, how_heard, message, ip_address)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $parentName, $parentEmail, $parentPhone, $childName, $childDob, $childGender,
                $currentSchool, $classApplying, $boardingType, $howHeard, $message,
                $_SERVER['REMOTE_ADDR']
            ]);
            $formSuccess = true;
            // Notify admin
            $adminEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');
            $mailSubject = 'New Admission Inquiry: ' . $childName . ' (' . $classApplying . ')';
            $mailBody = "New admission inquiry received\n\n"
                . "--- PARENT / GUARDIAN ---\n"
                . "Name:  $parentName\nEmail: $parentEmail\nPhone: $parentPhone\n\n"
                . "--- CHILD ---\n"
                . "Name:      $childName\nDOB:       $childDob\nGender:    $childGender\n"
                . "Applying:  $classApplying  |  Boarding: $boardingType\n"
                . "Current school: $currentSchool\n"
                . "How heard: $howHeard\n\n"
                . "Message:\n$message\n\n"
                . '---\nSent from ' . getSetting('site_name', 'Lukman Primary School') . ' website\n'
                . 'View in admin: https://lukmanps.ac.ug/admin/admission-inquiries.php';
            $mailHeaders = "From: noreply@lukmanps.ac.ug\r\nReply-To: $parentEmail\r\nX-Mailer: PHP/" . phpversion();
            $mailSent = mail($adminEmail, $mailSubject, $mailBody, $mailHeaders);
            if (!$mailSent) {
                error_log("[LPS Admissions] Mail delivery failed to: $adminEmail | Child: $childName ($classApplying)");
            }
        } catch (PDOException $e) {
            $formErrors[] = 'Sorry, we could not save your inquiry at this time. Please try again or call us directly.';
        }
    }
    skip_save:
}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Admissions</h1>
        <p>Begin your child's journey at <?php echo htmlspecialchars($siteShortName); ?></p>
    </div>
</div>

<main id="main-content">

    <!-- Steps Overview -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">How to Apply</span>
                <h2 class="section-title">Simple Admission Process</h2>
                <p class="section-subtitle">Four easy steps to get your child started at <?php echo htmlspecialchars($siteShortName); ?></p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                    <div class="step-card text-center">
                        <div class="step-number">1</div>
                        <h5>Submit Inquiry</h5>
                        <p>Fill in the online inquiry form below or visit the school office in Entebbe.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="150">
                    <div class="step-card text-center">
                        <div class="step-number">2</div>
                        <h5>School Visit</h5>
                        <p>We will invite you to visit the school, tour facilities, and meet the headteacher.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                    <div class="step-card text-center">
                        <div class="step-number">3</div>
                        <h5>Submit Documents</h5>
                        <p>Bring the required documents and pay the registration fee to confirm your child's place.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3" data-aos="fade-up" data-aos-delay="250">
                    <div class="step-card text-center">
                        <div class="step-number">4</div>
                        <h5>Enrolment</h5>
                        <p>Collect your child's admission letter, uniform list, and term dates. Welcome to the family!</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Requirements & Fees -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="row g-5">
                <!-- Requirements -->
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-badge">Documents Required</span>
                    <h3 class="section-title-left">Admission Requirements</h3>
                    <ul class="feature-list mt-3">
                        <li><i class="fas fa-check text-success me-2"></i>Completed admission inquiry form</li>
                        <li><i class="fas fa-check text-success me-2"></i>Birth certificate or baptism card (original + copy)</li>
                        <li><i class="fas fa-check text-success me-2"></i>2 recent passport-size photographs</li>
                        <li><i class="fas fa-check text-success me-2"></i>Latest school report (for P2–P7 transfers)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Transfer certificate (for transfers from another school)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Medical fitness certificate (boarding pupils)</li>
                        <li><i class="fas fa-check text-success me-2"></i>Immunisation card (P1 new entrants)</li>
                    </ul>
                    <p class="mt-3"><strong>Age requirement:</strong> P1 pupils must be at least 6 years old by January of the admission year.</p>
                </div>

                <!-- Fees Structure -->
                <div class="col-lg-6" data-aos="fade-left">
                    <span class="section-badge">School Fees</span>
                    <h3 class="section-title-left">Fees Structure</h3>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead style="background:var(--primary);color:#fff;">
                                <tr>
                                    <th>Item</th>
                                    <th>Boarding (UGX)</th>
                                    <th>Day (UGX)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>Tuition</td><td>Contact school</td><td>Contact school</td></tr>
                                <tr><td>Accommodation</td><td>Included</td><td>—</td></tr>
                                <tr><td>Meals (3/day)</td><td>Included</td><td>Optional</td></tr>
                                <tr><td>Books &amp; Materials</td><td colspan="2" class="text-center">Per term</td></tr>
                                <tr><td>Registration (once)</td><td colspan="2" class="text-center">One-off</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="small text-muted">Full fees structure available on request or in our <a href="downloads.php">Downloads page</a>. Fees are payable at the beginning of each term.</p>
                    <a href="downloads.php" class="btn-secondary-custom mt-2">
                        <i class="fas fa-file-pdf me-2"></i>Download Fees Structure
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Boarding Information -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-badge">Boarding School</span>
                    <h2 class="section-title-left">Life at Our Boarding Facility</h2>
                    <p><?php echo htmlspecialchars($siteShortName); ?> is a full boarding school. Our dormitories are supervised by matrons/patrons 24 hours, creating a safe, structured environment for pupils to focus on learning.</p>
                    <ul class="feature-list mt-3">
                        <li><i class="fas fa-bed text-success me-2"></i>Separate, supervised dormitories for boys and girls</li>
                        <li><i class="fas fa-utensils text-success me-2"></i>Three nutritious meals served daily</li>
                        <li><i class="fas fa-clock text-success me-2"></i>Structured prep (evening study) sessions</li>
                        <li><i class="fas fa-mosque text-success me-2"></i>Prayer times observed throughout the day</li>
                        <li><i class="fas fa-first-aid text-success me-2"></i>On-call nurse and first-aid facilities</li>
                        <li><i class="fas fa-shield-alt text-success me-2"></i>24-hour security on campus</li>
                    </ul>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="info-highlight-box">
                        <h4><i class="fas fa-info-circle me-2"></i>Boarding Terms</h4>
                        <p>The school year runs from January to November with three terms. Exeat (visiting) weekends are scheduled once per term. Parents are notified in advance.</p>
                        <hr>
                        <h4><i class="fas fa-calendar me-2"></i>School Calendar</h4>
                        <p>Term dates are published at the start of each year. Check the <a href="events.php">Events</a> page for upcoming term openings and exeat dates.</p>
                        <hr>
                        <h4><i class="fas fa-phone me-2"></i>More Questions?</h4>
                        <p>Call us on <strong><?php echo htmlspecialchars(getSetting('contact_phone', '+256 700 000 000')); ?></strong> or visit our school in Entebbe during office hours.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Inquiry Form -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Apply Online</span>
                <h2 class="section-title">Admission Inquiry Form</h2>
                <p class="section-subtitle">Fill in the form and we will contact you within 2 working days</p>
            </div>

            <div class="row justify-content-center">
                <div class="col-lg-8" data-aos="fade-up">

                    <?php if ($formSuccess): ?>
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                        <h5>Inquiry Received!</h5>
                        <p class="mb-0">Thank you! We have received your admission inquiry and will contact you at <strong><?php echo htmlspecialchars($parentEmail ?? ''); ?></strong> within 2 working days.</p>
                    </div>
                    <?php else: ?>

                    <?php if (!empty($formErrors)): ?>
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-circle me-1"></i>Please correct the following:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($formErrors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <div class="contact-form-wrapper">
                        <form method="POST" action="admissions.php#inquiry-form" id="inquiry-form" novalidate>
                            <?php echo csrfField(); ?>
                            <h5 class="form-section-title"><i class="fas fa-user me-2"></i>Parent / Guardian Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="parent_name" class="form-control" required
                                        value="<?php echo htmlspecialchars($_POST['parent_name'] ?? ''); ?>"
                                        placeholder="Your full name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                    <input type="tel" name="parent_phone" class="form-control" required
                                        value="<?php echo htmlspecialchars($_POST['parent_phone'] ?? ''); ?>"
                                        placeholder="+256 700 000 000">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="parent_email" class="form-control" required
                                        value="<?php echo htmlspecialchars($_POST['parent_email'] ?? ''); ?>"
                                        placeholder="your@email.com">
                                </div>
                            </div>

                            <h5 class="form-section-title"><i class="fas fa-child me-2"></i>Child's Information</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Child's Full Name <span class="text-danger">*</span></label>
                                    <input type="text" name="child_name" class="form-control" required
                                        value="<?php echo htmlspecialchars($_POST['child_name'] ?? ''); ?>"
                                        placeholder="Child's full name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                    <input type="date" name="child_dob" class="form-control" required
                                        value="<?php echo htmlspecialchars($_POST['child_dob'] ?? ''); ?>"
                                        max="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                                    <select name="child_gender" class="form-select" required>
                                        <option value="">Select gender</option>
                                        <option value="male"   <?php echo ($_POST['child_gender'] ?? '') == 'male'   ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo ($_POST['child_gender'] ?? '') == 'female' ? 'selected' : ''; ?>>Female</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Class Applying For <span class="text-danger">*</span></label>
                                    <select name="class_applying" class="form-select" required>
                                        <option value="">Select class</option>
                                        <optgroup label="Pre-Primary">
                                            <?php foreach (['Baby Class', 'Middle Class', 'Top Class'] as $c): ?>
                                            <option value="<?php echo $c; ?>" <?php echo ($_POST['class_applying'] ?? '') == $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <optgroup label="Primary">
                                            <?php foreach (['P1','P2','P3','P4','P5','P6','P7'] as $c): ?>
                                            <option value="<?php echo $c; ?>" <?php echo ($_POST['class_applying'] ?? '') == $c ? 'selected' : ''; ?>><?php echo $c; ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Boarding or Day? <span class="text-danger">*</span></label>
                                    <select name="boarding_type" class="form-select" required>
                                        <option value="">Select option</option>
                                        <option value="boarding" <?php echo ($_POST['boarding_type'] ?? '') == 'boarding' ? 'selected' : ''; ?>>Boarding</option>
                                        <option value="day"      <?php echo ($_POST['boarding_type'] ?? '') == 'day'      ? 'selected' : ''; ?>>Day Scholar</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Current School (if transferring)</label>
                                    <input type="text" name="current_school" class="form-control"
                                        value="<?php echo htmlspecialchars($_POST['current_school'] ?? ''); ?>"
                                        placeholder="Name of current or previous school">
                                </div>
                            </div>

                            <h5 class="form-section-title"><i class="fas fa-comment me-2"></i>Additional Information</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">How did you hear about us?</label>
                                    <select name="how_heard" class="form-select">
                                        <option value="">Select one</option>
                                        <option value="friend" <?php echo ($_POST['how_heard'] ?? '') == 'friend' ? 'selected' : ''; ?>>Friend / Family</option>
                                        <option value="social_media" <?php echo ($_POST['how_heard'] ?? '') == 'social_media' ? 'selected' : ''; ?>>Social Media</option>
                                        <option value="former_pupil" <?php echo ($_POST['how_heard'] ?? '') == 'former_pupil' ? 'selected' : ''; ?>>Former Pupil</option>
                                        <option value="signboard" <?php echo ($_POST['how_heard'] ?? '') == 'signboard' ? 'selected' : ''; ?>>School Signboard</option>
                                        <option value="other" <?php echo ($_POST['how_heard'] ?? '') == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Additional Message or Questions</label>
                                    <textarea name="message" class="form-control" rows="4"
                                        placeholder="Any special needs, questions about fees, or other information..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                                </div>
                                <!-- Honeypot — hidden from real users, traps bots -->
                                <input type="text" name="website_url" tabindex="-1" autocomplete="off" aria-hidden="true" style="position:absolute;left:-9999px;width:1px;height:1px;overflow:hidden;">
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="data_consent" id="data_consent" required
                                            <?php echo !empty($_POST['data_consent']) ? 'checked' : ''; ?>>
                                        <label class="form-check-label small" for="data_consent">
                                            I consent to <?php echo htmlspecialchars($siteName); ?> storing and processing my child's information for admission purposes in accordance with our <a href="privacy.php" target="_blank" rel="noopener">Privacy Policy</a>. <span class="text-danger">*</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="submit_inquiry" class="btn-primary-custom w-100">
                                        <i class="fas fa-paper-plane me-2"></i>Submit Inquiry
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container text-center" data-aos="zoom-in">
            <h2>Have more questions about joining <?php echo htmlspecialchars($siteShortName); ?>?</h2>
            <p>Our admissions team is happy to answer via phone, email, or in person.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn-cta-primary">
                    <i class="fas fa-envelope me-2"></i>Contact Us
                </a>
                <a href="faq.php" class="btn-cta-secondary">
                    <i class="fas fa-question-circle me-2"></i>Read the FAQ
                </a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

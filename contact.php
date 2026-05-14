<?php
$currentPage = 'contact';
$pageTitle = 'Contact Us';
include 'config.php';
include 'functions.php';

$siteName = getSetting('site_name', 'Lukman Primary School');
$contactEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');
$contactPhone = getSetting('contact_phone', '+256 782 284788');
$contactPhoneAlt = getSetting('contact_phone_alt', '+256 705 070995');
$contactAddress = getSetting('contact_address', 'Entebbe Municipality, Wakiso District');
$contactCity = getSetting('contact_city', 'Entebbe');
$officeHours = getSetting('office_hours', 'Monday - Friday: 7:30 AM - 5:00 PM');

$pageDescription = 'Contact Lukman Primary School in Entebbe, Uganda. Reach our admin office for admissions, fees, and general inquiries.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Contact Us']]);

// Handle form submission
$successMessage = '';
$errorMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errorMessage = 'Invalid form submission. Please try again.';
    } else {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($message)) {
        $errorMessage = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message, status, ip_address, created_at) VALUES (?, ?, ?, ?, ?, 'new', ?, NOW())");
            $stmt->execute([$name, $email, $phone, $subject, $message, $_SERVER['REMOTE_ADDR'] ?? null]);
            $successMessage = 'Thank you for your message! We will get back to you soon.';
            // Notify admin
            $adminEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');
            $mailSubject = 'New Contact Inquiry: ' . ($subject ?: '(no subject)');
            $mailBody = "New contact inquiry received\n\n"
                . "Name: $name\n"
                . "Email: $email\n"
                . "Phone: $phone\n"
                . "Subject: $subject\n\n"
                . "Message:\n$message\n\n"
                . '---\nSent from ' . getSetting('site_name', 'Lukman Primary School') . ' website';
            $mailHeaders = "From: noreply@lukmanps.ac.ug\r\nReply-To: $email\r\nX-Mailer: PHP/" . phpversion();
            $mailSent = mail($adminEmail, $mailSubject, $mailBody, $mailHeaders);
            if (!$mailSent) {
                error_log("[LPS Contact] Mail delivery failed to: $adminEmail | Subject: $mailSubject");
            }
        } catch (PDOException $e) {
            $errorMessage = 'Sorry, there was an error sending your message. Please try again.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
    } // end CSRF else
}
?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
            <p>Get in touch with Lukman Primary School. We are here to help.</p>
        </div>
    </div>

    <!-- Contact Section -->
    <section class="section-pad">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Info -->
                <div class="col-lg-5">
                    <h3 style="margin-bottom: 1.5rem;">Get in Touch</h3>
                    <p style="color: var(--gray-text); margin-bottom: 2rem; line-height: 1.7;">
                        Have questions about admissions, fees, boarding, or school activities? We would love to hear from you.
                    </p>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <h5>Head Office</h5>
                            <p><?php echo htmlspecialchars($contactAddress); ?><br><?php echo htmlspecialchars($contactCity); ?>, Uganda</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                            <h5>Phone</h5>
                            <p><a href="<?php echo getPhoneLink($contactPhone); ?>" style="color: var(--gray-text); text-decoration: none;"><?php echo htmlspecialchars($contactPhone); ?></a><br>
                            <a href="<?php echo getPhoneLink($contactPhoneAlt); ?>" style="color: var(--gray-text); text-decoration: none;"><?php echo htmlspecialchars($contactPhoneAlt); ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <h5>Email</h5>
                            <p><a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>" style="color: var(--gray-text); text-decoration: none;"><?php echo htmlspecialchars($contactEmail); ?></a></p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-item-icon"><i class="fas fa-clock"></i></div>
                        <div>
                            <h5>Office Hours</h5>
                            <p><?php echo htmlspecialchars($officeHours); ?><br>Sunday: Closed</p>
                        </div>
                    </div>

                    <!-- Google Map — coordinates: Lukman PS, Entebbe (0.0670° N, 32.4600° E) -->
                    <div style="margin-top:2rem;border-radius:12px;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.1);">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.7558!2d32.4600!3d0.0670!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMcKwMDQnMDEuMiJOIDMywrAyNycyMC4wIkU!5e0!3m2!1sen!2sug!4v1"
                            width="100%" height="260" style="border:0;display:block;" allowfullscreen loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            title="Lukman Primary School location on Google Maps"></iframe>
                    </div>
                    <div class="text-center mt-2">
                        <a href="https://www.google.com/maps/search/Lukman+Primary+School+Entebbe+Uganda/@0.0670,32.4600,15z"
                           target="_blank" rel="noopener noreferrer"
                           style="font-size:.875rem;color:var(--primary);font-weight:600;">
                            <i class="fas fa-directions me-1"></i>Get Directions on Google Maps
                        </a>
                    </div>
                </div>
                
                <!-- Contact Form -->
                <div class="col-lg-7">
                    <div style="background: var(--gray-light); border-radius: 12px; padding: 2.5rem;">
                        <h3 style="margin-bottom: 1.5rem;">Send us a Message</h3>
                        
                        <?php if ($successMessage): ?>
                        <div class="alert alert-custom alert-success" style="display: block;">
                            <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($successMessage); ?>
                        </div>
                        <?php endif; ?>
                        <?php if ($errorMessage): ?>
                        <div class="alert alert-custom alert-error" style="display: block;">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($errorMessage); ?>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="contact-form">
                            <?php echo csrfField(); ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="contact-name" class="visually-hidden">Your Name</label>
                                    <input type="text" id="contact-name" name="name" class="form-control" placeholder="Your Name *" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact-email" class="visually-hidden">Your Email</label>
                                    <input type="email" id="contact-email" name="email" class="form-control" placeholder="Your Email *" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="contact-phone" class="visually-hidden">Phone Number</label>
                                    <input type="tel" id="contact-phone" name="phone" class="form-control" placeholder="Phone Number" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="contact-subject" class="visually-hidden">Subject</label>
                                    <input type="text" id="contact-subject" name="subject" class="form-control" placeholder="Subject" value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                                </div>
                            </div>
                            <label for="contact-message" class="visually-hidden">Your Message</label>
                            <textarea id="contact-message" name="message" class="form-control" rows="5" placeholder="Your Message *" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                            <button type="submit" class="btn-submit mt-2">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Links -->
    <section class="section-pad" style="background: var(--gray-light);">
        <div class="container">
            <div class="section-title">
                <span class="badge-section">QUICK LINKS</span>
                <h2>You May Also Be Looking For</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-3 col-md-6">
                    <div class="branch-card text-center">
                        <div class="branch-icon mx-auto"><i class="fas fa-user-plus"></i></div>
                        <h5>Admissions</h5>
                        <p>Requirements, fees, and how to apply</p>
                        <a href="admissions.php">Apply Now &rarr;</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="branch-card text-center">
                        <div class="branch-icon mx-auto"><i class="fas fa-graduation-cap"></i></div>
                        <h5>PLE Results</h5>
                        <p>View our Uganda national exam results</p>
                        <a href="results.php">View Results &rarr;</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="branch-card text-center">
                        <div class="branch-icon mx-auto"><i class="fas fa-download"></i></div>
                        <h5>Downloads</h5>
                        <p>Forms, brochures, and school documents</p>
                        <a href="downloads.php">Browse Downloads &rarr;</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="branch-card text-center">
                        <div class="branch-icon mx-auto"><i class="fas fa-book-open"></i></div>
                        <h5>Academics</h5>
                        <p>Curriculum, subjects, and school timetable</p>
                        <a href="academics.php">Learn More &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>

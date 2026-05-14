<?php
$currentPage = 'privacy';
$pageTitle   = 'Privacy Policy';
include 'config.php';
include 'functions.php';

$siteName     = getSetting('site_name', 'Lukman Primary School');
$contactEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');
$contactPhone = getSetting('contact_phone', '+256 782 284788');
$foundingYear = getSetting('founding_year', '1997');

$pageDescription = 'Privacy Policy for ' . $siteName . ' — how we collect, use, and protect personal data in compliance with the Uganda Data Protection and Privacy Act 2019.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Privacy Policy']]);
?>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1>Privacy Policy</h1>
            <p>How we collect, use, and protect your personal information</p>
        </div>
    </div>

    <main id="main-content">
        <section class="section-pad">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-9">

                        <div style="background:var(--white);border-radius:12px;padding:2.5rem;box-shadow:0 2px 16px rgba(0,0,0,.06);">

                            <p class="text-muted mb-4" style="font-size:.9rem;">
                                <strong>Last updated:</strong> <?php echo date('d F Y'); ?> &nbsp;|&nbsp;
                                <strong>Effective from:</strong> 1 January 2025
                            </p>

                            <!-- 1 -->
                            <h3 class="mt-4">1. Who We Are</h3>
                            <p><?php echo htmlspecialchars($siteName); ?> ("<strong>the School</strong>", "<strong>we</strong>", "<strong>us</strong>") is a mixed boarding primary school located in Entebbe, Wakiso District, Uganda. We are committed to protecting the privacy of all individuals who interact with our website and services.</p>
                            <p>This policy applies to personal data collected through:</p>
                            <ul>
                                <li>Our website at <strong>lukmanps.ac.ug</strong></li>
                                <li>Our admission inquiry and contact forms</li>
                                <li>Our newsletter subscription service</li>
                            </ul>

                            <!-- 2 -->
                            <h3 class="mt-4">2. Legal Basis</h3>
                            <p>We process personal data in compliance with the <strong>Uganda Data Protection and Privacy Act, 2019</strong> (DPPA 2019) and its accompanying regulations. By submitting any form on this website you give your informed consent for us to process the data you provide.</p>

                            <!-- 3 -->
                            <h3 class="mt-4">3. What Data We Collect</h3>

                            <h5 class="mt-3">3.1 Admission Inquiry Form</h5>
                            <ul>
                                <li>Parent / guardian full name</li>
                                <li>Parent / guardian email address and phone number</li>
                                <li>Child's full name, date of birth, and gender</li>
                                <li>Child's current school (optional)</li>
                                <li>Class applied for and boarding preference</li>
                                <li>How you heard about us (optional)</li>
                                <li>Any additional message you choose to provide</li>
                            </ul>

                            <h5 class="mt-3">3.2 Contact Form</h5>
                            <ul>
                                <li>Your name, email address, and phone number</li>
                                <li>Subject and message content</li>
                            </ul>

                            <h5 class="mt-3">3.3 Newsletter Subscription</h5>
                            <ul>
                                <li>Your name and email address</li>
                            </ul>

                            <h5 class="mt-3">3.4 Automatically Collected Data</h5>
                            <ul>
                                <li>Your IP address (stored with each form submission for security purposes)</li>
                                <li>Browser type and operating system (via standard server logs)</li>
                                <li>Pages visited and time of visit (if Google Analytics is enabled)</li>
                                <li>Cookie preferences</li>
                            </ul>

                            <!-- 4 -->
                            <h3 class="mt-4">4. How We Use Your Data</h3>
                            <table class="table table-bordered mt-2">
                                <thead style="background:var(--primary);color:#fff;">
                                    <tr><th>Purpose</th><th>Legal Basis</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Responding to admission inquiries and arranging school visits</td><td>Consent / Legitimate interest</td></tr>
                                    <tr><td>Responding to general contact form messages</td><td>Consent</td></tr>
                                    <tr><td>Sending school newsletters (if subscribed)</td><td>Consent</td></tr>
                                    <tr><td>Preventing duplicate or fraudulent submissions</td><td>Legitimate interest</td></tr>
                                    <tr><td>Improving our website through analytics</td><td>Consent (via cookie banner)</td></tr>
                                    <tr><td>Maintaining website security and server logs</td><td>Legitimate interest</td></tr>
                                </tbody>
                            </table>
                            <p>We <strong>do not</strong> sell, rent, or share your personal data with third parties for marketing purposes.</p>

                            <!-- 5 -->
                            <h3 class="mt-4">5. Data Storage &amp; Security</h3>
                            <p>All data you submit through this website is stored in a secured database hosted on our school server. We apply the following safeguards:</p>
                            <ul>
                                <li>Encrypted database connections (SSL/TLS)</li>
                                <li>Access restricted to authorised school administrators only</li>
                                <li>Regular backups stored securely off-site</li>
                                <li>HTTPS encryption for all website traffic</li>
                                <li>CSRF (Cross-Site Request Forgery) protection on all forms</li>
                            </ul>
                            <p>Despite these measures, no internet transmission is 100% secure. Please do not submit sensitive financial or medical details through the contact form — contact us by phone for such matters.</p>

                            <!-- 6 -->
                            <h3 class="mt-4">6. Cookies</h3>
                            <p>Our website uses cookies to:</p>
                            <ul>
                                <li>Remember your cookie consent preference (<code>lps_cookie_consent</code>) — expires after 1 year</li>
                                <li>Maintain your session while using the site (session cookies — expire when you close your browser)</li>
                                <li>Collect anonymous analytics data (Google Analytics — only if you accept cookies)</li>
                            </ul>
                            <p>You can withdraw cookie consent at any time by clearing your browser cookies. Refusing cookies will not affect your ability to use the website.</p>

                            <!-- 7 -->
                            <h3 class="mt-4">7. How Long We Keep Your Data</h3>
                            <table class="table table-bordered mt-2">
                                <thead style="background:var(--primary);color:#fff;">
                                    <tr><th>Data Type</th><th>Retention Period</th></tr>
                                </thead>
                                <tbody>
                                    <tr><td>Admission inquiry records</td><td>3 years (or until resolved, whichever is sooner)</td></tr>
                                    <tr><td>Contact form messages</td><td>2 years</td></tr>
                                    <tr><td>Newsletter subscriptions</td><td>Until you unsubscribe</td></tr>
                                    <tr><td>Server / access logs</td><td>90 days</td></tr>
                                    <tr><td>Google Analytics data</td><td>As per Google's retention settings (default 26 months)</td></tr>
                                </tbody>
                            </table>

                            <!-- 8 -->
                            <h3 class="mt-4">8. Your Rights</h3>
                            <p>Under the Uganda Data Protection and Privacy Act 2019, you have the right to:</p>
                            <ul>
                                <li><strong>Access</strong> — request a copy of personal data we hold about you</li>
                                <li><strong>Rectification</strong> — ask us to correct inaccurate data</li>
                                <li><strong>Erasure</strong> — request deletion of your personal data</li>
                                <li><strong>Restriction</strong> — ask us to limit how we process your data</li>
                                <li><strong>Withdrawal of consent</strong> — withdraw consent at any time without affecting prior lawful processing</li>
                                <li><strong>Complaint</strong> — lodge a complaint with the <strong>Personal Data Protection Office (PDPO)</strong> of Uganda</li>
                            </ul>
                            <p>To exercise any of these rights, please contact us using the details in Section 10 below.</p>

                            <!-- 9 -->
                            <h3 class="mt-4">9. Third-Party Services</h3>
                            <p>Our website may use the following third-party services, each with their own privacy policies:</p>
                            <ul>
                                <li><strong>Google Analytics</strong> — website usage analytics (only active if you accept cookies)</li>
                                <li><strong>Google Maps</strong> — embedded map on our Contact page (Google's privacy policy applies)</li>
                                <li><strong>WhatsApp</strong> — the WhatsApp chat button links to WhatsApp's service (Meta's privacy policy applies)</li>
                                <li><strong>Google Fonts</strong> — fonts loaded from Google's CDN (Google's privacy policy applies)</li>
                            </ul>

                            <!-- 10 -->
                            <h3 class="mt-4">10. Contact Us About Privacy</h3>
                            <p>For any privacy-related questions, requests, or complaints, please contact:</p>
                            <div style="background:var(--gray-light);border-radius:8px;padding:1.25rem 1.5rem;margin-top:.75rem;">
                                <strong><?php echo htmlspecialchars($siteName); ?></strong><br>
                                Data Controller<br>
                                Entebbe Municipality, Wakiso District, Uganda<br>
                                <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a><br>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $contactPhone); ?>"><?php echo htmlspecialchars($contactPhone); ?></a>
                            </div>

                            <!-- 11 -->
                            <h3 class="mt-4">11. Changes to This Policy</h3>
                            <p>We may update this Privacy Policy from time to time. The "Last updated" date at the top of this page will reflect any changes. We encourage you to review this page periodically. Continued use of the website after changes constitutes acceptance of the revised policy.</p>

                        </div><!-- /card -->

                        <div class="text-center mt-4">
                            <a href="contact.php" class="btn-outline-custom">
                                <i class="fas fa-envelope me-2"></i>Contact Us with Privacy Questions
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>

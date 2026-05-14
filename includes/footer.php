<?php
// Load site settings if not already loaded
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../functions.php';
}

// Get commonly used settings for footer
$siteName = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'LPS');
$_footerFoundingYear = (int) getSetting('founding_year', '1997');
$footerAbout = getSetting('footer_about', 'Lukman Primary School is a mixed boarding primary school in Entebbe, Uganda offering an enriched dual secular and Islamic theology curriculum since ' . $_footerFoundingYear . '.');
$logoIconClass = getSetting('logo_icon_class', 'fas fa-school');
$contactEmail = getSetting('contact_email', 'info@lukmanps.ac.ug');
$contactPhone = getSetting('contact_phone');
$contactPhoneAlt = getSetting('contact_phone_alt');
$contactAddress = getSetting('contact_address', 'Entebbe');
$contactCity = getSetting('contact_city', 'Wakiso');
$footerText = getSetting('footer_text', 'All Rights Reserved Lukman Primary School');
$developerName = getSetting('developer_name', 'TusomeTech');
$developerUrl = getSetting('developer_url', 'https://tusometech.com');
$whatsappNumber = getSetting('whatsapp_number');
$whatsappEnabled = getSetting('enable_whatsapp_chat') == '1';
$whatsappMessage = getSetting('whatsapp_default_message', 'Hello, I would like to know more about Lukman Primary School.');
$customFooterCode = getSetting('custom_footer_code');

// Get social links
$socialLinks = getSocialLinks();
?>
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row g-4">
                <!-- About Column -->
                <div class="col-lg-3">
                    <div class="footer-section">
                        <h4><i class="<?php echo htmlspecialchars($logoIconClass); ?> me-2" style="color: var(--primary, #00723F);"></i><?php echo htmlspecialchars($siteShortName); ?></h4>
                        <p style="color: rgba(255,255,255,0.7); line-height: 1.8; font-size: 0.95rem;"><?php echo htmlspecialchars($footerAbout); ?></p>
                        <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-top: 0.5rem; font-style: italic;">Motto: Seek knowledge and attain wisdom.</p>
                        <div class="d-flex gap-2 mt-3">
                            <?php if (!empty($socialLinks['facebook'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['facebook']); ?>" class="social-link" target="_blank" rel="noopener" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['twitter'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['twitter']); ?>" class="social-link" target="_blank" rel="noopener" title="Twitter/X"><i class="fab fa-twitter"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['instagram'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['instagram']); ?>" class="social-link" target="_blank" rel="noopener" title="Instagram"><i class="fab fa-instagram"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['linkedin'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['linkedin']); ?>" class="social-link" target="_blank" rel="noopener" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['youtube'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['youtube']); ?>" class="social-link" target="_blank" rel="noopener" title="YouTube"><i class="fab fa-youtube"></i></a>
                            <?php endif; ?>
                            <?php if (!empty($socialLinks['tiktok'])): ?>
                            <a href="<?php echo htmlspecialchars($socialLinks['tiktok']); ?>" class="social-link" target="_blank" rel="noopener" title="TikTok"><i class="fab fa-tiktok"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <!-- Quick Links -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h4>About Us</h4>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">School Background</a></li>
                            <li><a href="about.php#mission">Mission &amp; Vision</a></li>
                            <li><a href="admissions.php">Admissions</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                            <li><a href="faq.php">FAQ</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Academics -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h4>Academics</h4>
                        <ul>
                            <li><a href="academics.php">Academics</a></li>
                            <li><a href="results.php">PLE Results</a></li>
                            <li><a href="downloads.php">Resources</a></li>
                            <li><a href="student-life.php">Student Life</a></li>
                            <li><a href="gallery.php">School Gallery</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Publications -->
                <div class="col-lg-2 col-md-6">
                    <div class="footer-section">
                        <h4>Publications</h4>
                        <ul>
                            <li><a href="news.php">News</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="downloads.php">Circulars</a></li>
                            <li><a href="downloads.php">Downloads</a></li>
                            <li><a href="testimonials.php">Testimonials</a></li>
                        </ul>
                    </div>
                </div>
                <!-- Contact Info -->
                <div class="col-lg-3">
                    <div class="footer-section">
                        <h4>Contact Us</h4>
                        <?php if ($contactAddress): ?>
                        <div class="footer-contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($contactAddress); ?><?php if ($contactCity): ?>, <?php echo htmlspecialchars($contactCity); ?>, Uganda<?php endif; ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($contactPhone): ?>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="<?php echo getPhoneLink($contactPhone); ?>"><?php echo htmlspecialchars($contactPhone); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($contactPhoneAlt): ?>
                        <div class="footer-contact-item">
                            <i class="fas fa-phone"></i>
                            <a href="<?php echo getPhoneLink($contactPhoneAlt); ?>"><?php echo htmlspecialchars($contactPhoneAlt); ?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($contactEmail): ?>
                        <div class="footer-contact-item">
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <!-- Hijri date -->
                <p style="font-size:.8rem; opacity:.55; margin-bottom:.4rem; letter-spacing:.03em;">
                    <i class="fas fa-moon me-1" style="color:#EA1B27;"></i>
                    <?php echo getHijriDateToday(); ?> &nbsp;·&nbsp;
                    <span style="opacity:.8;"><?php echo date('d F Y'); ?></span>
                </p>
                <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($footerText); ?> | Developed by <a href="<?php echo htmlspecialchars($developerUrl); ?>" target="_blank" rel="noopener" style="color: var(--primary, #00723F); text-decoration: none;"><?php echo htmlspecialchars($developerName); ?></a> | <a href="privacy.php" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.85rem;">Privacy Policy</a></p>
            </div>
        </div>
    </footer>

    <?php if ($whatsappEnabled && $whatsappNumber): ?>
    <!-- WhatsApp Floating Button -->
    <a href="<?php echo getWhatsAppLink($whatsappNumber, $whatsappMessage); ?>" class="whatsapp-float" target="_blank" rel="noopener" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <style>
    .whatsapp-float {
        position: fixed;
        bottom: 90px; /* sits above back-to-top and cookie banner */
        right: 20px;
        width: 56px;
        height: 56px;
        background: #25D366;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        box-shadow: 0 4px 15px rgba(37,211,102,0.4);
        z-index: 9998;
        transition: background 0.25s ease, transform 0.25s ease, box-shadow 0.25s ease;
    }
    .whatsapp-float:hover {
        background: #128C7E;
        color: #fff;
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(37,211,102,0.5);
    }
    /* When cookie banner is visible, push WhatsApp further up */
    body.cookie-banner-active .whatsapp-float { bottom: 150px; }
    @media (max-width: 576px) {
        .whatsapp-float { bottom: 80px; right: 14px; }
        body.cookie-banner-active .whatsapp-float { bottom: 160px; }
    }
    </style>
    <?php endif; ?>

    <!-- Back-to-top button -->
    <button id="back-to-top" class="btt-btn" aria-label="Back to top" title="Back to top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
            integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"
            integrity="sha512-Ixzuzfxv1UFoHVtBBEFLd4LI+MGGhgFxFWcAnFPeWdkG8p8KVq3l/5ILTFR4I9Bq7fFxpb61pRMdlRFcPGKkQ=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        if (typeof lightbox !== 'undefined') {
            lightbox.option({
                'resizeDuration': 200,
                'wrapAround': true,
                'albumLabel': 'Image %1 of %2',
                'disableScrolling': true
            });
        }
    </script>
    
    <?php if ($customFooterCode): ?>
    <?php echo $customFooterCode; ?>
    <?php endif; ?>

    <!-- Cookie Consent Banner -->
    <div class="cookie-consent-banner" id="cookieBanner" role="dialog" aria-live="polite" aria-label="Cookie notice" style="display:none">
        <div class="cookie-consent-inner">
            <p class="cookie-consent-text">
                <i class="fas fa-cookie-bite me-2"></i>
                This site uses cookies to improve your experience.
                <a href="privacy.php" class="cookie-link">Learn more</a>
            </p>
            <button class="cookie-accept-btn" id="cookieAccept" onclick="acceptCookies()">Accept</button>
        </div>
    </div>
    <style>
    .cookie-consent-banner {
        position: fixed; bottom: 0; left: 0; right: 0; z-index: 10000;
        background: #1a1a2e; color: #fff; padding: 14px 20px;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.25);
    }
    .cookie-consent-inner {
        max-width: 960px; margin: 0 auto;
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        flex-wrap: wrap;
    }
    .cookie-consent-text { margin: 0; font-size: 0.92rem; color: rgba(255,255,255,0.88); flex: 1; }
    .cookie-link { color: var(--primary, #00723F); text-decoration: underline; }
    .cookie-accept-btn {
        background: var(--primary, #00723F); color: #fff;
        border: none; border-radius: 6px; padding: 8px 22px;
        font-size: 0.9rem; font-weight: 600; cursor: pointer; white-space: nowrap;
        transition: background 0.2s;
    }
    .cookie-accept-btn:hover { background: #005a30; }
    @media (max-width: 480px) {
        .cookie-consent-inner { flex-direction: column; align-items: flex-start; }
        .cookie-accept-btn { width: 100%; text-align: center; }
    }
    </style>
    <script>
    function acceptCookies() {
        var d = new Date(); d.setFullYear(d.getFullYear() + 1);
        document.cookie = 'lps_cookie_consent=1; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
        document.getElementById('cookieBanner').style.display = 'none';
        document.body.classList.remove('cookie-banner-active');
    }
    (function() {
        if (!/lps_cookie_consent=1/.test(document.cookie)) {
            document.getElementById('cookieBanner').style.display = 'block';
            document.body.classList.add('cookie-banner-active');
        }
    })();
    </script>
</body>
</html>

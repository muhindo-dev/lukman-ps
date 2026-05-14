<?php
// Load site settings if not already loaded
if (!function_exists('getSetting')) {
    require_once __DIR__ . '/../functions.php';
}

// Get commonly used settings
$siteName = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'LPS');
$siteTagline = getSetting('site_tagline', 'Seek Knowledge and Attain Wisdom');
$siteDescription = getSetting('site_description', 'Lukman Primary School is a mixed boarding primary school in Entebbe, Uganda offering dual secular and Islamic theology curriculum.');
$siteLogo = getSiteLogo();
$siteFavicon = getSiteFavicon();
$logoIconClass = getSetting('logo_icon_class', 'fas fa-school');
$metaTitle = getSetting('meta_title', $siteName);
$metaKeywords = getSetting('meta_keywords', 'Lukman Primary School, Entebbe, Uganda, Islamic school, boarding school, primary education');
$googleAnalyticsId = getSetting('google_analytics_id');
$enableAnalytics = getSetting('enable_google_analytics') == '1';
$customHeadCode = getSetting('custom_head_code');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' . $siteShortName : $metaTitle; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(isset($pageDescription) ? $pageDescription : $siteDescription); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta name="robots" content="<?php echo isset($noIndex) && $noIndex ? 'noindex, nofollow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1'; ?>">

    <!-- Canonical URL — query strings stripped; base URL defined in config to prevent HTTP_HOST spoofing -->
    <?php
    $siteBaseUrl  = defined('SITE_BASE_URL') ? SITE_BASE_URL : 'https://lukmanps.ac.ug';
    $canonicalPath = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
    ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($siteBaseUrl . $canonicalPath); ?>">
    
    <!-- Favicon -->
    <?php if ($siteFavicon): 
        $faviconExt = strtolower(pathinfo($siteFavicon, PATHINFO_EXTENSION));
        $faviconType = ($faviconExt === 'ico') ? 'image/x-icon' : 'image/' . $faviconExt;
    ?>
    <link rel="icon" type="<?php echo $faviconType; ?>" href="<?php echo htmlspecialchars($siteFavicon); ?>">
    <link rel="shortcut icon" type="<?php echo $faviconType; ?>" href="<?php echo htmlspecialchars($siteFavicon); ?>">
    <link rel="apple-touch-icon" href="<?php echo htmlspecialchars($siteFavicon); ?>">
    <?php endif; ?>
    
    <!-- Open Graph / Social Media -->
    <?php
    // OG image: prefer uploaded image, fall back to the school logo
    $ogImageSetting = getSetting('og_image');
    $ogImageUrl = $ogImageSetting
        ? $siteBaseUrl . '/uploads/' . $ogImageSetting
        : $siteBaseUrl . '/assets/images/lukman-transparent-bordered-logo.png';
    $twitterHandle = getSetting('twitter_handle', '@LukmanPS');
    $ogPageUrl = htmlspecialchars($siteBaseUrl . $canonicalPath);
    ?>
    <meta property="og:title" content="<?php echo htmlspecialchars(getSetting('og_title', $siteName)); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars(getSetting('og_description', $siteDescription)); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $ogPageUrl; ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
    <meta property="og:image:alt" content="<?php echo htmlspecialchars($siteName); ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars(getSetting('og_title', $siteName)); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars(getSetting('og_description', $siteDescription)); ?>">
    <meta name="twitter:site" content="<?php echo htmlspecialchars($twitterHandle); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($ogImageUrl); ?>">
    <meta name="twitter:image:alt" content="<?php echo htmlspecialchars($siteName); ?>">
    
    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "EducationalOrganization",
        "name": "<?php echo htmlspecialchars($siteName); ?>",
        "alternateName": "<?php echo htmlspecialchars($siteShortName); ?>",
        "url": "https://lukmanps.ac.ug",
        "description": "<?php echo htmlspecialchars($siteDescription); ?>",
        "email": "<?php echo htmlspecialchars(getSetting('contact_email', 'info@lukmanps.ac.ug')); ?>",
        "telephone": "<?php echo htmlspecialchars(getSetting('contact_phone')); ?>",
        "foundingDate": "<?php echo htmlspecialchars(getSetting('founding_year', '1997')); ?>",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Entebbe",
            "addressRegion": "Wakiso",
            "addressCountry": "UG"
        },
        "sameAs": [
            <?php
            $socials = [];
            if ($fb = getSetting('facebook_url')) $socials[] = '"' . htmlspecialchars($fb) . '"';
            if ($tw = getSetting('twitter_url')) $socials[] = '"' . htmlspecialchars($tw) . '"';
            if ($ig = getSetting('instagram_url')) $socials[] = '"' . htmlspecialchars($ig) . '"';
            if ($yt = getSetting('youtube_url')) $socials[] = '"' . htmlspecialchars($yt) . '"';
            echo implode(",\n            ", $socials);
            ?>
        ]
    }
    </script>
    <?php if (isset($currentPage) && $currentPage === 'home'): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?php echo htmlspecialchars($siteName); ?>",
        "url": "https://lukmanps.ac.ug"
    }
    </script>
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM"
          crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css"
          integrity="sha512-ZKX+BvQihRJPA8CROKBhDNvoc2aDMOdAlcm7TUQY+35XYtrd3yh95QOOhm7/7eBCH3DqTlFgWVZG1LppRbk3g=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.1/dist/aos.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <?php if ($enableAnalytics && $googleAnalyticsId): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($googleAnalyticsId); ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo htmlspecialchars($googleAnalyticsId); ?>');
    </script>
    <?php endif; ?>
    
    <?php if ($customHeadCode): ?>
    <?php echo $customHeadCode; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Skip to main content — keyboard / screen-reader accessibility -->
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <!-- Top Utility Bar -->
    <div class="hdr-topbar">
        <div class="container hdr-topbar-inner">
            <div class="hdr-topbar-left">
                <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', getSetting('contact_phone', '+256700000000'))); ?>"><i class="fas fa-phone-alt"></i><span><?php echo htmlspecialchars(getSetting('contact_phone', '+256 700 000 000')); ?></span></a>
                <a href="mailto:<?php echo htmlspecialchars(getSetting('contact_email', 'info@lukmanps.ac.ug')); ?>"><i class="fas fa-envelope"></i><span><?php echo htmlspecialchars(getSetting('contact_email', 'info@lukmanps.ac.ug')); ?></span></a>
                <span class="hdr-topbar-loc"><i class="fas fa-map-marker-alt"></i><span>Entebbe, Uganda</span></span>
            </div>
            <div class="hdr-topbar-right">
                <?php if ($wa = getSetting('whatsapp_number')): ?>
                <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $wa); ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                <?php endif; ?>
                <?php if ($fb = getSetting('facebook_url')): ?>
                <a href="<?php echo htmlspecialchars($fb); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                <?php endif; ?>
                <?php if ($tw = getSetting('twitter_url')): ?>
                <a href="<?php echo htmlspecialchars($tw); ?>" target="_blank" rel="noopener" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                <?php endif; ?>
                <?php if ($ig = getSetting('instagram_url')): ?>
                <a href="<?php echo htmlspecialchars($ig); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <?php endif; ?>
                <?php if ($yt = getSetting('youtube_url')): ?>
                <a href="<?php echo htmlspecialchars($yt); ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php if ($siteLogo): ?>
                <img src="<?php echo htmlspecialchars($siteLogo); ?>" alt="<?php echo htmlspecialchars($siteShortName); ?>" class="brand-logo">
                <?php else: ?>
                <img src="assets/images/lukman-transparent-bordered-logo.png" alt="<?php echo htmlspecialchars($siteShortName); ?>" class="brand-logo">
                <?php endif; ?>
                <span>
                    <span class="brand-main"><?php echo htmlspecialchars($siteName); ?></span>
                    <span class="brand-sub"><?php echo htmlspecialchars($siteTagline); ?></span>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($currentPage ?? '', ['about', 'team', 'testimonials']) ? 'active' : ''; ?>" href="#" role="button">About<span class="dropdown-icon"></span></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="about.php"><i class="fas fa-school"></i>About Lukman PS</a></li>
                            <li><a class="dropdown-item" href="team.php"><i class="fas fa-users"></i>Our Team</a></li>
                            <li><a class="dropdown-item" href="testimonials.php"><i class="fas fa-star"></i>Testimonials</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($currentPage ?? '', ['academics', 'results', 'admissions']) ? 'active' : ''; ?>" href="#" role="button">Academics<span class="dropdown-icon"></span></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="academics.php"><i class="fas fa-book-open"></i>Curriculum</a></li>
                            <li><a class="dropdown-item" href="results.php"><i class="fas fa-chart-bar"></i>PLE Results</a></li>
                            <li><a class="dropdown-item" href="admissions.php"><i class="fas fa-user-plus"></i>Admissions</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($currentPage ?? '', ['news', 'events', 'gallery', 'downloads']) ? 'active' : ''; ?>" href="#" role="button">Media<span class="dropdown-icon"></span></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="news.php"><i class="fas fa-newspaper"></i>News</a></li>
                            <li><a class="dropdown-item" href="events.php"><i class="fas fa-calendar-alt"></i>Events</a></li>
                            <li><a class="dropdown-item" href="gallery.php"><i class="fas fa-images"></i>Gallery</a></li>
                            <li><a class="dropdown-item" href="downloads.php"><i class="fas fa-file-pdf"></i>Downloads</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo in_array($currentPage ?? '', ['prayer-times','quran']) ? 'active' : ''; ?>" href="#" role="button">Islamic<span class="dropdown-icon"></span></a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="prayer-times.php"><i class="fas fa-moon"></i>Prayer Times</a></li>
                            <li><a class="dropdown-item" href="quran.php"><i class="fas fa-book-open"></i>&#7716;if&#7827; Wall of Fame</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($currentPage ?? '') == 'contact' ? 'active' : ''; ?>" href="contact.php">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-1">
                        <a class="nav-link <?php echo ($currentPage ?? '') == 'search' ? 'active' : ''; ?>" href="search.php" title="Search" aria-label="Search">
                            <i class="fas fa-search"></i>
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn-cta-nav" href="admissions.php"><i class="fas fa-graduation-cap me-1"></i>Apply Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Notice Strip -->
    <?php
    try {
        $noticeStmt = $pdo->query(
            "SELECT * FROM notices
              WHERE status = 'active'
                AND start_date <= CURDATE()
                AND (end_date IS NULL OR end_date >= CURDATE())
              ORDER BY is_pinned DESC, created_at DESC
              LIMIT 5"
        );
        $activeNotices = $noticeStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $activeNotices = [];
    }
    if (!empty($activeNotices)):
    $typeStyles = [
        'info'    => ['bg'=>'#e8f4fd','border'=>'#00723F','text'=>'#00723F','icon'=>'fa-info-circle'],
        'warning' => ['bg'=>'#fff8e1','border'=>'#DAA520','text'=>'#856404','icon'=>'fa-exclamation-triangle'],
        'urgent'  => ['bg'=>'#fdecea','border'=>'#dc3545','text'=>'#842029','icon'=>'fa-exclamation-circle'],
        'event'   => ['bg'=>'#f0f7f0','border'=>'#198754','text'=>'#145a38','icon'=>'fa-calendar-star'],
    ];
    ?>
    <div id="notice-strip" style="border-bottom: 1px solid #dee2e6;">
    <?php foreach ($activeNotices as $ni):
        $ns = $typeStyles[$ni['type']] ?? $typeStyles['info'];
    ?>
    <div class="notice-item" style="background:<?php echo $ns['bg']; ?>;border-left:4px solid <?php echo $ns['border']; ?>;padding:8px 16px;display:flex;align-items:center;gap:10px;">
        <i class="fas <?php echo $ns['icon']; ?>" style="color:<?php echo $ns['border']; ?>;flex-shrink:0;"></i>
        <span style="color:<?php echo $ns['text']; ?>;font-size:.875rem;font-weight:500;flex:1;">
            <?php echo htmlspecialchars($ni['title']); ?>
            <?php if ($ni['content']):
                $noticeDetailId = 'notice-detail-' . (int) $ni['id'];
            ?>
            <button class="btn btn-link btn-sm p-0 ms-2" style="color:<?php echo $ns['border']; ?>;font-size:.8rem;vertical-align:baseline;"
                    aria-expanded="false" aria-controls="<?php echo $noticeDetailId; ?>"
                    onclick="var d=document.getElementById('<?php echo $noticeDetailId; ?>');var open=d.style.display==='none';d.style.display=open?'block':'none';this.setAttribute('aria-expanded',open?'true':'false');">
                Details
            </button>
            <span id="<?php echo $noticeDetailId; ?>" class="notice-detail" style="display:none;margin-top:4px;font-size:.8rem;font-weight:400;color:inherit;">
                <?php echo nl2br(htmlspecialchars($ni['content'])); ?>
            </span>
            <?php endif; ?>
        </span>
        <?php if ($ni['is_pinned']): ?>
        <i class="fas fa-thumbtack" style="color:<?php echo $ns['border']; ?>;font-size:.75rem;flex-shrink:0;" title="Pinned"></i>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>


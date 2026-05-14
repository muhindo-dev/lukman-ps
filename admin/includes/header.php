<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>LPS Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <?php
    // Get site settings for admin
    $siteLogo = getSetting('site_logo', '');
    $siteLogoLight = getSetting('site_logo_light', '');
    $siteShortName = getSetting('site_short_name', 'Lukman PS');
    $logoIconClass = getSetting('logo_icon_class', 'fas fa-hands-helping');
    $siteFavicon = getSetting('site_favicon', '');
    
    // Use light logo if available, otherwise use main logo
    $adminLogo = $siteLogoLight ?: $siteLogo;
    ?>
    <?php if ($siteFavicon): ?>
    <link rel="icon" href="../uploads/<?php echo htmlspecialchars($siteFavicon); ?>" type="image/x-icon">
    <?php endif; ?>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="brand">
                <?php if ($adminLogo): ?>
                <img src="../uploads/<?php echo htmlspecialchars($adminLogo); ?>" alt="<?php echo htmlspecialchars($siteShortName); ?>" class="brand-logo">
                <?php else: ?>
                <i class="<?php echo htmlspecialchars($logoIconClass); ?>"></i>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($siteShortName); ?> Admin</span>
            </div>
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="sidebar-user">
            <div class="user-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <h4><?php echo htmlspecialchars($currentAdmin['name']); ?></h4>
                <span>Administrator</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <ul>
                <li class="nav-item <?php echo ($currentPage == 'dashboard') ? 'active' : ''; ?>">
                    <a href="index.php">
                        <i class="fas fa-dashboard"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-section">Content Management</li>
                
                <li class="nav-item <?php echo ($currentPage == 'news') ? 'active' : ''; ?>">
                    <a href="news.php">
                        <i class="fas fa-newspaper"></i>
                        <span>News & Blog</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'events') ? 'active' : ''; ?>">
                    <a href="events.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Events</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'gallery') ? 'active' : ''; ?>">
                    <a href="gallery.php">
                        <i class="fas fa-images"></i>
                        <span>Gallery</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'team') ? 'active' : ''; ?>">
                    <a href="team.php">
                        <i class="fas fa-users"></i>
                        <span>Our Team</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'testimonials') ? 'active' : ''; ?>">
                    <a href="testimonials.php">
                        <i class="fas fa-quote-right"></i>
                        <span>Testimonials</span>
                        <?php try { $pdoT = getDBConnection(); $pendingT = $pdoT->query("SELECT COUNT(*) FROM testimonials WHERE status = 'pending'")->fetchColumn(); if ($pendingT > 0) echo '<span class="badge">' . $pendingT . '</span>'; } catch(Exception $e){} ?>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'downloads') ? 'active' : ''; ?>">
                    <a href="downloads.php">
                        <i class="fas fa-download"></i>
                        <span>Downloads</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'results') ? 'active' : ''; ?>">
                    <a href="results.php">
                        <i class="fas fa-graduation-cap"></i>
                        <span>PLE Results</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'admission-inquiries') ? 'active' : ''; ?>">
                    <a href="admission-inquiries.php">
                        <i class="fas fa-user-graduate"></i>
                        <span>Admissions</span>
                        <?php try { $pdo2 = getDBConnection(); $newAdm = $pdo2->query("SELECT COUNT(*) FROM admission_inquiries WHERE status = 'new'")->fetchColumn(); if ($newAdm > 0) echo '<span class="badge">' . $newAdm . '</span>'; } catch(Exception $e){} ?>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'faq') ? 'active' : ''; ?>">
                    <a href="faq.php">
                        <i class="fas fa-question-circle"></i>
                        <span>FAQ</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'calendar') ? 'active' : ''; ?>">
                    <a href="calendar.php">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Calendar</span>
                    </a>
                </li>

                <li class="nav-item <?php echo ($currentPage == 'hifdh') ? 'active' : ''; ?>">
                    <a href="hifdh.php">
                        <i class="fas fa-trophy"></i>
                        <span>&#7716;if&#7827; Wall</span>
                        <?php
                        try {
                            $hPdo = getDBConnection();
                            $hCount = $hPdo->query("SELECT COUNT(*) FROM hifdh_achievements WHERE status='active'")->fetchColumn();
                            if ($hCount > 0) echo '<span class="badge">' . (int)$hCount . '</span>';
                        } catch (Exception $e) {}
                        ?>
                    </a>
                </li>

                <li class="nav-item <?php echo ($currentPage == 'notices') ? 'active' : ''; ?>">
                    <a href="notices.php">
                        <i class="fas fa-bullhorn"></i>
                        <span>Notices</span>
                        <?php
                        try {
                            $pdo = getDBConnection();
                            $n = $pdo->query("SELECT COUNT(*) FROM notices WHERE status='active' AND start_date <= CURDATE() AND (end_date IS NULL OR end_date >= CURDATE())")->fetchColumn();
                            if ($n > 0) echo '<span class="badge">' . $n . '</span>';
                        } catch (Exception $e) {}
                        ?>
                    </a>
                </li>
                
                <li class="nav-section">Communication</li>
                
                <li class="nav-item <?php echo ($currentPage == 'inquiries') ? 'active' : ''; ?>">
                    <a href="inquiries.php">
                        <i class="fas fa-envelope"></i>
                        <span>Inquiries</span>
                        <?php 
                        // Show unread inquiry count
                        try {
                            $pdo = getDBConnection();
                            $checkCol = $pdo->query("SHOW COLUMNS FROM contact_inquiries LIKE 'status'");
                            if ($checkCol->fetch()) {
                                $stmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'");
                                $newInquiries = $stmt->fetchColumn();
                                if ($newInquiries > 0) {
                                    echo '<span class="badge">' . $newInquiries . '</span>';
                                }
                            }
                        } catch (Exception $e) {}
                        ?>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'newsletter') ? 'active' : ''; ?>">
                    <a href="newsletter.php">
                        <i class="fas fa-envelope-open-text"></i>
                        <span>Newsletter</span>
                    </a>
                </li>
                
                <li class="nav-section">Settings</li>
                
                <li class="nav-item <?php echo ($currentPage == 'settings') ? 'active' : ''; ?>">
                    <a href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Site Settings</span>
                    </a>
                </li>
                
                <li class="nav-item <?php echo ($currentPage == 'admins') ? 'active' : ''; ?>">
                    <a href="admins.php">
                        <i class="fas fa-user-shield"></i>
                        <span>Admin Users</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="logout.php" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main" id="adminMain">
        <!-- Top Header -->
        <header class="admin-header">
            <button class="mobile-toggle" id="mobileToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="header-left">
                <h1 class="page-title"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
            </div>
            
            <div class="header-right">
                <a href="../index.php" class="btn-view-site" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Site</span>
                </a>
                <div class="admin-user-menu">
                    <button class="user-menu-toggle" onclick="this.closest('.admin-user-menu').querySelector('.user-dropdown').classList.toggle('show')">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($currentAdmin['name']); ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </button>
                    <div class="user-dropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="admin-content">

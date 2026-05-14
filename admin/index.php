<?php
/**
 * Admin Dashboard
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';
require_once __DIR__ . '/../functions.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'dashboard';
$pageTitle = 'Dashboard';

// Initialize stats
$stats = [
    'news' => ['total' => 0, 'published' => 0, 'draft' => 0],
    'events' => ['total' => 0, 'upcoming' => 0, 'past' => 0],
    'gallery' => ['albums' => 0, 'images' => 0],
    'team' => ['total' => 0, 'active' => 0],
    'inquiries' => ['total' => 0, 'new' => 0, 'replied' => 0],
    'admins' => 0
];

$recentActivity = [];

try {
    $pdo = getDBConnection();
    if ($pdo) {
        // News stats
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM news_posts");
            $stats['news']['total'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM news_posts WHERE status = 'published'");
            $stats['news']['published'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM news_posts WHERE status = 'draft'");
            $stats['news']['draft'] = $stmt->fetchColumn();
        } catch (PDOException $e) {}
        
        // Events stats
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM events");
            $stats['events']['total'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date >= NOW()");
            $stats['events']['upcoming'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM events WHERE event_date < NOW()");
            $stats['events']['past'] = $stmt->fetchColumn();
        } catch (PDOException $e) {}
        
        // Gallery stats
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM gallery_albums");
            $stats['gallery']['albums'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM gallery_images");
            $stats['gallery']['images'] = $stmt->fetchColumn();
        } catch (PDOException $e) {}
        
        // Team stats
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM team_members");
            $stats['team']['total'] = $stmt->fetchColumn();
            $stmt = $pdo->query("SELECT COUNT(*) FROM team_members WHERE status = 'active'");
            $stats['team']['active'] = $stmt->fetchColumn();
        } catch (PDOException $e) {}
        
        // Inquiries stats
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries");
            $stats['inquiries']['total'] = $stmt->fetchColumn();
            
            // Check if status column exists
            $checkCol = $pdo->query("SHOW COLUMNS FROM contact_inquiries LIKE 'status'");
            if ($checkCol->fetch()) {
                $stmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'new'");
                $stats['inquiries']['new'] = $stmt->fetchColumn();
                $stmt = $pdo->query("SELECT COUNT(*) FROM contact_inquiries WHERE status = 'replied'");
                $stats['inquiries']['replied'] = $stmt->fetchColumn();
            }
        } catch (PDOException $e) {}
        
        // Admin users count
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM admin_users WHERE status = 'active'");
            $stats['admins'] = $stmt->fetchColumn();
        } catch (PDOException $e) {}
        
        // Get recent activity (last 10 items from various tables)
        try {
            // Recent news
            $stmt = $pdo->query("SELECT 'news' as type, title as item, created_at FROM news_posts ORDER BY created_at DESC LIMIT 3");
            while ($row = $stmt->fetch()) {
                $recentActivity[] = $row;
            }
            
            // Recent events
            $stmt = $pdo->query("SELECT 'event' as type, title as item, created_at FROM events ORDER BY created_at DESC LIMIT 3");
            while ($row = $stmt->fetch()) {
                $recentActivity[] = $row;
            }
            
            // Recent inquiries
            $stmt = $pdo->query("SELECT 'inquiry' as type, CONCAT(name, ' - ', subject) as item, created_at FROM contact_inquiries ORDER BY created_at DESC LIMIT 4");
            while ($row = $stmt->fetch()) {
                $recentActivity[] = $row;
            }
            
            // Sort by date
            usort($recentActivity, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
            
            // Keep only top 10
            $recentActivity = array_slice($recentActivity, 0, 10);
            
        } catch (PDOException $e) {}
    }
} catch (Exception $e) {
    // Keep default values
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="dashboard-header">
        <div>
            <h1>Welcome back, <?php echo htmlspecialchars($currentAdmin['name']); ?>!</h1>
            <p>Here's an overview of your content management system.</p>
        </div>
        <div class="dashboard-date">
            <i class="fas fa-calendar"></i>
            <?php echo date('l, F d, Y'); ?>
        </div>
    </div>

    <!-- Main Stats Grid -->
    <div class="stats-grid">
        <!-- News Stats -->
        <div class="stat-card">
            <div class="stat-icon news">
                <i class="fas fa-newspaper"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['news']['total']; ?></div>
                <div class="stat-label">News Articles</div>
                <div class="stat-details">
                    <span><?php echo $stats['news']['published']; ?> Published</span>
                    <span><?php echo $stats['news']['draft']; ?> Drafts</span>
                </div>
            </div>
            <a href="news.php" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Events Stats -->
        <div class="stat-card">
            <div class="stat-icon events">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['events']['total']; ?></div>
                <div class="stat-label">Events</div>
                <div class="stat-details">
                    <span><?php echo $stats['events']['upcoming']; ?> Upcoming</span>
                    <span><?php echo $stats['events']['past']; ?> Past</span>
                </div>
            </div>
            <a href="events.php" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Gallery Stats -->
        <div class="stat-card">
            <div class="stat-icon gallery">
                <i class="fas fa-images"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['gallery']['images']; ?></div>
                <div class="stat-label">Gallery Images</div>
                <div class="stat-details">
                    <span><?php echo $stats['gallery']['albums']; ?> Albums</span>
                    <span><?php echo $stats['gallery']['images']; ?> Photos</span>
                </div>
            </div>
            <a href="gallery.php" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Team Stats -->
        <div class="stat-card">
            <div class="stat-icon team">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['team']['total']; ?></div>
                <div class="stat-label">Team Members</div>
                <div class="stat-details">
                    <span><?php echo $stats['team']['active']; ?> Active</span>
                    <span><?php echo $stats['team']['total'] - $stats['team']['active']; ?> Inactive</span>
                </div>
            </div>
            <a href="team.php" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <!-- Inquiries Stats -->
        <div class="stat-card highlight">
            <div class="stat-icon inquiries">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number"><?php echo $stats['inquiries']['new']; ?></div>
                <div class="stat-label">New Inquiries</div>
                <div class="stat-details">
                    <span><?php echo $stats['inquiries']['total']; ?> Total</span>
                    <span><?php echo $stats['inquiries']['replied']; ?> Replied</span>
                </div>
            </div>
            <a href="inquiries.php" class="stat-link">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>

    </div>

    <!-- Content Grid -->
    <div class="dashboard-grid">
        <!-- Recent Activity -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> Recent Activity</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentActivity)): ?>
                    <div class="empty-state">
                        <i class="fas fa-clock"></i>
                        <p>Activity log will appear here once you start managing content.</p>
                    </div>
                <?php else: ?>
                    <div class="activity-list">
                        <?php foreach ($recentActivity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?php echo $activity['type']; ?>">
                                    <?php
                                    $icons = [
                                        'news' => 'fa-newspaper',
                                        'event' => 'fa-calendar',
                                        'inquiry' => 'fa-envelope'
                                    ];
                                    ?>
                                    <i class="fas <?php echo $icons[$activity['type']] ?? 'fa-file'; ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?php echo htmlspecialchars($activity['item']); ?></div>
                                    <div class="activity-time"><?php echo timeAgo($activity['created_at']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div class="quick-actions">
                    <a href="news-add.php" class="quick-action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add News Article</span>
                    </a>
                    <a href="events-add.php" class="quick-action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Event</span>
                    </a>
                    <a href="admissions.php" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <span>Manage Admissions</span>
                    </a>
                    <a href="gallery-add.php" class="quick-action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Album</span>
                    </a>
                    <a href="team-add.php" class="quick-action-btn">
                        <i class="fas fa-plus"></i>
                        <span>Add Team Member</span>
                    </a>
                    <a href="settings.php" class="quick-action-btn">
                        <i class="fas fa-cog"></i>
                        <span>Site Settings</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info (Full Width) -->
    <div class="dashboard-card" style="margin-top: 1rem;">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> System Info</h3>
        </div>
        <div class="card-body">
            <div class="system-info">
                <div class="info-item">
                    <span class="info-label">Active Admins</span>
                    <span class="info-value"><?php echo $stats['admins']; ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Login</span>
                    <span class="info-value"><?php echo date('M d, Y h:i A'); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">PHP Version</span>
                    <span class="info-value"><?php echo phpversion(); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Server Time</span>
                    <span class="info-value"><?php echo date('h:i A'); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' min' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M d, Y', $timestamp);
    }
}
?>

<style>
.admin-content {
    padding: 1.5rem;
}

.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 3px solid #FFC107;
}

.dashboard-header h1 {
    font-size: 1.75rem;
    margin: 0 0 0.25rem 0;
    font-weight: 700;
}

.dashboard-header p {
    margin: 0;
    color: #6c757d;
    font-size: 0.9375rem;
}

.dashboard-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 600;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: #fff;
    border: 2px solid #000;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    transition: transform 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 4px 4px 0 #000;
}

.stat-card.highlight {
    border-color: #FFC107;
    background: #fffef5;
}

.stat-icon {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: #fff;
    flex-shrink: 0;
}

.stat-icon.news { background: #000; }
.stat-icon.events { background: #17a2b8; }
.stat-icon.causes { background: #28a745; }
.stat-icon.gallery { background: #dc3545; }
.stat-icon.team { background: #6f42c1; }
.stat-icon.inquiries { background: #FFC107; color: #000; }

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: #000;
}

.stat-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.stat-details {
    display: flex;
    gap: 1rem;
    font-size: 0.75rem;
    color: #6c757d;
}

.stat-details span {
    display: flex;
    align-items: center;
}

.stat-link {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    border: 2px solid #000;
    color: #000;
    text-decoration: none;
    transition: all 0.2s;
}

.stat-link:hover {
    background: #FFC107;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
}

.dashboard-card {
    background: #fff;
    border: 2px solid #000;
}

.dashboard-card .card-header {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-bottom: 2px solid #000;
}

.dashboard-card .card-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.dashboard-card .card-body {
    padding: 1rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    color: #dee2e6;
}

.empty-state p {
    margin: 0;
    font-size: 0.875rem;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.activity-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #000;
    color: #fff;
    flex-shrink: 0;
}

.activity-icon.news { background: #000; }
.activity-icon.event { background: #17a2b8; }
.activity-icon.inquiry { background: #FFC107; color: #000; }

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-size: 0.875rem;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.activity-time {
    font-size: 0.75rem;
    color: #6c757d;
}

.quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.75rem;
}

.quick-action-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem;
    background: #fff;
    border: 2px solid #000;
    color: #000;
    text-decoration: none;
    font-size: 0.8125rem;
    font-weight: 600;
    transition: all 0.2s;
}

.quick-action-btn:hover {
    background: #FFC107;
    transform: translateX(2px);
}

.quick-action-btn i {
    font-size: 0.875rem;
}

.system-info {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
}

.info-label {
    font-size: 0.8125rem;
    color: #6c757d;
    font-weight: 600;
}

.info-value {
    font-size: 0.875rem;
    font-weight: 700;
    color: #000;
}

@media (max-width: 1200px) {
    .system-info {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
    
    .system-info {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

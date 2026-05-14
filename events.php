<?php 
$currentPage = 'events';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$siteName = getSetting('site_name', 'Lukman Primary School');

$pageTitle = 'Events';
$pageDescription = 'Upcoming events, activities, and community gatherings at ' . $siteName . '.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Events']]);

// Filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'upcoming';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Build query based on filter
if ($filter === 'past') {
    $sql = "SELECT * FROM events WHERE status = 'past' OR (status = 'upcoming' AND event_date < NOW()) ORDER BY event_date DESC LIMIT :limit OFFSET :offset";
    $countSql = "SELECT COUNT(*) FROM events WHERE status = 'past' OR (status = 'upcoming' AND event_date < NOW())";
} else {
    $sql = "SELECT * FROM events WHERE status = 'upcoming' AND event_date >= NOW() ORDER BY event_date ASC LIMIT :limit OFFSET :offset";
    $countSql = "SELECT COUNT(*) FROM events WHERE status = 'upcoming' AND event_date >= NOW()";
}

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$totalEvents = $pdo->query($countSql)->fetchColumn();
$totalPages = ceil($totalEvents / $perPage);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Events</h1>
        <p>Upcoming events and community gatherings</p>
    </div>
</div>

<!-- Filter Tabs -->
<section style="padding: 2rem 0; background: #f8f9fa; border-bottom: 2px solid var(--primary-blue);">
    <div class="container">
        <div class="d-flex justify-content-center gap-3">
            <a href="events.php?filter=upcoming" class="btn" style="border: 2px solid var(--primary-blue); background: <?php echo $filter === 'upcoming' ? 'var(--primary-yellow)' : 'transparent'; ?>; color: <?php echo $filter === 'upcoming' ? 'var(--white)' : 'var(--primary-blue)'; ?>; padding: 0.75rem 2rem; font-weight: 600; text-decoration: none;">
                <i class="far fa-calendar-plus me-2"></i> Upcoming Events
            </a>
            <a href="events.php?filter=past" class="btn" style="border: 2px solid var(--primary-blue); background: <?php echo $filter === 'past' ? 'var(--primary-yellow)' : 'transparent'; ?>; color: <?php echo $filter === 'past' ? 'var(--white)' : 'var(--primary-blue)'; ?>; padding: 0.75rem 2rem; font-weight: 600; text-decoration: none;">
                <i class="far fa-calendar-check me-2"></i> Past Events
            </a>
        </div>
    </div>
</section>

<!-- Events Grid -->
<section style="padding: 80px 0; background: #fff;">
    <div class="container">
        <?php if (empty($events)): ?>
            <div class="text-center" style="padding: 3rem 0;">
                <i class="far fa-calendar" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                <h3 style="color: #666;">No <?php echo $filter; ?> events found</h3>
                <p style="color: #999;">Check back soon for new events!</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($events as $event): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="event-card" style="border: 2px solid var(--primary-blue); border-radius: 0; overflow: hidden; height: 100%; display: flex; flex-direction: column; background: #fff; transition: transform 0.3s;">
                        <?php if (!empty($event['featured_image'])): ?>
                        <div class="event-image" style="height: 220px; overflow: hidden; position: relative;">
                            <img src="uploads/<?php echo htmlspecialchars($event['featured_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            <div class="event-date-badge" style="position: absolute; top: 1rem; left: 1rem; background: var(--primary-yellow); color: var(--white); padding: 0.75rem; border: 2px solid var(--primary-blue); text-align: center; min-width: 70px;">
                                <div style="font-size: 1.75rem; font-weight: 800; line-height: 1;"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                                <div style="font-size: 0.875rem; font-weight: 600;"><?php echo strtoupper(date('M', strtotime($event['event_date']))); ?></div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="event-content" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                            <h3 style="font-size: 1.3rem; font-weight: 600; margin-bottom: 1rem; line-height: 1.4;">
                                <a href="event-detail.php?id=<?php echo $event['id']; ?>" style="color: var(--primary-blue); text-decoration: none;">
                                    <?php echo htmlspecialchars($event['title']); ?>
                                </a>
                            </h3>
                            <div class="event-details" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1.25rem; color: #666; font-size: 0.95rem;">
                                <div><i class="far fa-clock" style="color: var(--primary-green); width: 24px;"></i> <?php echo date('F j, Y - g:i A', strtotime($event['event_date'])); ?></div>
                                <div><i class="fas fa-map-marker-alt" style="color: var(--primary-green); width: 24px;"></i> <?php echo htmlspecialchars($event['location'] ?? ''); ?></div>
                                <?php if (!empty($event['registration_required']) && $filter === 'upcoming'): ?>
                                <div><i class="fas fa-users" style="color: var(--primary-green); width: 24px;"></i> 
                                    <?php if (!empty($event['max_capacity'])): ?>
                                        <?php echo (($event['max_capacity'] ?? 0) - ($event['current_registrations'] ?? 0)); ?> spots remaining
                                    <?php else: ?>
                                        Registration required
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 1.5rem; flex: 1;">
                                <?php echo htmlspecialchars(substr(strip_tags($event['description']), 0, 120)); ?>...
                            </p>
                            <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-sm" style="border: 2px solid var(--primary-blue); background: var(--primary-yellow); color: var(--white); padding: 0.65rem 1.75rem; align-self: flex-start; font-weight: 600; text-decoration: none;">
                                <?php echo $filter === 'past' ? 'View Details' : 'Learn More'; ?> <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="Events pagination" style="margin-top: 3rem;">
                <ul class="pagination justify-content-center" style="gap: 0.5rem;">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="events.php?filter=<?php echo $filter; ?>&page=<?php echo $page - 1; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
                            <i class="fas fa-chevron-left"></i> Previous
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == $page): ?>
                        <li class="page-item active">
                            <span class="page-link" style="border: 2px solid var(--primary-blue); background: var(--primary-yellow); color: var(--white); padding: 0.5rem 1rem; font-weight: 600;">
                                <?php echo $i; ?>
                            </span>
                        </li>
                        <?php else: ?>
                        <li class="page-item">
                            <a class="page-link" href="events.php?filter=<?php echo $filter; ?>&page=<?php echo $i; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="events.php?filter=<?php echo $filter; ?>&page=<?php echo $page + 1; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
                            Next <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

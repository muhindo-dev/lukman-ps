<?php 
$currentPage = 'events';
$pageTitle = 'Event Details';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');

// Get event
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$event) {
    header('Location: events.php');
    exit;
}

$pageTitle = $event['title'] . ' - ' . $siteShortName;
$pageDescription = substr(strip_tags($event['description']), 0, 160);

// Check if event is past
$isPast = strtotime($event['event_date']) < time();
$spotsRemaining = $event['max_capacity'] ? ($event['max_capacity'] - $event['current_registrations']) : null;

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([
    ['label' => 'Events', 'url' => 'events.php'],
    ['label' => mb_strimwidth($event['title'], 0, 60, '…')],
]);

// Event Structured Data
$eventImage = !empty($event['featured_image']) ? 'https://lukmanps.ac.ug/uploads/' . $event['featured_image'] : '';
$eventDate = date('Y-m-d\TH:i:s', strtotime($event['event_date']));
$eventEndDate = !empty($event['end_date']) ? date('Y-m-d\TH:i:s', strtotime($event['end_date'])) : $eventDate;
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Event",
    "name": "<?php echo htmlspecialchars($event['title']); ?>",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "image": "<?php echo $eventImage; ?>",
    "startDate": "<?php echo $eventDate; ?>",
    "endDate": "<?php echo $eventEndDate; ?>",
    "location": {
        "@type": "Place",
        "name": "<?php echo htmlspecialchars($event['location'] ?? ''); ?>",
        "address": "<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
    },
    "organizer": {
        "@type": "Organization",
        "name": "Lukman Primary School",
        "url": "https://lukmanps.ac.ug"
    }
}
</script>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));">
    <div class="container">
        <div class="text-center">
            <div style="margin-bottom: 1rem;">
                <a href="events.php" style="color: var(--white); text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back to Events
                </a>
            </div>
            <?php if ($isPast): ?>
            <div style="margin-bottom: 1rem;">
                <span style="display: inline-block; padding: 0.5rem 1rem; background: #6c757d; color: #fff; font-weight: 600; font-size: 0.9rem;">
                    <i class="far fa-calendar-check"></i> Past Event
                </span>
            </div>
            <?php else: ?>
            <div style="margin-bottom: 1rem;">
                <span style="display: inline-block; padding: 0.5rem 1rem; background: var(--primary-yellow); color: var(--white); font-weight: 600; font-size: 0.9rem;">
                    <i class="far fa-calendar-plus"></i> Upcoming Event
                </span>
            </div>
            <?php endif; ?>
            <h1 style="color: #fff; font-size: 2.75rem; font-weight: 800; margin-bottom: 1rem; max-width: 900px; margin-left: auto; margin-right: auto; line-height: 1.2;">
                <?php echo htmlspecialchars($event['title']); ?>
            </h1>
        </div>
    </div>
</section>

<!-- Event Content -->
<section style="padding: 80px 0; background: #fff;">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if (!empty($event['featured_image'] ?? '')): ?>
                <div style="margin-bottom: 2.5rem; border: 2px solid var(--primary-blue); overflow: hidden;">
                    <img src="uploads/<?php echo htmlspecialchars($event['featured_image']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" style="width: 100%; height: auto; display: block;">
                </div>
                <?php endif; ?>
                
                <h2 style="font-size: 1.75rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary-blue);">About This Event</h2>
                <div style="font-size: 1.05rem; line-height: 1.8; color: #333; margin-bottom: 3rem;">
                    <?php echo $event['description']; ?>
                </div>

                <!-- Additional Details -->
                <?php if (!empty($event['organizer'] ?? '') || !empty($event['contact_email'] ?? '') || !empty($event['contact_phone'] ?? '')): ?>
                <div style="border: 2px solid var(--primary-blue); padding: 2rem; background: #f8f9fa; margin-bottom: 2rem;">
                    <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary-blue);">Contact Information</h3>
                    <?php if (!empty($event['organizer'] ?? '')): ?>
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-blue);">Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($event['contact_email'] ?? '')): ?>
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-blue);">Email:</strong> 
                        <a href="mailto:<?php echo htmlspecialchars($event['contact_email']); ?>" style="color: var(--primary-blue);">
                            <?php echo htmlspecialchars($event['contact_email']); ?>
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($event['contact_phone'] ?? '')): ?>
                    <div>
                        <strong style="color: var(--primary-blue);">Phone:</strong> <?php echo htmlspecialchars($event['contact_phone']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div style="position: sticky; top: 100px;">
                    <!-- Event Details Card -->
                    <div style="border: 2px solid var(--primary-blue); padding: 2rem; background: #fff; margin-bottom: 2rem;">
                        <h3 style="font-size: 1.4rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary-blue);">Event Details</h3>
                        
                        <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                            <div style="display: flex; gap: 1rem;">
                                <div style="flex-shrink: 0;">
                                    <i class="far fa-calendar" style="color: var(--primary-green); font-size: 1.5rem; width: 30px;"></i>
                                </div>
                                <div>
                                    <strong style="display: block; color: var(--primary-blue); margin-bottom: 0.25rem;">Date</strong>
                                    <span style="color: #666;"><?php echo date('l, F j, Y', strtotime($event['event_date'])); ?></span>
                                </div>
                            </div>

                            <div style="display: flex; gap: 1rem;">
                                <div style="flex-shrink: 0;">
                                    <i class="far fa-clock" style="color: var(--primary-green); font-size: 1.5rem; width: 30px;"></i>
                                </div>
                                <div>
                                    <strong style="display: block; color: var(--primary-blue); margin-bottom: 0.25rem;">Time</strong>
                                    <span style="color: #666;"><?php echo date('g:i A', strtotime($event['event_date'])); ?></span>
                                    <?php if ($event['end_date']): ?>
                                        <span style="color: #666;"> - <?php echo date('g:i A', strtotime($event['end_date'])); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="display: flex; gap: 1rem;">
                                <div style="flex-shrink: 0;">
                                    <i class="fas fa-map-marker-alt" style="color: var(--primary-green); font-size: 1.5rem; width: 30px;"></i>
                                </div>
                                <div>
                                    <strong style="display: block; color: var(--primary-blue); margin-bottom: 0.25rem;">Location</strong>
                                    <span style="color: #666;"><?php echo htmlspecialchars($event['location'] ?? 'TBA'); ?></span>
                                </div>
                            </div>

                            <?php if ($event['registration_required'] && !$isPast): ?>
                            <div style="display: flex; gap: 1rem;">
                                <div style="flex-shrink: 0;">
                                    <i class="fas fa-users" style="color: var(--primary-green); font-size: 1.5rem; width: 30px;"></i>
                                </div>
                                <div>
                                    <strong style="display: block; color: var(--primary-blue); margin-bottom: 0.25rem;">Capacity</strong>
                                    <?php if ($spotsRemaining !== null): ?>
                                        <span style="color: #666;"><?php echo $spotsRemaining; ?> spots remaining</span>
                                    <?php else: ?>
                                        <span style="color: #666;">Registration required</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!$isPast && $event['registration_required']): ?>
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e9ecef;">
                            <?php if ($spotsRemaining !== null && $spotsRemaining <= 0): ?>
                                <div style="text-align: center; padding: 1rem; background: #f8d7da; color: #721c24; border: 2px solid #f5c6cb;">
                                    <strong>Event Full</strong>
                                </div>
                            <?php else: ?>
                                <a href="contact.php?subject=Event Registration: <?php echo urlencode($event['title']); ?>" class="btn" style="width: 100%; border: 2px solid var(--primary-blue); background: var(--primary-yellow); color: var(--white); padding: 1rem; font-weight: 700; text-align: center; text-decoration: none; display: block; font-size: 1.1rem;">
                                    <i class="fas fa-check-circle me-2"></i> Register Now
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Share Card -->
                    <div style="border: 2px solid var(--primary-blue); padding: 2rem; background: #f8f9fa;">
                        <h4 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.25rem; color: var(--primary-black);">Share Event</h4>
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" style="flex: 1; min-width: 45%; display: inline-flex; align-items: center; justify-content: center; height: 45px; border: 2px solid var(--primary-blue); background: #fff; color: var(--primary-black); text-decoration: none; font-weight: 600;">
                                <i class="fab fa-facebook-f me-2"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($event['title']); ?>" target="_blank" style="flex: 1; min-width: 45%; display: inline-flex; align-items: center; justify-content: center; height: 45px; border: 2px solid var(--primary-blue); background: #fff; color: var(--primary-black); text-decoration: none; font-weight: 600;">
                                <i class="fab fa-twitter me-2"></i> Twitter
                            </a>
                            <a href="whatsapp://send?text=<?php echo urlencode($event['title'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" style="flex: 1; min-width: 45%; display: inline-flex; align-items: center; justify-content: center; height: 45px; border: 2px solid var(--primary-blue); background: #fff; color: var(--primary-black); text-decoration: none; font-weight: 600;">
                                <i class="fab fa-whatsapp me-2"></i> WhatsApp
                            </a>
                            <button id="ev-copy-btn" onclick="copyEventLink()" style="flex: 1; min-width: 45%; display: inline-flex; align-items: center; justify-content: center; height: 45px; border: 2px solid var(--primary-blue); background: #fff; color: var(--primary-black); font-weight: 600; cursor: pointer;">
                                <i class="fas fa-link me-2" id="ev-copy-icon"></i> <span id="ev-copy-label">Copy Link</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section style="padding: 80px 0; background: var(--primary-yellow);">
    <div class="container text-center">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: var(--primary-black);">Get Involved</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; color: var(--primary-black);">
            Explore more events and school activities at Lukman Primary School
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="events.php" class="btn" style="border: 2px solid var(--primary-blue); background: #fff; color: var(--primary-black); padding: 0.75rem 2rem; font-weight: 600; text-decoration: none;">
                <i class="far fa-calendar me-2"></i> View More Events
            </a>
            <a href="get-involved.php" class="btn" style="border: 2px solid var(--primary-blue); background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)); color: #fff; padding: 0.75rem 2rem; font-weight: 600; text-decoration: none;">
                <i class="fas fa-user-plus me-2"></i> Apply for Admission
            </a>
        </div>
    </div>
</section>

<script>
function copyEventLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        document.getElementById('ev-copy-icon').className = 'fas fa-check me-2';
        document.getElementById('ev-copy-label').textContent = 'Copied!';
        setTimeout(function() {
            document.getElementById('ev-copy-icon').className = 'fas fa-link me-2';
            document.getElementById('ev-copy-label').textContent = 'Copy Link';
        }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>

<?php
$currentPage = 'student-life';
$pageTitle   = 'Student Life';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageDescription = 'Discover life at ' . $siteName . ' — boarding routine, clubs, sports, Islamic worship schedule, co-curricular activities, and what makes our school community special.';

// Upcoming calendar events
$calendarEvents = [];
try {
    $stmt = $pdo->prepare("SELECT * FROM school_calendar WHERE status = 'active' AND start_date >= CURDATE() ORDER BY start_date ASC LIMIT 6");
    $stmt->execute();
    $calendarEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {}

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Student Life</h1>
        <p>A vibrant, faith-filled community where pupils grow and thrive</p>
    </div>
</div>

<main id="main-content">

    <!-- Daily Routine Banner -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Our Community</span>
                <h2 class="section-title">Life at <?php echo htmlspecialchars($siteShortName); ?></h2>
                <p class="section-subtitle">From morning prayers to evening prep, every moment at <?php echo htmlspecialchars($siteShortName); ?> is purposeful</p>
            </div>
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <p>At <?php echo htmlspecialchars($siteName); ?>, pupil life is structured to balance academic work, Islamic practice, physical activity, and social development. Our boarders follow a carefully designed daily routine that ensures maximum learning time while preserving time for worship, rest, and fun.</p>
                    <p>Day scholars follow the same academic timetable and are welcome to participate in morning assembly, prayer times, and after-school activities before travelling home.</p>
                    <a href="admissions.php" class="btn-primary-custom mt-2">
                        <i class="fas fa-graduation-cap me-2"></i>Join Our School
                    </a>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <!-- Daily Routine Timeline -->
                    <div class="routine-timeline">
                        <div class="routine-item">
                            <div class="routine-time">5:00 AM</div>
                            <div class="routine-event"><i class="fas fa-mosque"></i> Fajr Prayer &amp; Quran recitation</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">6:30 AM</div>
                            <div class="routine-event"><i class="fas fa-shower"></i> Morning hygiene &amp; breakfast</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">7:30 AM</div>
                            <div class="routine-event"><i class="fas fa-flag"></i> Morning assembly (flag raising, national anthem)</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">8:00 AM</div>
                            <div class="routine-event"><i class="fas fa-book-open"></i> Class lessons begin</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">12:00 PM</div>
                            <div class="routine-event"><i class="fas fa-mosque"></i> Dhuhr prayer &amp; lunch break</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">1:15 PM</div>
                            <div class="routine-event"><i class="fas fa-book"></i> Afternoon lessons (including Islamic Studies)</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">4:00 PM</div>
                            <div class="routine-event"><i class="fas fa-futbol"></i> Sports &amp; co-curricular activities</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">6:00 PM</div>
                            <div class="routine-event"><i class="fas fa-mosque"></i> Maghrib prayer &amp; dinner</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">7:00 PM</div>
                            <div class="routine-event"><i class="fas fa-pencil-alt"></i> Evening prep (supervised study)</div>
                        </div>
                        <div class="routine-item">
                            <div class="routine-time">8:30 PM</div>
                            <div class="routine-event"><i class="fas fa-moon"></i> Isha prayer &amp; lights out (dormitory)</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Sports & Clubs -->
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Beyond the Classroom</span>
                <h2 class="section-title">Sports &amp; Clubs</h2>
                <p class="section-subtitle">Every pupil is encouraged to explore talents outside the classroom</p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-futbol fa-2x" style="color:var(--primary)"></i>
                            <h5>Football</h5>
                        </div>
                        <p>Two school football teams — boys and girls — compete in district and regional inter-school tournaments. Trained by a qualified PE teacher every week.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="150">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-basketball-ball fa-2x" style="color:var(--primary)"></i>
                            <h5>Netball</h5>
                        </div>
                        <p>Our girls' netball team participates in Entebbe district competitions. Netball develops teamwork, coordination and discipline.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="200">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-running fa-2x" style="color:var(--primary)"></i>
                            <h5>Athletics</h5>
                        </div>
                        <p>Track and field events including sprints, long jump, and cross-country. Pupils represent the school at district athletics meets each term.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="250">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-microphone fa-2x" style="color:var(--secondary)"></i>
                            <h5>Debate Club</h5>
                        </div>
                        <p>English and Luganda debating competitions develop critical thinking, confidence and public speaking from P4 onwards.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="300">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-palette fa-2x" style="color:var(--secondary)"></i>
                            <h5>Arts &amp; Drama</h5>
                        </div>
                        <p>School plays, cultural dances, art exhibition, and choir performances at end-of-year graduation ceremonies and community events.</p>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="350">
                    <div class="activity-detail-card">
                        <div class="activity-header">
                            <i class="fas fa-seedling fa-2x" style="color:var(--secondary)"></i>
                            <h5>Environmental Club</h5>
                        </div>
                        <p>Tree planting, gardening, and environmental awareness campaigns. Pupils learn responsibility for their school and community.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Boarding Life -->
    <section class="section-padding bg-white">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="section-badge">Boarding Welfare</span>
                    <h2 class="section-title-left">Safe &amp; Caring Boarding</h2>
                    <p>Our boarding facility is designed to feel like a second home. Matrons and patrons supervise the dormitories around the clock and provide pastoral care for every boarder.</p>
                    <div class="row g-3 mt-2">
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-bed text-success"></i>
                                <span>Separate dorms for boys &amp; girls</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-utensils text-success"></i>
                                <span>3 nutritious meals daily</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-first-aid text-success"></i>
                                <span>On-call nurse &amp; first aid</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-shield-alt text-success"></i>
                                <span>24-hour security</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-phone text-success"></i>
                                <span>Parent communication updates</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="welfare-item">
                                <i class="fas fa-calendar text-success"></i>
                                <span>Scheduled exeat weekends</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <div class="info-highlight-box">
                        <h4><i class="fas fa-hands-helping me-2 text-success"></i>Student Government</h4>
                        <p>Every term, pupils elect a School Prefect body — Head Prefect, Deputy, and class representatives. The prefect body helps maintain school culture, organise events, and represent pupil interests to teachers.</p>
                        <hr>
                        <h4><i class="fas fa-award me-2 text-warning"></i>Awards &amp; Recognition</h4>
                        <p>Outstanding academic performance, Islamic conduct, sportsmanship, and community service are recognised at our end-of-year graduation ceremony open to all parents.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- School Calendar -->
    <?php if (!empty($calendarEvents)): ?>
    <section class="section-padding bg-light-custom">
        <div class="container">
            <div class="text-center mb-5" data-aos="fade-up">
                <span class="section-badge">Upcoming Dates</span>
                <h2 class="section-title">School Calendar Highlights</h2>
            </div>
            <div class="row g-3">
                <?php foreach ($calendarEvents as $ev): ?>
                <div class="col-md-6 col-lg-4" data-aos="fade-up">
                    <div class="calendar-event-card">
                        <div class="cal-date">
                            <span class="cal-day"><?php echo date('d', strtotime($ev['start_date'])); ?></span>
                            <span class="cal-month"><?php echo date('M', strtotime($ev['start_date'])); ?></span>
                        </div>
                        <div class="cal-info">
                            <h6><?php echo htmlspecialchars($ev['title']); ?></h6>
                            <?php if ($ev['term']): ?><span class="badge-term">Term <?php echo $ev['term']; ?></span><?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-4">
                <a href="events.php" class="btn-secondary-custom"><i class="fas fa-calendar me-2"></i>Full Events Calendar</a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA -->
    <section class="cta-section">
        <div class="container text-center" data-aos="zoom-in">
            <h2>Ready to join our community?</h2>
            <p>Applications for boarding and day places are open. Come visit and see school life for yourself.</p>
            <div class="cta-buttons">
                <a href="admissions.php" class="btn-cta-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Apply Now
                </a>
                <a href="contact.php" class="btn-cta-secondary">
                    <i class="fas fa-phone me-2"></i>Book a School Visit
                </a>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

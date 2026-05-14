<?php
$currentPage = 'prayer-times';
include 'config.php';
include 'functions.php';

$siteName      = getSetting('site_name', 'Lukman Primary School');
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$pageTitle      = 'Prayer Times — ' . $siteShortName;
$pageDescription = 'Daily Salah prayer times for ' . $siteName . ' in Entebbe, Uganda. Stay on time with Fajr, Dhuhr, Asr, Maghrib, and Isha.';

// ── Monthly prayer times lookup for Entebbe, Uganda (0.05°N, 32.47°E, UTC+3)
// Calculated using Muslim World League method. Format: [Fajr, Dhuhr, Asr, Maghrib, Isha]
$prayerTable = [
    1  => ['05:17','12:51','16:04','19:09','20:20'], // January
    2  => ['05:10','12:47','16:00','19:08','20:19'], // February
    3  => ['05:02','12:43','15:56','19:09','20:20'], // March
    4  => ['04:58','12:43','15:56','19:12','20:24'], // April
    5  => ['04:59','12:47','16:00','19:18','20:30'], // May
    6  => ['05:03','12:52','16:05','19:24','20:36'], // June
    7  => ['05:04','12:54','16:07','19:27','20:39'], // July
    8  => ['04:59','12:49','16:01','19:22','20:33'], // August
    9  => ['04:50','12:40','15:52','19:14','20:25'], // September
    10 => ['04:48','12:36','15:48','19:09','20:20'], // October
    11 => ['04:53','12:40','15:53','19:11','20:22'], // November
    12 => ['05:04','12:49','16:02','19:17','20:28'], // December
];

$prayers = [
    ['key' => 'fajr',    'en' => 'Fajr',    'ar' => 'الفجر',    'idx' => 0, 'icon' => 'fas fa-moon',         'desc' => 'Pre-dawn',   'color' => '#1a237e', 'bg' => '#e8eaf6'],
    ['key' => 'dhuhr',   'en' => 'Dhuhr',   'ar' => 'الظهر',    'idx' => 1, 'icon' => 'fas fa-sun',          'desc' => 'Midday',     'color' => '#e65100', 'bg' => '#fff3e0'],
    ['key' => 'asr',     'en' => 'Asr',     'ar' => 'العصر',    'idx' => 2, 'icon' => 'fas fa-cloud-sun',    'desc' => 'Afternoon',  'color' => '#1565c0', 'bg' => '#e3f2fd'],
    ['key' => 'maghrib', 'en' => 'Maghrib', 'ar' => 'المغرب',   'idx' => 3, 'icon' => 'fas fa-sunset',       'desc' => 'Sunset',     'color' => '#6a1b9a', 'bg' => '#f3e5f5'],
    ['key' => 'isha',    'en' => 'Isha',    'ar' => 'العشاء',   'idx' => 4, 'icon' => 'fas fa-star-and-crescent', 'desc' => 'Night', 'color' => '#00723F', 'bg' => '#e8f5e9'],
];

$currentMonth     = (int)date('n');
$currentMonthName = date('F');
$todayTimes       = $prayerTable[$currentMonth];
$currentTimeStr   = date('H:i');

// Determine active (current/next) prayer
function prayerStatus(string $timeStr, string $now, string $nextStr): string {
    if ($now >= $timeStr && ($nextStr === '' || $now < $nextStr)) return 'current';
    if ($now < $timeStr) return 'upcoming';
    return 'passed';
}

$statuses = [];
for ($i = 0; $i < 5; $i++) {
    $next = $i < 4 ? $todayTimes[$i + 1] : '';
    $statuses[$i] = prayerStatus($todayTimes[$i], $currentTimeStr, $next);
}

// Find the next prayer
$nextPrayerIdx = null;
foreach ($statuses as $i => $s) {
    if ($s === 'upcoming') { $nextPrayerIdx = $i; break; }
}

$hijriDisplayLine = getHijriDateToday() . ' · ' . date('l, d F Y');

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Prayer Times']]);
?>

<!-- ══════════════ HERO BANNER ══════════════ -->
<section style="background: linear-gradient(135deg, #0d1b2a 0%, #0d2e1a 40%, #00723F 100%); padding: 70px 0 50px; position:relative; overflow:hidden;">
    <!-- decorative geometric pattern -->
    <div aria-hidden="true" style="position:absolute;inset:0;opacity:.06;background-image:url('data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%2280%22 height=%2280%22><polygon points=%2240,5 75,28 75,62 40,75 5,62 5,28%22 fill=%22none%22 stroke=%22%23fff%22 stroke-width=%221.5%22/></svg>');background-size:80px 80px;"></div>
    <div class="container text-center text-white position-relative">
        <div style="font-size:3rem; margin-bottom:.5rem;" aria-hidden="true">☽</div>
        <h1 class="display-5 fw-bold mb-2" style="letter-spacing:.03em;">مواقيت الصلاة</h1>
        <p class="lead mb-1" style="opacity:.85;">Daily Prayer Times — Entebbe, Uganda</p>
        <p style="opacity:.6; font-size:.9rem;">Calculated for Lukman Primary School (0.05° N, 32.47° E) · Muslim World League method</p>

        <!-- Live clock -->
        <div id="live-clock" style="font-size:2.5rem; font-weight:700; letter-spacing:.08em; margin:1.5rem auto .5rem; font-variant-numeric:tabular-nums;"></div>
        <div id="hijri-display" style="font-size:1rem; opacity:.7; margin-bottom:.5rem;"></div>
        <div id="next-prayer-line" style="font-size:1rem; opacity:.9; margin-bottom:0;"></div>
    </div>
</section>

<!-- ══════════════ TODAY'S PRAYER CARDS ══════════════ -->
<section style="background:#f8f9fa; padding:50px 0;">
    <div class="container">
        <h2 class="text-center fw-bold mb-4" style="color:#0d2e1a;">
            Today's Salah Times
            <span style="display:block; font-size:1rem; font-weight:400; color:#6c757d; margin-top:.3rem;"><?php echo $currentMonthName . ' — ' . $currentMonth; ?> month schedule</span>
        </h2>

        <div class="row g-3 justify-content-center">
        <?php foreach ($prayers as $p):
            $t      = $todayTimes[$p['idx']];
            $status = $statuses[$p['idx']];
            $isNow  = $status === 'current';
            $isNext = !$isNow && $p['idx'] === $nextPrayerIdx;
        ?>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="text-center rounded-3 p-3 h-100 position-relative shadow-sm"
                 style="background:<?php echo $isNow ? $p['color'] : ($status === 'passed' ? '#fff' : $p['bg']); ?>;
                        border: 2px solid <?php echo $isNow ? $p['color'] : ($isNext ? $p['color'] : 'transparent'); ?>;
                        color:<?php echo $isNow ? '#fff' : '#212529'; ?>;
                        transition: all .2s;
                        <?php echo $status === 'passed' ? 'opacity:.5;' : ''; ?>">
                <?php if ($isNow): ?>
                <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#EA1B27;color:#000;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap;">NOW</div>
                <?php elseif ($isNext): ?>
                <div style="position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:<?php echo $p['color']; ?>;color:#fff;font-size:.65rem;font-weight:700;padding:2px 8px;border-radius:20px;white-space:nowrap;">NEXT</div>
                <?php endif; ?>
                <i class="<?php echo $p['icon']; ?> fa-lg mb-2" style="color:<?php echo $isNow ? 'rgba(255,255,255,.85)' : $p['color']; ?>"></i>
                <div lang="ar" style="font-size:1.5rem; font-family:serif; font-weight:600; margin-bottom:.1rem; direction:rtl;"><?php echo $p['ar']; ?></div>
                <div style="font-size:.85rem; font-weight:600; margin-bottom:.3rem;"><?php echo $p['en']; ?></div>
                <div style="font-size:1.6rem; font-weight:700; font-variant-numeric:tabular-nums;"><?php echo $t; ?></div>
                <div style="font-size:.75rem; opacity:.65;"><?php echo $p['desc']; ?></div>
                <?php if ($isNow): ?>
                <div id="elapsed-<?php echo $p['idx']; ?>" style="font-size:.7rem; margin-top:.3rem; opacity:.85;"></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>

        <!-- Jumu'ah note -->
        <div class="text-center mt-4">
            <div class="d-inline-flex align-items-center gap-2 rounded-pill px-4 py-2 shadow-sm"
                 style="background:#fff; border:1.5px solid #00723F; color:#00723F; font-size:.9rem;">
                <i class="fas fa-mosque"></i>
                <strong>Jumu'ah (Friday Prayer)</strong> at school: 12:50 PM · Masjid Al-Noor
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ FULL YEAR TABLE ══════════════ -->
<section style="padding:50px 0; background:#fff;">
    <div class="container">
        <h2 class="text-center fw-bold mb-2" style="color:#0d2e1a;">Annual Prayer Timetable</h2>
        <p class="text-center text-muted mb-4" style="font-size:.9rem;">Monthly approximate times for Entebbe, Uganda. Times shown in 24-hour (EAT, UTC+3) format.</p>
        <div class="table-responsive shadow-sm rounded-3 overflow-hidden">
        <table class="table table-bordered table-hover mb-0" style="font-size:.9rem; text-align:center;">
            <thead style="background:#0d2e1a; color:#fff;">
                <tr>
                    <th class="text-start ps-3">Month</th>
                    <th><i class="fas fa-moon me-1"></i>Fajr</th>
                    <th><i class="fas fa-sun me-1"></i>Dhuhr</th>
                    <th><i class="fas fa-cloud-sun me-1"></i>Asr</th>
                    <th><i class="fas fa-sunset me-1" style="color:#EA1B27"></i>Maghrib</th>
                    <th><i class="fas fa-star me-1"></i>Isha</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $monthNames = ['','January','February','March','April','May','June','July','August','September','October','November','December'];
            foreach ($prayerTable as $m => $times):
                $isCurrentMonth = $m === $currentMonth;
            ?>
            <tr style="<?php echo $isCurrentMonth ? 'background:#e8f5e9; font-weight:600;' : ''; ?>">
                <td class="text-start ps-3">
                    <?php echo $monthNames[$m]; ?>
                    <?php if ($isCurrentMonth): ?><span class="badge ms-1" style="background:#00723F; font-size:.65rem;">Current</span><?php endif; ?>
                </td>
                <?php foreach ($times as $t): ?>
                <td><?php echo $t; ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <p class="text-muted text-center mt-2" style="font-size:.78rem;">
            <i class="fas fa-info-circle me-1"></i>
            Times are approximate. Verify with your local mosque or a trusted prayer time application. School observes prayer breaks at Dhuhr and Asr daily.
        </p>
    </div>
</section>

<!-- ══════════════ SCHOOL PRAYER ROUTINE ══════════════ -->
<section style="padding:50px 0; background:#f8f9fa;">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-lg-8">
                <h2 class="fw-bold mb-4 text-center" style="color:#0d2e1a;">Prayer at Lukman PS</h2>
                <div class="row g-3">
                    <?php
                    $routine = [
                        ['time' => 'Fajr', 'detail' => 'Boarding pupils pray in congregation at the school masjid before the morning wash bell.', 'icon' => 'fas fa-moon', 'col' => '#1a237e'],
                        ['time' => 'Dhuhr Break', 'detail' => 'Entire school — boarders and day pupils — observes a 20-minute Dhuhr break. Classes resume at 1:10 PM.', 'icon' => 'fas fa-sun', 'col' => '#e65100'],
                        ['time' => 'Asr', 'detail' => 'Boarding pupils return to the masjid after afternoon prep for Asr in congregation.', 'icon' => 'fas fa-cloud-sun', 'col' => '#1565c0'],
                        ['time' => 'Maghrib & Isha', 'detail' => 'Evening prayers are observed by boarding pupils before supper and at the close of evening prep respectively.', 'icon' => 'fas fa-star-and-crescent', 'col' => '#00723F'],
                    ];
                    foreach ($routine as $r): ?>
                    <div class="col-md-6">
                        <div class="d-flex gap-3 p-3 rounded-3 bg-white shadow-sm h-100">
                            <div style="width:40px; height:40px; background:<?php echo $r['col']; ?>; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <i class="<?php echo $r['icon']; ?> text-white" style="font-size:.9rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold mb-1" style="color:<?php echo $r['col']; ?>;"><?php echo $r['time']; ?></div>
                                <div style="font-size:.875rem; color:#555; line-height:1.6;"><?php echo $r['detail']; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ══════════════ CTA ══════════════ -->
<section id="cta" class="section-pad-sm">
    <div class="container text-center">
        <h2 class="section-heading" style="color:var(--white);">Nurturing Faith &amp; Knowledge Together</h2>
        <p style="color:rgba(255,255,255,0.9); margin-bottom:2rem;">Discover how Lukman PS integrates Islamic values into every aspect of school life.</p>
        <a href="quran.php" class="btn-green-custom" style="padding:.8rem 2rem;"><i class="fas fa-book-open me-2"></i>Hifdh Wall of Fame</a>
        <a href="admissions.php" style="display:inline-block;background:transparent;color:var(--white);border:2px solid var(--white);padding:.8rem 2rem;font-weight:600;border-radius:6px;text-decoration:none;margin-left:.5rem;"><i class="fas fa-graduation-cap me-2"></i>Apply for Admission</a>
    </div>
</section>

<!-- JS: live clock, countdown, elapsed time -->
<script>
(function() {
    // Prayer data for JS countdown
    var prayerTimes = <?php echo json_encode(array_values($todayTimes)); ?>;
    var prayerNames = ['Fajr','Dhuhr','Asr','Maghrib','Isha'];

    function pad(n){ return String(n).padStart(2,'0'); }

    function parseHHMM(s) {
        var p = s.split(':');
        return parseInt(p[0]) * 60 + parseInt(p[1]);
    }

    function tick() {
        var now = new Date();
        var h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        document.getElementById('live-clock').textContent = pad(h) + ':' + pad(m) + ':' + pad(s);

        var nowMin = h * 60 + m;

        // Next prayer countdown
        var nextIdx = -1;
        for (var i = 0; i < prayerTimes.length; i++) {
            if (nowMin < parseHHMM(prayerTimes[i])) { nextIdx = i; break; }
        }

        if (nextIdx >= 0) {
            var diff = parseHHMM(prayerTimes[nextIdx]) - nowMin;
            var dh = Math.floor(diff / 60), dm = diff % 60;
            document.getElementById('next-prayer-line').textContent =
                prayerNames[nextIdx] + ' in ' + (dh > 0 ? dh + 'h ' : '') + dm + 'm';
        } else {
            document.getElementById('next-prayer-line').textContent = 'Isha passed · Fajr tomorrow at ' + prayerTimes[0];
        }

        // Elapsed in current prayer
        for (var j = 0; j < prayerTimes.length; j++) {
            var el = document.getElementById('elapsed-' + j);
            if (el) {
                var since = nowMin - parseHHMM(prayerTimes[j]);
                el.textContent = since + ' min ago';
            }
        }
    }

    tick();
    setInterval(tick, 1000);

    // Hijri date from shared PHP helper
    document.getElementById('hijri-display').textContent = <?php echo json_encode($hijriDisplayLine); ?>;
})();
</script>

<?php include 'includes/footer.php'; ?>

<?php
$currentPage = 'search';
include 'config.php';
include 'functions.php';

$siteShortName = getSetting('site_short_name', 'Lukman PS');
$siteName = getSetting('site_name', 'Lukman Primary School');

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$pageTitle = $q ? 'Search: ' . htmlspecialchars($q) . ' — ' . $siteShortName : 'Search — ' . $siteShortName;
$pageDescription = 'Search results for ' . $siteName;

$results = [];
$totalCount = 0;

if ($q !== '' && strlen($q) >= 2) {
    $like = '%' . $q . '%';

    // News
    $stmt = $pdo->prepare("SELECT id, title, excerpt, slug, featured_image, published_at, category,
                            'news' AS result_type FROM news_posts
                           WHERE status = 'published'
                             AND (title LIKE ? OR excerpt LIKE ? OR content LIKE ?)
                           ORDER BY published_at DESC LIMIT 20");
    $stmt->execute([$like, $like, $like]);
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Events
    $stmt = $pdo->prepare("SELECT id, title, description AS excerpt, slug, image AS featured_image,
                            start_date AS published_at, category,
                            'event' AS result_type FROM events
                           WHERE status = 'published'
                             AND (title LIKE ? OR description LIKE ?)
                           ORDER BY start_date DESC LIMIT 20");
    $stmt->execute([$like, $like]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // FAQs
    $stmt = $pdo->prepare("SELECT id, question AS title, answer AS excerpt, NULL AS slug,
                            NULL AS featured_image, created_at AS published_at, category,
                            'faq' AS result_type FROM faq_items
                           WHERE status = 'active'
                             AND (question LIKE ? OR answer LIKE ?)
                           ORDER BY sort_order ASC LIMIT 20");
    $stmt->execute([$like, $like]);
    $faqs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $results = array_merge($news, $events, $faqs);
    $totalCount = count($results);
}

function highlightKeyword(string $text, string $keyword): string {
    if ($keyword === '') return htmlspecialchars($text);
    $escaped = htmlspecialchars($keyword);
    $safe    = htmlspecialchars($text);
    return preg_replace('/(' . preg_quote($escaped, '/') . ')/iu', '<mark>$1</mark>', $safe);
}

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => $q ? 'Search: ' . mb_strimwidth($q, 0, 40, '…') : 'Search']]);
?>

<!-- Page Banner -->
<section class="page-banner" style="background: linear-gradient(135deg, var(--primary) 0%, #005a30 100%); padding: 60px 0;">
    <div class="container text-center text-white">
        <h1 class="display-5 fw-bold mb-2"><i class="fas fa-search me-2"></i>Site Search</h1>
        <p class="lead mb-0">Search news, events, and FAQs</p>
    </div>
</section>

<!-- Search Form -->
<section class="py-4 bg-light border-bottom">
    <div class="container">
        <form method="GET" action="search.php" class="row g-2 justify-content-center">
            <div class="col-12 col-md-7">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="q" class="form-control border-start-0 ps-0"
                           placeholder="Search the site…"
                           value="<?php echo htmlspecialchars($q); ?>"
                           autofocus minlength="2" required>
                    <button class="btn btn-primary px-4" type="submit" style="background:var(--primary);border-color:var(--primary);">Search</button>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Results -->
<section class="py-5">
    <div class="container">

        <?php if ($q === ''): ?>
        <!-- Initial state -->
        <div class="text-center py-5 text-muted">
            <i class="fas fa-search fa-3x mb-3 opacity-25"></i>
            <p class="fs-5">Enter a keyword above to search news, events, and FAQs.</p>
        </div>

        <?php elseif (strlen($q) < 2): ?>
        <div class="alert alert-warning text-center">Please enter at least 2 characters.</div>

        <?php elseif ($totalCount === 0): ?>
        <!-- No results -->
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-3x mb-3 text-muted opacity-50"></i>
            <h4 class="text-muted">No results found for "<strong><?php echo htmlspecialchars($q); ?></strong>"</h4>
            <p class="text-muted">Try different keywords or browse the site using the menu above.</p>
        </div>

        <?php else: ?>
        <!-- Results header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h5 class="mb-0 text-muted">
                Found <strong class="text-dark"><?php echo $totalCount; ?></strong>
                result<?php echo $totalCount !== 1 ? 's' : ''; ?>
                for "<strong class="text-dark"><?php echo htmlspecialchars($q); ?></strong>"
            </h5>
        </div>

        <!-- Group by type -->
        <?php
        $groups = [
            'news'  => ['label' => 'News Articles', 'icon' => 'newspaper', 'color' => 'primary', 'items' => []],
            'event' => ['label' => 'Events',         'icon' => 'calendar-alt', 'color' => 'warning', 'items' => []],
            'faq'   => ['label' => 'FAQs',           'icon' => 'question-circle', 'color' => 'info', 'items' => []],
        ];
        foreach ($results as $r) {
            $groups[$r['result_type']]['items'][] = $r;
        }
        ?>

        <?php foreach ($groups as $type => $group): ?>
        <?php if (empty($group['items'])) continue; ?>
        <div class="mb-5">
            <h5 class="fw-bold mb-3 border-bottom pb-2">
                <i class="fas fa-<?php echo $group['icon']; ?> text-<?php echo $group['color']; ?> me-2"></i>
                <?php echo $group['label']; ?>
                <span class="badge bg-<?php echo $group['color']; ?> ms-2 fs-6"><?php echo count($group['items']); ?></span>
            </h5>

            <div class="row g-3">
            <?php foreach ($group['items'] as $item): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm h-100">
                    <div class="row g-0 align-items-center">
                        <?php if (!empty($item['featured_image'])): ?>
                        <div class="col-auto d-none d-md-block">
                            <img src="uploads/<?php echo htmlspecialchars($item['featured_image']); ?>"
                                 alt="" class="rounded-start"
                                 style="width:100px;height:80px;object-fit:cover;">
                        </div>
                        <?php endif; ?>
                        <div class="col">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-start gap-2 mb-1">
                                    <span class="badge bg-<?php echo $group['color']; ?> text-<?php echo $group['color'] === 'warning' ? 'dark' : 'white'; ?> flex-shrink-0">
                                        <?php echo rtrim($group['label'], 's'); ?>
                                    </span>
                                    <?php if (!empty($item['category'])): ?>
                                    <span class="badge bg-light text-muted border"><?php echo htmlspecialchars($item['category']); ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($type === 'news'): ?>
                                <h6 class="card-title mb-1">
                                    <a href="news-detail.php?id=<?php echo $item['id']; ?>"
                                       class="text-decoration-none text-dark stretched-link">
                                        <?php echo highlightKeyword($item['title'], $q); ?>
                                    </a>
                                </h6>
                                <?php elseif ($type === 'event'): ?>
                                <h6 class="card-title mb-1">
                                    <a href="event-detail.php?id=<?php echo $item['id']; ?>"
                                       class="text-decoration-none text-dark stretched-link">
                                        <?php echo highlightKeyword($item['title'], $q); ?>
                                    </a>
                                </h6>
                                <?php else: ?>
                                <h6 class="card-title mb-1"><?php echo highlightKeyword($item['title'], $q); ?></h6>
                                <?php endif; ?>

                                <?php if (!empty($item['excerpt'])): ?>
                                <p class="card-text small text-muted mb-1">
                                    <?php
                                    $excerpt = strip_tags($item['excerpt']);
                                    // Find keyword position for context snippet
                                    $pos = stripos($excerpt, $q);
                                    if ($pos !== false) {
                                        $start = max(0, $pos - 60);
                                        $snippet = ($start > 0 ? '…' : '') . substr($excerpt, $start, 200) . '…';
                                    } else {
                                        $snippet = substr($excerpt, 0, 200) . (strlen($excerpt) > 200 ? '…' : '');
                                    }
                                    echo highlightKeyword($snippet, $q);
                                    ?>
                                </p>
                                <?php endif; ?>

                                <?php if (!empty($item['published_at'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('d M Y', strtotime($item['published_at'])); ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>

    </div>
</section>

<style>
mark {
    background: rgba(218, 165, 32, 0.3);
    color: inherit;
    border-radius: 2px;
    padding: 0 2px;
}
</style>

<?php include 'includes/footer.php'; ?>

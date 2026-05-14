<?php 
$currentPage = 'gallery';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$siteName = getSetting('site_name', 'Lukman Primary School');

$pageTitle = 'Photo Gallery';
$pageDescription = 'View photos of school life, events, sports, and activities at ' . $siteName . '.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'Gallery']]);

// Category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Build query
$sql = "SELECT * FROM gallery_albums WHERE status = 'active'";
if ($category) {
    $sql .= " AND category = :category";
}
$sql .= " ORDER BY display_order ASC, created_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$albums = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count
$countSql = "SELECT COUNT(*) FROM gallery_albums WHERE status = 'active'";
if ($category) {
    $countSql .= " AND category = :category";
}
$countStmt = $pdo->prepare($countSql);
if ($category) {
    $countStmt->bindParam(':category', $category);
}
$countStmt->execute();
$totalAlbums = $countStmt->fetchColumn();
$totalPages = ceil($totalAlbums / $perPage);

// Get categories for filter
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM gallery_albums WHERE status = 'active' ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);

// Get image count for each album
foreach ($albums as &$album) {
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM gallery_images WHERE album_id = ? AND status = 'active'");
    $countStmt->execute([$album['id']]);
    $album['image_count'] = $countStmt->fetchColumn();
}
unset($album);

// Total images across all albums  
$totalImagesStmt = $pdo->query("SELECT COUNT(*) FROM gallery_images WHERE status = 'active'");
$totalImages = $totalImagesStmt->fetchColumn();
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>Photo Gallery</h1>
        <p>A glimpse into daily life, events and activities at <?php echo htmlspecialchars($siteName); ?></p>
    </div>
</div>

<!-- Gallery Content -->
<section class="gallery-home-section" style="padding: 60px 0 80px;">
    <div class="container">

        <!-- Toolbar: same as homepage gallery module -->
        <div class="gal-toolbar">
            <p class="gal-toolbar-note"><i class="fas fa-images"></i> <?php echo $totalAlbums; ?> Albums &middot; <?php echo $totalImages; ?> Photos</p>
            <div class="gal-filters" role="group" aria-label="Filter by category">
                <a href="gallery.php" class="btn gal-filter <?php echo !$category ? 'active' : ''; ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                <a href="gallery.php?category=<?php echo urlencode($cat); ?>" class="btn gal-filter <?php echo $category === $cat ? 'active' : ''; ?>"><?php echo htmlspecialchars($cat); ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (empty($albums)): ?>
            <div class="text-center" style="padding: 4rem 0;">
                <i class="far fa-images" style="font-size: 3.5rem; color: #dce7e2; margin-bottom: 1rem; display: block;"></i>
                <h3 style="color: var(--gray-text); font-weight: 600;">No albums found</h3>
                <p style="color: #999;">Check back soon for new photos!</p>
                <?php if ($category): ?>
                <a href="gallery.php" class="gal-cta mt-3"><i class="fas fa-th me-2"></i>View All Albums</a>
                <?php endif; ?>
            </div>
        <?php else: ?>

            <!-- Albums Grid: reuses homepage gal-card pattern -->
            <div class="row g-3 gal-grid">
                <?php 
                // Layout pattern: Row 1 = 5-4-3, Row 2 = 3-3-3-3, Row 3 = 3-5-4 — cycled
                $colPatterns = [
                    ['col-lg-5 col-md-6', 'col-lg-4 col-md-6', 'col-lg-3 col-md-6'],
                    ['col-lg-3 col-md-6', 'col-lg-3 col-md-6', 'col-lg-3 col-md-6', 'col-lg-3 col-md-6'],
                    ['col-lg-3 col-md-6', 'col-lg-5 col-md-6', 'col-lg-4 col-md-6'],
                ];
                $patternIdx = 0;
                $colIdx = 0;
                
                foreach ($albums as $album):
                    $coverImgPath = $album['cover_image'];
                    if ($coverImgPath && strpos($coverImgPath, '/') === false) {
                        $coverImgPath = 'gallery/' . $coverImgPath;
                    }
                    
                    $currentPattern = $colPatterns[$patternIdx % count($colPatterns)];
                    $colClass = $currentPattern[$colIdx % count($currentPattern)];
                    $isLarge = (strpos($colClass, 'col-lg-5') !== false || strpos($colClass, 'col-lg-4') !== false);
                    
                    $colIdx++;
                    if ($colIdx >= count($currentPattern)) {
                        $colIdx = 0;
                        $patternIdx++;
                    }
                ?>
                <div class="<?php echo $colClass; ?> gal-cell" data-category="<?php echo htmlspecialchars(strtolower($album['category'])); ?>">
                    <a href="gallery-album.php?id=<?php echo $album['id']; ?>" style="text-decoration: none; display: block;">
                        <div class="gal-card <?php echo $isLarge ? 'gal-card--lg' : ''; ?>">
                            <?php if ($coverImgPath): ?>
                            <img src="uploads/<?php echo htmlspecialchars($coverImgPath); ?>" alt="<?php echo htmlspecialchars($album['title']); ?>" loading="lazy">
                            <?php endif; ?>
                            <div class="gal-overlay">
                                <i class="fas fa-folder-open"></i>
                                <span><?php echo htmlspecialchars($album['title']); ?></span>
                                <span style="font-size: 0.7rem; font-weight: 400; opacity: 0.85;"><?php echo $album['image_count']; ?> photos</span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Footer: matches homepage pattern -->
            <div class="gal-footer">
                <span class="gal-count">
                    <i class="fas fa-camera me-2"></i>
                    Showing <?php echo count($albums); ?> of <?php echo $totalAlbums; ?> Albums
                    <?php if ($category): ?>
                    &middot; <a href="gallery.php" style="color: var(--primary); font-weight: 700; text-decoration: none;">Clear filter</a>
                    <?php endif; ?>
                </span>
                <?php if ($totalPages > 1): ?>
                <div class="gal-filters" role="navigation" aria-label="Gallery pages">
                    <?php if ($page > 1): ?>
                    <a href="gallery.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="btn gal-filter"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="gallery.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="btn gal-filter <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="gallery.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" class="btn gal-filter"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

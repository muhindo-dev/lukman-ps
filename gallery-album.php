<?php 
$currentPage = 'gallery';
$pageTitle = 'Album';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');

// Get album
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM gallery_albums WHERE id = ? AND status = 'active'");
$stmt->execute([$id]);
$album = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$album) {
    header('Location: gallery.php');
    exit;
}

$pageTitle = $album['title'] . ' - ' . $siteShortName;
$pageDescription = substr($album['description'], 0, 160);

// Get images for this album
$imagesStmt = $pdo->prepare("SELECT * FROM gallery_images WHERE album_id = ? AND status = 'active' ORDER BY display_order ASC, id ASC");
$imagesStmt->execute([$id]);
$images = $imagesStmt->fetchAll(PDO::FETCH_ASSOC);

// Fix image paths for display
foreach ($images as &$image) {
    if (strpos($image['image_path'], '/') === false) {
        $image['image_path'] = 'uploads/gallery/' . $image['image_path'];
    } elseif (strpos($image['image_path'], 'uploads/') !== 0) {
        $image['image_path'] = 'uploads/' . $image['image_path'];
    }
}
unset($image);

// Get next/prev albums for navigation
$prevStmt = $pdo->prepare("SELECT id, title FROM gallery_albums WHERE status = 'active' AND display_order < ? ORDER BY display_order DESC LIMIT 1");
$prevStmt->execute([$album['display_order']]);
$prevAlbum = $prevStmt->fetch(PDO::FETCH_ASSOC);

$nextStmt = $pdo->prepare("SELECT id, title FROM gallery_albums WHERE status = 'active' AND display_order > ? ORDER BY display_order ASC LIMIT 1");
$nextStmt->execute([$album['display_order']]);
$nextAlbum = $nextStmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([
    ['label' => 'Gallery', 'url' => 'gallery.php'],
    ['label' => mb_strimwidth($album['title'], 0, 60, '…')],
]);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1><?php echo htmlspecialchars($album['title']); ?></h1>
        <p><?php echo htmlspecialchars($album['description']); ?></p>
    </div>
</div>

<!-- Album Photos -->
<section class="gallery-home-section" style="padding: 60px 0 80px;">
    <div class="container">

        <!-- Toolbar -->
        <div class="gal-toolbar">
            <p class="gal-toolbar-note"><i class="fas fa-images"></i> <?php echo count($images); ?> Photos &middot; <?php echo htmlspecialchars($album['category']); ?></p>
            <div class="gal-filters">
                <a href="gallery.php" class="btn gal-filter"><i class="fas fa-arrow-left me-1"></i> All Albums</a>
                <?php if ($prevAlbum): ?>
                <a href="gallery-album.php?id=<?php echo $prevAlbum['id']; ?>" class="btn gal-filter" title="<?php echo htmlspecialchars($prevAlbum['title']); ?>"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($nextAlbum): ?>
                <a href="gallery-album.php?id=<?php echo $nextAlbum['id']; ?>" class="btn gal-filter" title="<?php echo htmlspecialchars($nextAlbum['title']); ?>"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($images)): ?>
            <div class="text-center" style="padding: 4rem 0;">
                <i class="far fa-images" style="font-size: 3.5rem; color: #dce7e2; margin-bottom: 1rem; display: block;"></i>
                <h3 style="color: var(--gray-text); font-weight: 600;">No photos in this album yet</h3>
                <p style="color: #999;">Check back soon for updates!</p>
            </div>
        <?php else: ?>

            <!-- Photos Grid: reuses homepage gal-card pattern -->
            <div class="row g-3 gal-grid">
                <?php 
                $colPatterns = [
                    ['col-lg-5 col-md-6', 'col-lg-4 col-md-6', 'col-lg-3 col-md-6'],
                    ['col-lg-3 col-md-6', 'col-lg-3 col-md-6', 'col-lg-3 col-md-6', 'col-lg-3 col-md-6'],
                    ['col-lg-3 col-md-6', 'col-lg-5 col-md-6', 'col-lg-4 col-md-6'],
                ];
                $patternIdx = 0;
                $colIdx = 0;

                foreach ($images as $index => $image):
                    $currentPattern = $colPatterns[$patternIdx % count($colPatterns)];
                    $colClass = $currentPattern[$colIdx % count($currentPattern)];
                    $isLarge = (strpos($colClass, 'col-lg-5') !== false);

                    $colIdx++;
                    if ($colIdx >= count($currentPattern)) {
                        $colIdx = 0;
                        $patternIdx++;
                    }
                ?>
                <div class="<?php echo $colClass; ?> gal-cell">
                    <div class="gal-card <?php echo $isLarge ? 'gal-card--lg' : ''; ?>" onclick="openLightbox(<?php echo $index; ?>)" role="button" tabindex="0" style="cursor:pointer;" aria-label="View <?php echo htmlspecialchars($image['title'] ?: $album['title']); ?>">
                        <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="<?php echo htmlspecialchars($image['alt_text'] ?: $image['title'] ?: $album['title']); ?>" loading="lazy">
                        <div class="gal-overlay">
                            <i class="fas fa-search-plus"></i>
                            <?php if ($image['title']): ?>
                            <span><?php echo htmlspecialchars($image['title']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Footer -->
            <div class="gal-footer">
                <span class="gal-count"><i class="fas fa-camera me-2"></i><?php echo count($images); ?> Photos in this album</span>
                <a href="gallery.php" class="gal-cta"><i class="fas fa-th me-2"></i>Browse All Albums</a>
            </div>

        <?php endif; ?>
    </div>
</section>

<!-- Lightbox -->
<div id="lightbox" class="gal-lightbox" style="display:none;" role="dialog" aria-modal="true" aria-label="Image viewer">
    <div class="gal-lightbox-inner">
        <button class="gal-lb-close" onclick="closeLightbox()" aria-label="Close"><i class="fas fa-times"></i></button>
        <button class="gal-lb-prev" onclick="previousImage()" aria-label="Previous image"><i class="fas fa-chevron-left"></i></button>
        <button class="gal-lb-next" onclick="nextImage()" aria-label="Next image"><i class="fas fa-chevron-right"></i></button>
        <div class="gal-lb-content">
            <img id="lightbox-image" src="" alt="" class="gal-lb-img">
            <div class="gal-lb-info">
                <div id="lightbox-caption" class="gal-lb-caption"></div>
                <div id="lightbox-counter" class="gal-lb-counter"></div>
            </div>
        </div>
    </div>
</div>

<script>
const images = <?php echo json_encode(array_values($images)); ?>;
let currentImageIndex = 0;

function openLightbox(index) {
    currentImageIndex = index;
    showImage();
    document.getElementById('lightbox').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function showImage() {
    const image = images[currentImageIndex];
    const lbImg = document.getElementById('lightbox-image');
    lbImg.src = image.image_path;
    lbImg.alt = image.alt_text || image.title || '<?php echo addslashes($album['title']); ?>';
    document.getElementById('lightbox-caption').textContent = image.title || '';
    document.getElementById('lightbox-counter').textContent = (currentImageIndex + 1) + ' / ' + images.length;
}

function nextImage() {
    currentImageIndex = (currentImageIndex + 1) % images.length;
    showImage();
}

function previousImage() {
    currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
    showImage();
}

document.addEventListener('keydown', function(e) {
    if (document.getElementById('lightbox').style.display !== 'none') {
        if (e.key === 'ArrowRight') nextImage();
        if (e.key === 'ArrowLeft') previousImage();
        if (e.key === 'Escape') closeLightbox();
    }
});

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this || e.target.classList.contains('gal-lightbox-inner')) closeLightbox();
});

(function() {
    var lb = document.getElementById('lightbox');
    var sx = 0, sy = 0;
    lb.addEventListener('touchstart', function(e) {
        sx = e.changedTouches[0].screenX;
        sy = e.changedTouches[0].screenY;
    }, { passive: true });
    lb.addEventListener('touchend', function(e) {
        var dx = e.changedTouches[0].screenX - sx;
        var dy = e.changedTouches[0].screenY - sy;
        if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 50) {
            dx < 0 ? nextImage() : previousImage();
        }
    }, { passive: true });
})();

document.querySelectorAll('.gal-card[role="button"]').forEach(function(card) {
    card.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            card.click();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>

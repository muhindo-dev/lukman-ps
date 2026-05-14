<?php 
$currentPage = 'news';
$pageTitle = 'News Article';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');

// Get news article
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT n.*, COALESCE(a.full_name, 'LPS Admin') as author 
                       FROM news_posts n 
                       LEFT JOIN admin_users a ON n.author_id = a.id 
                       WHERE n.id = ? AND n.status = 'published'");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    header('Location: news.php');
    exit;
}

$pageTitle = $news['title'] . ' - ' . $siteShortName;
$pageDescription = substr(strip_tags($news['content']), 0, 160);

// Get related articles (same category, excluding current)
$relatedStmt = $pdo->prepare("SELECT * FROM news_posts WHERE category = ? AND id != ? AND status = 'published' ORDER BY published_at DESC LIMIT 3");
$relatedStmt->execute([$news['category'], $id]);
$relatedArticles = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([
    ['label' => 'News & Updates', 'url' => 'news.php'],
    ['label' => mb_strimwidth($news['title'], 0, 60, '…')],
]);

// Article Structured Data
$articleImage = !empty($news['featured_image']) ? 'https://lukmanps.ac.ug/uploads/' . $news['featured_image'] : '';
?>
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "<?php echo htmlspecialchars($news['title']); ?>",
    "description": "<?php echo htmlspecialchars($pageDescription); ?>",
    "image": "<?php echo $articleImage; ?>",
    "datePublished": "<?php echo $news['published_at']; ?>",
    "dateModified": "<?php echo $news['updated_at']; ?>",
    "author": {
        "@type": "Person",
        "name": "<?php echo htmlspecialchars($news['author']); ?>"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Lukman Primary School",
        "logo": {
            "@type": "ImageObject",
            "url": "https://lukmanps.ac.ug/uploads/logo.png"
        }
    }
}
</script>

<!-- Page Header -->
<section style="padding: 120px 0 60px; background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue));">
    <div class="container">
        <div class="text-center">
            <div style="margin-bottom: 1rem;">
                <a href="news.php" style="color: var(--white); text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Back to News
                </a>
            </div>
            <div style="margin-bottom: 1rem;">
                <span style="display: inline-block; padding: 0.5rem 1rem; background: var(--primary-yellow); color: var(--white); font-weight: 600; font-size: 0.9rem;">
                    <?php echo htmlspecialchars($news['category']); ?>
                </span>
            </div>
            <h1 style="color: #fff; font-size: 2.5rem; font-weight: 800; margin-bottom: 1rem; max-width: 900px; margin-left: auto; margin-right: auto; line-height: 1.2;">
                <?php echo htmlspecialchars($news['title']); ?>
            </h1>
            <div style="color: rgba(255,255,255,0.7); font-size: 1rem;">
                <i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($news['published_at'])); ?>
                <span style="margin: 0 0.5rem;">•</span>
                <i class="far fa-user"></i> <?php echo htmlspecialchars($news['author']); ?>
            </div>
        </div>
    </div>
</section>

<!-- Article Content -->
<section style="padding: 80px 0; background: #fff;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <?php if ($news['featured_image']): 
                    $imagePath = $news['featured_image'];
                    if (strpos($imagePath, '/') === false) {
                        $imagePath = 'news/' . $imagePath;
                    }
                ?>
                <div style="margin-bottom: 3rem; border: 2px solid var(--primary-blue); overflow: hidden;">
                    <img src="uploads/<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" style="width: 100%; height: auto; display: block;">
                </div>
                <?php endif; ?>
                
                <div style="font-size: 1.1rem; line-height: 1.8; color: #333;">
                    <?php echo $news['content']; ?>
                </div>

                <!-- Social Share -->
                <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e9ecef;">
                    <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1rem;">Share this article:</h4>
                    <div style="display: flex; gap: 1rem;">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($news['title']); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&title=<?php echo urlencode($news['title']); ?>" target="_blank" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="whatsapp://send?text=<?php echo urlencode($news['title'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); text-decoration: none; transition: all 0.3s;">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <button id="copy-link-btn" onclick="copyPageLink()" title="Copy link" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); cursor: pointer; transition: all 0.3s;">
                            <i class="fas fa-link" id="copy-icon"></i>
                        </button>
                    </div>
                    <span id="copy-feedback" style="display:none;font-size:.8rem;color:var(--primary);font-weight:600;margin-top:.5rem;">Link copied!</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Articles -->
<?php if (!empty($relatedArticles)): ?>
<section style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <div class="text-center" style="margin-bottom: 3rem;">
            <h2 style="font-size: 2rem; font-weight: 700;">Related Articles</h2>
            <p style="color: #666;">More stories you might be interested in</p>
        </div>
        <div class="row g-4">
            <?php foreach ($relatedArticles as $related): ?>
            <div class="col-lg-4 col-md-6">
                <div class="news-card" style="border: 2px solid var(--primary-blue); border-radius: 0; overflow: hidden; height: 100%; display: flex; flex-direction: column; background: #fff;">
                    <?php if ($related['featured_image']): 
                        $relatedImagePath = $related['featured_image'];
                        if (strpos($relatedImagePath, '/') === false) {
                            $relatedImagePath = 'news/' . $relatedImagePath;
                        }
                    ?>
                    <div class="news-image" style="height: 200px; overflow: hidden;">
                        <img src="uploads/<?php echo htmlspecialchars($relatedImagePath); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                    <div class="news-content" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                        <div class="news-meta" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #666;">
                            <span><i class="far fa-calendar" style="color: var(--primary-green);"></i> <?php echo date('M d, Y', strtotime($related['published_at'])); ?></span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 0.75rem; line-height: 1.4;">
                            <a href="news-detail.php?id=<?php echo $related['id']; ?>" style="color: var(--primary-blue); text-decoration: none;">
                                <?php echo htmlspecialchars($related['title']); ?>
                            </a>
                        </h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1.25rem; flex: 1;">
                            <?php echo htmlspecialchars(substr(strip_tags($related['content']), 0, 100)); ?>...
                        </p>
                        <a href="news-detail.php?id=<?php echo $related['id']; ?>" class="btn btn-sm" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1.5rem; align-self: flex-start; text-decoration: none;">
                            Read More <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section style="padding: 80px 0; background: var(--primary-yellow);">
    <div class="container text-center">
        <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem; color: var(--white);">Interested in Enrolling?</h2>
        <p style="font-size: 1.1rem; margin-bottom: 2rem; color: var(--white);">
            Give your child the gift of quality, values-centred education at Lukman Primary School
        </p>
        <a href="admissions.php" class="btn" style="border: 2px solid var(--primary-blue); background: linear-gradient(135deg, var(--primary-blue), var(--dark-blue)); color: #fff; padding: 1rem 2.5rem; font-weight: 600; font-size: 1.1rem; text-decoration: none; display: inline-block;">
            <i class="fas fa-user-plus me-2"></i> Apply Now
        </a>
    </div>
</section>

<script>
function copyPageLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        var fb = document.getElementById('copy-feedback');
        var ic = document.getElementById('copy-icon');
        ic.className = 'fas fa-check';
        fb.style.display = 'block';
        setTimeout(function() { ic.className = 'fas fa-link'; fb.style.display = 'none'; }, 2000);
    });
}
</script>

<?php include 'includes/footer.php'; ?>

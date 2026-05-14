<?php 
$currentPage = 'news';
include 'config.php';
include 'functions.php';

// Get settings
$siteShortName = getSetting('site_short_name', 'Lukman PS');
$siteName = getSetting('site_name', 'Lukman Primary School');

$pageTitle = 'News & Updates';
$pageDescription = 'Stay updated with the latest news, stories, and announcements from ' . $siteName . '.';
include 'includes/header.php';
include 'includes/breadcrumb.php';
breadcrumb([['label' => 'News & Updates']]);

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Category filter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query
$sql = "SELECT * FROM news_posts WHERE status = 'published'";
if ($category) {
    $sql .= " AND category = :category";
}
$sql .= " ORDER BY published_at DESC LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
if ($category) {
    $stmt->bindParam(':category', $category);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$newsPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count for pagination
$countSql = "SELECT COUNT(*) FROM news_posts WHERE status = 'published'";
if ($category) {
    $countSql .= " AND category = :category";
}
$countStmt = $pdo->prepare($countSql);
if ($category) {
    $countStmt->bindParam(':category', $category);
}
$countStmt->execute();
$totalPosts = $countStmt->fetchColumn();
$totalPages = ceil($totalPosts / $perPage);

// Get categories for filter
$categoriesStmt = $pdo->query("SELECT DISTINCT category FROM news_posts WHERE status = 'published' ORDER BY category");
$categories = $categoriesStmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!-- Page Header -->
<div class="page-header">
    <div class="container">
        <h1>News & Updates</h1>
        <p>Latest stories and announcements from Lukman Primary School</p>
    </div>
</div>

<!-- Category Filter -->
<section style="padding: 2rem 0; background: #f8f9fa; border-bottom: 1px solid var(--gray-border);">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 justify-content-center">
            <a href="news.php" class="btn btn-sm <?php echo !$category ? 'btn-active' : ''; ?>" style="border: 2px solid var(--primary-blue); background: <?php echo !$category ? 'var(--primary-yellow)' : 'transparent'; ?>; color: <?php echo !$category ? 'var(--white)' : 'var(--primary-blue)'; ?>; padding: 0.5rem 1.5rem; font-weight: 600; text-decoration: none;">
                All News
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="news.php?category=<?php echo urlencode($cat); ?>" class="btn btn-sm <?php echo $category === $cat ? 'btn-active' : ''; ?>" style="border: 2px solid var(--primary-blue); background: <?php echo $category === $cat ? 'var(--primary-yellow)' : 'transparent'; ?>; color: <?php echo $category === $cat ? 'var(--white)' : 'var(--primary-blue)'; ?>; padding: 0.5rem 1.5rem; font-weight: 600; text-decoration: none;">
                <?php echo htmlspecialchars($cat); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- News Grid -->
<section style="padding: 80px 0; background: #fff;">
    <div class="container">
        <?php if (empty($newsPosts)): ?>
            <div class="text-center" style="padding: 3rem 0;">
                <i class="fas fa-newspaper" style="font-size: 4rem; color: #ddd; margin-bottom: 1rem;"></i>
                <h3 style="color: #666;">No news articles found</h3>
                <p style="color: #999;">Check back soon for updates!</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($newsPosts as $news): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="news-card" style="border: 2px solid var(--primary-blue); border-radius: 0; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: transform 0.3s;">
                        <?php if ($news['featured_image']): 
                            // Handle both old format (filename only) and new format (news/filename)
                            $imagePath = $news['featured_image'];
                            if (strpos($imagePath, '/') === false) {
                                $imagePath = 'news/' . $imagePath;
                            }
                        ?>
                        <div class="news-image" style="height: 220px; overflow: hidden;">
                            <img src="uploads/<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($news['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <?php endif; ?>
                        <div class="news-content" style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column;">
                            <div class="news-meta" style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #666;">
                                <span><i class="far fa-calendar" style="color: var(--primary-green);"></i> <?php echo date('M d, Y', strtotime($news['published_at'])); ?></span>
                                <span><i class="far fa-folder" style="color: var(--primary-green);"></i> <?php echo htmlspecialchars($news['category']); ?></span>
                            </div>
                            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 0.75rem; line-height: 1.4;">
                                <a href="news-detail.php?id=<?php echo $news['id']; ?>" style="color: var(--primary-blue); text-decoration: none;">
                                    <?php echo htmlspecialchars($news['title']); ?>
                                </a>
                            </h3>
                            <p style="color: #666; font-size: 0.95rem; margin-bottom: 1.25rem; flex: 1;">
                                <?php echo htmlspecialchars(substr(strip_tags($news['content']), 0, 150)); ?>...
                            </p>
                            <a href="news-detail.php?id=<?php echo $news['id']; ?>" class="btn btn-sm" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1.5rem; align-self: flex-start; transition: all 0.3s; text-decoration: none;">
                                Read More <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <nav aria-label="News pagination" style="margin-top: 3rem;">
                <ul class="pagination justify-content-center" style="gap: 0.5rem;">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="news.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
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
                            <a class="page-link" href="news.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="news.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?>" style="border: 2px solid var(--primary-blue); background: transparent; color: var(--primary-blue); padding: 0.5rem 1rem; text-decoration: none;">
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

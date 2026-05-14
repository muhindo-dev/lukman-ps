<?php
/**
 * Dynamic XML Sitemap Generator — Lukman Primary School
 */
header('Content-Type: application/xml; charset=utf-8');

require_once 'config.php';
require_once 'functions.php';

$baseUrl = 'https://lukmanps.ac.ug';

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Core Pages -->
    <url><loc><?php echo $baseUrl; ?>/</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>
    <url><loc><?php echo $baseUrl; ?>/about.php</loc><changefreq>monthly</changefreq><priority>0.8</priority></url>
    <url><loc><?php echo $baseUrl; ?>/academics.php</loc><changefreq>monthly</changefreq><priority>0.8</priority></url>
    <url><loc><?php echo $baseUrl; ?>/admissions.php</loc><changefreq>monthly</changefreq><priority>0.9</priority></url>
    <url><loc><?php echo $baseUrl; ?>/student-life.php</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>
    <url><loc><?php echo $baseUrl; ?>/results.php</loc><changefreq>quarterly</changefreq><priority>0.7</priority></url>
    <url><loc><?php echo $baseUrl; ?>/faq.php</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
    <url><loc><?php echo $baseUrl; ?>/contact.php</loc><changefreq>monthly</changefreq><priority>0.7</priority></url>

    <!-- Media -->
    <url><loc><?php echo $baseUrl; ?>/news.php</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
    <url><loc><?php echo $baseUrl; ?>/events.php</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>
    <url><loc><?php echo $baseUrl; ?>/gallery.php</loc><changefreq>weekly</changefreq><priority>0.6</priority></url>
    <url><loc><?php echo $baseUrl; ?>/testimonials.php</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>
    <url><loc><?php echo $baseUrl; ?>/downloads.php</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>

    <!-- Dynamic: News Posts -->
<?php
$stmt = $pdo->query("SELECT id, updated_at FROM news_posts WHERE status = 'published' ORDER BY published_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
?>
    <url><loc><?php echo $baseUrl; ?>/news-detail.php?id=<?php echo $row['id']; ?></loc><lastmod><?php echo date('Y-m-d', strtotime($row['updated_at'])); ?></lastmod><changefreq>monthly</changefreq><priority>0.6</priority></url>
<?php endwhile; ?>

    <!-- Dynamic: Events -->
<?php
$stmt = $pdo->query("SELECT id, updated_at FROM events WHERE status IN ('upcoming','ongoing') ORDER BY event_date DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
?>
    <url><loc><?php echo $baseUrl; ?>/event-detail.php?id=<?php echo $row['id']; ?></loc><lastmod><?php echo date('Y-m-d', strtotime($row['updated_at'])); ?></lastmod><changefreq>weekly</changefreq><priority>0.5</priority></url>
<?php endwhile; ?>

    <!-- Dynamic: Gallery Albums -->
<?php
$stmt = $pdo->query("SELECT id, updated_at FROM gallery_albums WHERE status = 'active' ORDER BY created_at DESC");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
?>
    <url><loc><?php echo $baseUrl; ?>/gallery-album.php?id=<?php echo $row['id']; ?></loc><lastmod><?php echo date('Y-m-d', strtotime($row['updated_at'])); ?></lastmod><changefreq>monthly</changefreq><priority>0.5</priority></url>
<?php endwhile; ?>
</urlset>
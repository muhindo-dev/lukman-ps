<?php
/**
 * Breadcrumb helper
 * Usage: breadcrumb([['label'=>'News','url'=>'news.php'], ['label'=>'Article Title']])
 * Last item should have no URL (current page).
 */
function breadcrumb(array $crumbs, string $homeName = 'Home'): void {
    // Prepend Home
    array_unshift($crumbs, ['label' => $homeName, 'url' => 'index.php']);

    // Schema.org JSON-LD
    $items = [];
    foreach ($crumbs as $i => $crumb) {
        $item = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $crumb['label'],
        ];
        if (!empty($crumb['url'])) {
            $item['item'] = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
                . '://' . $_SERVER['HTTP_HOST']
                . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/'
                . $crumb['url'];
        }
        $items[] = $item;
    }
    $ld = json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $items,
    ], JSON_UNESCAPED_SLASHES);

    echo '<script type="application/ld+json">' . $ld . '</script>' . "\n";

    // Visual breadcrumb
    echo '<nav aria-label="breadcrumb" class="lps-breadcrumb">';
    echo '<div class="container"><ol class="breadcrumb mb-0">';
    $last = count($crumbs) - 1;
    foreach ($crumbs as $i => $crumb) {
        if ($i < $last) {
            echo '<li class="breadcrumb-item">';
            echo '<a href="' . htmlspecialchars($crumb['url']) . '">' . htmlspecialchars($crumb['label']) . '</a>';
            echo '</li>';
        } else {
            echo '<li class="breadcrumb-item active" aria-current="page">';
            echo htmlspecialchars($crumb['label']);
            echo '</li>';
        }
    }
    echo '</ol></div></nav>';
}
?>

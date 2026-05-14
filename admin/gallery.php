<?php
/**
 * Gallery Management - Albums List
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'gallery';
$pageTitle = 'Gallery';

$perPage = 12;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$category = $_GET['category'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$filters = [];
if ($category) $filters['category'] = $category;
if ($status) $filters['status'] = $status;

if ($search) {
    $searchFields = ['title', 'description'];
    $totalRecords = getSearchCount('gallery_albums', $searchFields, $search, $filters);
    $paginationData = getPaginationData($totalRecords, $page, $perPage);
    $albums = searchRecords('gallery_albums', $searchFields, $search, $filters, 'created_at DESC', $perPage, $paginationData['offset']);
} else {
    $totalRecords = getRecordCount('gallery_albums', $filters);
    $paginationData = getPaginationData($totalRecords, $page, $perPage);
    $albums = getAllRecords('gallery_albums', $filters, 'created_at DESC', $perPage, $paginationData['offset']);
}

$categories = ['Events', 'Programs', 'Community', 'Volunteers', 'Success Stories', 'Behind the Scenes'];

include 'includes/header.php';
?>

<div class="admin-content">
    <?php if (isset($_SESSION['alert'])): ?>
        <div class="alert alert-<?php echo $_SESSION['alert']['type']; ?>">
            <i class="fas fa-<?php echo $_SESSION['alert']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
            <?php echo $_SESSION['alert']['message']; ?>
        </div>
        <?php unset($_SESSION['alert']); ?>
    <?php endif; ?>
    
    <div class="content-header-compact">
        <h1><i class="fas fa-images"></i> Gallery Albums</h1>
        <div class="header-actions">
            <form method="GET" class="search-inline">
                <input type="text" name="search" class="form-control-sm" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
                <select name="category" class="form-control-sm">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($category == $cat) ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control-sm">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="hidden" <?php echo ($status == 'hidden') ? 'selected' : ''; ?>>Hidden</option>
                </select>
                <button type="submit" class="btn-sm btn-secondary"><i class="fas fa-filter"></i></button>
            </form>
            <a href="gallery-add.php" class="btn-sm btn-primary"><i class="fas fa-plus"></i> New Album</a>
        </div>
    </div>

    <?php if (empty($albums)): ?>
        <div class="empty-state">
            <i class="fas fa-images"></i>
            <p>No albums found</p>
            <a href="gallery-add.php" class="btn-sm btn-primary"><i class="fas fa-plus"></i> Create First Album</a>
        </div>
    <?php else: ?>
        <div class="albums-grid">
            <?php foreach ($albums as $album): ?>
                <div class="album-card">
                    <div class="album-cover">
                        <?php if ($album['cover_image']): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($album['cover_image']); ?>" alt="<?php echo htmlspecialchars($album['title']); ?>">
                        <?php else: ?>
                            <div class="no-cover"><i class="fas fa-images"></i></div>
                        <?php endif; ?>
                        <div class="album-overlay">
                            <a href="gallery-images.php?album=<?php echo $album['id']; ?>" class="overlay-btn" title="Manage Images"><i class="fas fa-th"></i></a>
                            <a href="gallery-edit.php?id=<?php echo $album['id']; ?>" class="overlay-btn" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="gallery-delete.php?id=<?php echo $album['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="overlay-btn danger" title="Delete" onclick="return confirm('Delete this album and all images?');"><i class="fas fa-trash"></i></a>
                        </div>
                        <?php if ($album['is_featured']): ?>
                            <span class="featured-badge"><i class="fas fa-star"></i></span>
                        <?php endif; ?>
                        <?php if ($album['status'] == 'hidden'): ?>
                            <span class="hidden-badge"><i class="fas fa-eye-slash"></i></span>
                        <?php endif; ?>
                    </div>
                    <div class="album-info">
                        <h3><?php echo htmlspecialchars($album['title']); ?></h3>
                        <div class="album-meta">
                            <span><i class="fas fa-images"></i> <?php echo $album['image_count']; ?></span>
                            <span><i class="fas fa-folder"></i> <?php echo $album['category']; ?></span>
                            <span><i class="fas fa-eye"></i> <?php echo number_format($album['views']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($paginationData['total_pages'] > 1): ?>
            <div class="pagination-compact">
                <span>Showing <?php echo $paginationData['offset'] + 1; ?>-<?php echo min($paginationData['offset'] + $perPage, $totalRecords); ?> of <?php echo $totalRecords; ?></span>
                <div class="pagination">
                    <?php if ($paginationData['has_previous']): ?>
                        <a href="?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <span>Page <?php echo $page; ?> of <?php echo $paginationData['total_pages']; ?></span>
                    <?php if ($paginationData['has_next']): ?>
                        <a href="?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . urlencode($category) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<style>
.content-header-compact { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; padding-bottom: 0.375rem; border-bottom: 2px solid #FFC107; }
.content-header-compact h1 { font-size: 1.25rem; margin: 0; font-weight: 700; }
.header-actions { display: flex; gap: 0.375rem; align-items: center; }
.search-inline { display: flex; gap: 0.375rem; }
.form-control-sm { padding: 0.25rem 0.5rem; font-size: 0.8125rem; border: 2px solid #dee2e6; outline: none; }
.form-control-sm:focus { border-color: #FFC107; }
.btn-sm { padding: 0.25rem 0.625rem; font-size: 0.8125rem; border: 2px solid; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; background: #fff; }
.btn-sm.btn-primary { background: #FFC107; border-color: #FFC107; color: #000; }
.btn-sm.btn-primary:hover { background: #000; color: #FFC107; }
.btn-sm.btn-secondary { background: #6c757d; border-color: #6c757d; color: #fff; }

.albums-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1rem; }
.album-card { background: #fff; border: 2px solid #dee2e6; overflow: hidden; transition: all 0.2s; }
.album-card:hover { border-color: #FFC107; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.album-cover { position: relative; height: 150px; background: #f8f9fa; overflow: hidden; }
.album-cover img { width: 100%; height: 100%; object-fit: cover; }
.no-cover { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #dee2e6; font-size: 3rem; }
.album-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; gap: 0.5rem; opacity: 0; transition: opacity 0.2s; }
.album-card:hover .album-overlay { opacity: 1; }
.overlay-btn { width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; background: #FFC107; color: #000; text-decoration: none; font-size: 0.875rem; transition: all 0.2s; }
.overlay-btn:hover { background: #fff; transform: scale(1.1); }
.overlay-btn.danger:hover { background: #dc3545; color: #fff; }
.featured-badge { position: absolute; top: 0.5rem; left: 0.5rem; background: #FFC107; color: #000; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }
.hidden-badge { position: absolute; top: 0.5rem; right: 0.5rem; background: #6c757d; color: #fff; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; }
.album-info { padding: 0.75rem; }
.album-info h3 { font-size: 0.875rem; font-weight: 600; margin: 0 0 0.375rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.album-meta { display: flex; gap: 0.75rem; font-size: 0.6875rem; color: #6c757d; }
.album-meta span { display: flex; align-items: center; gap: 0.25rem; }

.empty-state { text-align: center; padding: 3rem 1rem; color: #6c757d; background: #fff; border: 2px solid #dee2e6; }
.empty-state i { font-size: 3rem; color: #dee2e6; margin-bottom: 1rem; display: block; }
.empty-state p { margin: 0.5rem 0 1rem; font-size: 0.875rem; }

.pagination-compact { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; font-size: 0.75rem; margin-top: 1rem; }
.pagination { display: flex; gap: 0.25rem; align-items: center; }
.pagination a { width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border: 2px solid #dee2e6; color: #495057; text-decoration: none; font-size: 0.75rem; }
.pagination a:hover { border-color: #FFC107; background: #fff9e6; color: #000; }

.alert { padding: 0.625rem 0.75rem; margin-bottom: 0.75rem; border: 2px solid; font-size: 0.8125rem; }
.alert-success { background: #d4edda; border-color: #28a745; color: #155724; }
.alert-danger { background: #f8d7da; border-color: #dc3545; color: #721c24; }

@media (max-width: 768px) {
    .content-header-compact { flex-direction: column; align-items: stretch; gap: 0.5rem; }
    .header-actions { flex-direction: column; }
    .search-inline { flex-direction: column; }
    .form-control-sm { width: 100%; }
    .albums-grid { grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 0.75rem; }
    .album-cover { height: 120px; }
    .pagination-compact { flex-direction: column; gap: 0.5rem; text-align: center; }
}
</style>

<?php include 'includes/footer.php'; ?>

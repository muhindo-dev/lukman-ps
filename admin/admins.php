<?php
/**
 * Admin Users Management - List View
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'admins';
$pageTitle = 'Admin Users';

// Pagination settings
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get filters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(username LIKE ? OR full_name LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$pdo = getDBConnection();

// Get total count
$countSql = "SELECT COUNT(*) FROM admin_users $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = $stmt->fetchColumn();

$totalPages = ceil($totalRecords / $perPage);
$page = max(1, min($page, $totalPages ?: 1));
$offset = ($page - 1) * $perPage;

// Get admins
$sql = "SELECT * FROM admin_users $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$admins = $stmt->fetchAll();

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
        <h1><i class="fas fa-user-shield"></i> Admin Users</h1>
        <div class="header-actions">
            <form method="GET" action="" class="search-inline">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control-sm" 
                    placeholder="Search..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <select name="status" class="form-control-sm">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <button type="submit" class="btn-sm btn-secondary"><i class="fas fa-filter"></i></button>
            </form>
            <a href="admins-add.php" class="btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add Admin
            </a>
        </div>
    </div>

    <div class="card">
        <?php if (empty($admins)): ?>
            <div class="empty-state">
                <i class="fas fa-user-shield"></i>
                <p>No admin users found</p>
                <a href="admins-add.php" class="btn-sm btn-primary"><i class="fas fa-plus"></i> Add First Admin</a>
            </div>
        <?php else: ?>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th width="40">ID</th>
                        <th>Admin</th>
                        <th width="150">Username</th>
                        <th width="80">Status</th>
                        <th width="120">Last Login</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td data-label="ID"><?php echo $admin['id']; ?></td>
                            <td data-label="Admin">
                                <div class="admin-cell">
                                    <div class="admin-avatar">
                                        <?php if ($admin['avatar']): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($admin['avatar']); ?>" alt="">
                                        <?php else: ?>
                                            <i class="fas fa-user"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="admin-info">
                                        <strong><?php echo htmlspecialchars($admin['full_name']); ?></strong>
                                        <small><?php echo htmlspecialchars($admin['email']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Username"><code><?php echo htmlspecialchars($admin['username']); ?></code></td>
                            <td data-label="Status"><?php echo getStatusBadge($admin['status'] ?? 'active'); ?></td>
                            <td data-label="Last Login">
                                <?php echo $admin['last_login'] ? date('M d, Y', strtotime($admin['last_login'])) : '<span class="text-muted">Never</span>'; ?>
                            </td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="admins-edit.php?id=<?php echo $admin['id']; ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                    <?php if ($admin['id'] != $currentAdmin['id']): ?>
                                        <a href="admins-delete.php?id=<?php echo $admin['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this admin user?');"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <div class="pagination-compact">
                    <span>Showing <?php echo $offset + 1; ?>-<?php echo min($offset + $perPage, $totalRecords); ?> of <?php echo $totalRecords; ?></span>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<style>
.content-header-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.375rem;
    border-bottom: 2px solid #FFC107;
}

.content-header-compact h1 {
    font-size: 1.25rem;
    margin: 0;
    font-weight: 700;
}

.header-actions {
    display: flex;
    gap: 0.375rem;
    align-items: center;
}

.search-inline {
    display: flex;
    gap: 0.375rem;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8125rem;
    border: 2px solid #dee2e6;
    outline: none;
}

.form-control-sm:focus {
    border-color: #FFC107;
}

.btn-sm {
    padding: 0.25rem 0.625rem;
    font-size: 0.8125rem;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
}

.btn-primary {
    background: #FFC107;
    border-color: #000;
    color: #000;
}

.btn-secondary {
    background: #f8f9fa;
    border-color: #000;
    color: #000;
}

.card {
    background: #fff;
    border: 2px solid #000;
}

.table-compact {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.table-compact th,
.table-compact td {
    padding: 0.5rem 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.table-compact th {
    background: #f8f9fa;
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    border-bottom: 2px solid #000;
}

.table-compact tr:hover {
    background: #fffef5;
}

.admin-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.admin-avatar {
    width: 40px;
    height: 40px;
    border: 2px solid #000;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    overflow: hidden;
}

.admin-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.admin-avatar i {
    font-size: 1.25rem;
    color: #6c757d;
}

.admin-info {
    display: flex;
    flex-direction: column;
}

.admin-info small {
    color: #6c757d;
    font-size: 0.75rem;
}

code {
    background: #f8f9fa;
    padding: 0.125rem 0.375rem;
    font-size: 0.8125rem;
    border: 1px solid #dee2e6;
}

.text-muted {
    color: #6c757d;
}

.actions {
    display: flex;
    gap: 0.25rem;
}

.btn-icon {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #000;
    background: #f8f9fa;
    color: #000;
    text-decoration: none;
    font-size: 0.75rem;
}

.btn-icon:hover {
    background: #FFC107;
}

.btn-icon.btn-danger {
    background: #fff;
}

.btn-icon.btn-danger:hover {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
}

.badge {
    padding: 0.2rem 0.5rem;
    font-size: 0.7rem;
    font-weight: 600;
    border: 1px solid;
    display: inline-block;
}

.badge-dark {
    background: #000;
    color: #FFC107;
    border-color: #000;
}

.badge-primary {
    background: #FFC107;
    color: #000;
    border-color: #000;
}

.badge-info {
    background: #17a2b8;
    color: #fff;
    border-color: #17a2b8;
}

.badge-success {
    background: #28a745;
    color: #fff;
    border-color: #28a745;
}

.badge-secondary {
    background: #6c757d;
    color: #fff;
    border-color: #6c757d;
}

.badge-danger {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
}

.pagination-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    border-top: 2px solid #000;
    font-size: 0.8125rem;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination a {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #000;
    background: #f8f9fa;
    color: #000;
    text-decoration: none;
}

.pagination a:hover {
    background: #FFC107;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #dee2e6;
}

.alert {
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border: 2px solid;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-error {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

@media (max-width: 768px) {
    .content-header-compact {
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-start;
    }
    
    .search-inline {
        flex-wrap: wrap;
    }
    
    .table-compact thead {
        display: none;
    }
    
    .table-compact tr {
        display: block;
        margin-bottom: 0.5rem;
        border: 2px solid #000;
    }
    
    .table-compact td {
        display: flex;
        justify-content: space-between;
        border: none;
        border-bottom: 1px solid #dee2e6;
    }
    
    .table-compact td:before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

<?php
/**
 * Our Team - List View
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'team';
$pageTitle = 'Our Team';

// Pagination settings
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get filters
$department = $_GET['department'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(name LIKE ? OR position LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($department) {
    $conditions[] = "department = ?";
    $params[] = $department;
}

if ($status) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

$pdo = getDBConnection();

// Get total count
$countSql = "SELECT COUNT(*) FROM team_members $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = $stmt->fetchColumn();

$totalPages = ceil($totalRecords / $perPage);
$page = max(1, min($page, $totalPages ?: 1));
$offset = ($page - 1) * $perPage;

// Get team members
$sql = "SELECT * FROM team_members $whereClause ORDER BY sort_order ASC, name ASC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$members = $stmt->fetchAll();

$departments = ['Leadership', 'Management', 'Program Staff', 'Administrative', 'Volunteers', 'Advisors'];

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
        <h1><i class="fas fa-users"></i> Our Team</h1>
        <div class="header-actions">
            <form method="GET" action="" class="search-inline">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control-sm" 
                    placeholder="Search..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <select name="department" class="form-control-sm">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept; ?>" <?php echo ($department == $dept) ? 'selected' : ''; ?>><?php echo $dept; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control-sm">
                    <option value="">All Status</option>
                    <option value="active" <?php echo ($status == 'active') ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo ($status == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <button type="submit" class="btn-sm btn-secondary"><i class="fas fa-filter"></i></button>
            </form>
            <a href="team-add.php" class="btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>

    <div class="card">
        <?php if (empty($members)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No team members found</p>
                <a href="team-add.php" class="btn-sm btn-primary"><i class="fas fa-plus"></i> Add First Member</a>
            </div>
        <?php else: ?>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th width="40">ID</th>
                        <th>Name</th>
                        <th width="150">Position</th>
                        <th width="120">Department</th>
                        <th width="80">Status</th>
                        <th width="60">Order</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $member): ?>
                        <tr>
                            <td data-label="ID"><?php echo $member['id']; ?></td>
                            <td data-label="Name">
                                <div class="title-cell">
                                    <?php if ($member['photo']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($member['photo']); ?>" alt="" class="member-thumb">
                                    <?php else: ?>
                                        <div class="member-thumb-placeholder"><i class="fas fa-user"></i></div>
                                    <?php endif; ?>
                                    <div class="member-name-wrap">
                                        <span class="member-name"><?php echo htmlspecialchars($member['name']); ?></span>
                                        <?php if ($member['is_featured']): ?>
                                            <span class="featured-star" title="Featured"><i class="fas fa-star"></i></span>
                                        <?php endif; ?>
                                        <?php if ($member['email']): ?>
                                            <small class="member-email"><?php echo htmlspecialchars($member['email']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Position"><?php echo htmlspecialchars($member['position']); ?></td>
                            <td data-label="Department"><span class="badge badge-info"><?php echo htmlspecialchars($member['department'] ?? 'Program Staff'); ?></span></td>
                            <td data-label="Status"><?php echo getStatusBadge($member['status']); ?></td>
                            <td data-label="Order"><?php echo $member['sort_order']; ?></td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="team-edit.php?id=<?php echo $member['id']; ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="team-delete.php?id=<?php echo $member['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this team member?');"><i class="fas fa-trash"></i></a>
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
                            <a href="?page=<?php echo $page - 1; ?><?php echo $department ? '&department=' . urlencode($department) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <span>Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $department ? '&department=' . urlencode($department) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a>
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
    background: #fff;
}

.btn-sm.btn-primary {
    background: #FFC107;
    border-color: #FFC107;
    color: #000;
}

.btn-sm.btn-primary:hover {
    background: #000;
    color: #FFC107;
}

.btn-sm.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.btn-sm.btn-secondary:hover {
    background: #5a6268;
}

.card {
    background: #fff;
    border: 2px solid #dee2e6;
    margin-bottom: 0;
}

.table-compact {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.8125rem;
}

.table-compact thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table-compact th {
    padding: 0.375rem 0.5rem;
    text-align: left;
    font-weight: 600;
    color: #495057;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-compact td {
    padding: 0.375rem 0.5rem;
    border-bottom: 1px solid #dee2e6;
    vertical-align: middle;
}

.table-compact tbody tr:hover {
    background: #f8f9fa;
}

.title-cell {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.member-thumb {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border: 2px solid #FFC107;
    border-radius: 50%;
}

.member-thumb-placeholder {
    width: 36px;
    height: 36px;
    border: 2px solid #dee2e6;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    color: #adb5bd;
    font-size: 0.875rem;
}

.member-name-wrap {
    display: flex;
    flex-direction: column;
    line-height: 1.2;
}

.member-name {
    font-weight: 600;
    font-size: 0.8125rem;
}

.featured-star {
    color: #FFC107;
    font-size: 0.625rem;
    margin-left: 4px;
    display: inline;
}

.member-email {
    font-size: 0.6875rem;
    color: #6c757d;
}

.badge {
    display: inline-block;
    padding: 2px 6px;
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-info {
    background: #e8f4fd;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.actions {
    display: flex;
    gap: 0.25rem;
}

.btn-icon {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    color: #495057;
    text-decoration: none;
    font-size: 0.75rem;
    background: #fff;
}

.btn-icon:hover {
    background: #FFC107;
    border-color: #FFC107;
}

.btn-icon.btn-danger {
    border-color: #dc3545;
    color: #dc3545;
}

.btn-icon.btn-danger:hover {
    background: #dc3545;
    color: #fff;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0 0 1rem;
}

.pagination-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    border-top: 2px solid #dee2e6;
    font-size: 0.75rem;
    color: #6c757d;
}

.pagination {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination a {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    color: #000;
    text-decoration: none;
    font-size: 0.75rem;
}

.pagination a:hover {
    background: #FFC107;
    border-color: #FFC107;
}

.alert {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0.5rem;
    border: 2px solid;
    font-size: 0.8125rem;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

@media (max-width: 768px) {
    .header-actions {
        flex-wrap: wrap;
    }
    .search-inline {
        width: 100%;
        flex-wrap: wrap;
    }
    .form-control-sm {
        flex: 1;
        min-width: 100px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

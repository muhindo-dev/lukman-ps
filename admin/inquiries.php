<?php
/**
 * Inquiries Management - List View
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'inquiries';
$pageTitle = 'Inquiries';

// Pagination settings
$perPage = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get filters
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$pdo = getDBConnection();

// Check if status column exists
$hasStatusColumn = false;
try {
    $checkCol = $pdo->query("SHOW COLUMNS FROM contact_inquiries LIKE 'status'");
    $hasStatusColumn = (bool)$checkCol->fetch();
} catch (Exception $e) {}

// Build query
$conditions = [];
$params = [];

if ($search) {
    $conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status && $hasStatusColumn) {
    $conditions[] = "status = ?";
    $params[] = $status;
}

$whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

// Get total count
$countSql = "SELECT COUNT(*) FROM contact_inquiries $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalRecords = $stmt->fetchColumn();

$totalPages = ceil($totalRecords / $perPage);
$page = max(1, min($page, $totalPages ?: 1));
$offset = ($page - 1) * $perPage;

// Get inquiries
$sql = "SELECT * FROM contact_inquiries $whereClause ORDER BY created_at DESC LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

// Check if status column exists and get counts
$newCount = 0;
$statusCounts = [];
if ($hasStatusColumn) {
    try {
        $countStmt = $pdo->query("SELECT status, COUNT(*) as count FROM contact_inquiries GROUP BY status");
        while ($row = $countStmt->fetch()) {
            $statusCounts[$row['status']] = $row['count'];
        }
        $newCount = $statusCounts['new'] ?? 0;
    } catch (Exception $e) {}
}

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
        <h1><i class="fas fa-envelope"></i> Inquiries <?php if ($newCount > 0): ?><span class="badge badge-warning"><?php echo $newCount; ?> New</span><?php endif; ?></h1>
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
                    <option value="new" <?php echo ($status == 'new') ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo ($status == 'read') ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo ($status == 'replied') ? 'selected' : ''; ?>>Replied</option>
                    <option value="archived" <?php echo ($status == 'archived') ? 'selected' : ''; ?>>Archived</option>
                </select>
                <button type="submit" class="btn-sm btn-secondary"><i class="fas fa-filter"></i></button>
            </form>
        </div>
    </div>

    <div class="card">
        <?php if (empty($inquiries)): ?>
            <div class="empty-state">
                <i class="fas fa-envelope-open"></i>
                <p>No inquiries found</p>
            </div>
        <?php else: ?>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th width="40">ID</th>
                        <th width="150">Name</th>
                        <th width="180">Email</th>
                        <th>Subject</th>
                        <th width="80">Status</th>
                        <th width="120">Date</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr class="<?php echo ($inquiry['status'] ?? '') == 'new' ? 'row-highlight' : ''; ?>">
                            <td data-label="ID"><?php echo $inquiry['id']; ?></td>
                            <td data-label="Name">
                                <strong><?php echo htmlspecialchars($inquiry['name']); ?></strong>
                                <?php if ($inquiry['phone']): ?>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($inquiry['phone']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td data-label="Email">
                                <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>" class="email-link">
                                    <?php echo htmlspecialchars($inquiry['email']); ?>
                                </a>
                            </td>
                            <td data-label="Subject">
                                <?php echo htmlspecialchars($inquiry['subject']); ?>
                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($inquiry['message'] ?? '', 0, 60)); ?>...</small>
                            </td>
                            <td data-label="Status"><?php echo getInquiryStatusBadge($inquiry['status'] ?? 'new'); ?></td>
                            <td data-label="Date"><?php echo date('M d, Y', strtotime($inquiry['created_at'])); ?><br><small class="text-muted"><?php echo date('h:i A', strtotime($inquiry['created_at'])); ?></small></td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="inquiries-view.php?id=<?php echo $inquiry['id']; ?>" class="btn-icon" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="inquiries-delete.php?id=<?php echo $inquiry['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this inquiry?');"><i class="fas fa-trash"></i></a>
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

<?php
function getInquiryStatusBadge($status) {
    $badges = [
        'new' => '<span class="badge badge-warning">New</span>',
        'read' => '<span class="badge badge-info">Read</span>',
        'replied' => '<span class="badge badge-success">Replied</span>',
        'archived' => '<span class="badge badge-secondary">Archived</span>'
    ];
    return $badges[$status] ?? '<span class="badge badge-secondary">Unknown</span>';
}
?>

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
    display: flex;
    align-items: center;
    gap: 0.5rem;
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

.row-highlight {
    background: #fff9e6 !important;
    border-left: 3px solid #FFC107;
}

.row-highlight:hover {
    background: #fff5cc !important;
}

.email-link {
    color: #000;
    text-decoration: none;
}

.email-link:hover {
    color: #FFC107;
    text-decoration: underline;
}

.text-muted {
    color: #6c757d;
    font-size: 0.75rem;
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

.badge-warning {
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

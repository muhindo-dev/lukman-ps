<?php
/**
 * Events Management - List View
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

// Check authentication
requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'events';
$pageTitle = 'Events';

// Pagination settings
$perPage = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get filters
$eventType = $_GET['event_type'] ?? '';
$status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build filters array
$filters = [];
if ($eventType) {
    $filters['event_type'] = $eventType;
}
if ($status) {
    $filters['status'] = $status;
}

// Get records
if ($search) {
    $searchFields = ['title', 'description', 'location'];
    $totalRecords = getSearchCount('events', $searchFields, $search, $filters);
    $paginationData = getPaginationData($totalRecords, $page, $perPage);
    $events = searchRecords('events', $searchFields, $search, $filters, 'event_date DESC', $perPage, $paginationData['offset']);
} else {
    $totalRecords = getRecordCount('events', $filters);
    $paginationData = getPaginationData($totalRecords, $page, $perPage);
    $events = getAllRecords('events', $filters, 'event_date DESC', $perPage, $paginationData['offset']);
}

// Event types
$eventTypes = ['Fundraiser', 'Workshop', 'Community Service', 'Campaign', 'Awareness', 'Volunteer Drive'];

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
        <h1><i class="fas fa-calendar-alt"></i> Events</h1>
        <div class="header-actions">
            <form method="GET" action="" class="search-inline">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control-sm" 
                    placeholder="Search..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >
                <select name="event_type" class="form-control-sm">
                    <option value="">All Types</option>
                    <?php foreach ($eventTypes as $type): ?>
                        <option value="<?php echo $type; ?>" <?php echo ($eventType == $type) ? 'selected' : ''; ?>><?php echo $type; ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="status" class="form-control-sm">
                    <option value="">All Status</option>
                    <option value="upcoming" <?php echo ($status == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                    <option value="ongoing" <?php echo ($status == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                    <option value="completed" <?php echo ($status == 'completed') ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn-sm btn-secondary"><i class="fas fa-filter"></i></button>
            </form>
            <a href="events-add.php" class="btn-sm btn-primary">
                <i class="fas fa-plus"></i> Add New
            </a>
        </div>
    </div>

    <div class="card">
        <?php if (empty($events)): ?>
            <div class="empty-state">
                <i class="fas fa-calendar-alt"></i>
                <p>No events found</p>
                <a href="events-add.php" class="btn-sm btn-primary"><i class="fas fa-plus"></i> Add First Event</a>
            </div>
        <?php else: ?>
            <table class="table-compact">
                <thead>
                    <tr>
                        <th width="40">ID</th>
                        <th>Event Title</th>
                        <th width="120">Type</th>
                        <th width="120">Start Date</th>
                        <th width="120">Location</th>
                        <th width="80">Status</th>
                        <th width="60">Views</th>
                        <th width="100">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event): ?>
                        <tr>
                            <td data-label="ID"><?php echo $event['id']; ?></td>
                            <td data-label="Event Title">
                                <div class="title-cell">
                                    <?php if ($event['featured_image']): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($event['featured_image']); ?>" alt="">
                                    <?php endif; ?>
                                    <span><?php echo htmlspecialchars($event['title']); ?></span>
                                </div>
                            </td>
                            <td data-label="Type"><span class="badge badge-info"><?php echo htmlspecialchars($event['event_type'] ?? '-'); ?></span></td>
                            <td data-label="Start Date"><?php echo formatDateTime($event['event_date'], 'M d, Y h:i A'); ?></td>
                            <td data-label="Location"><?php echo htmlspecialchars(truncateText($event['location'] ?? '', 20)); ?></td>
                            <td data-label="Status"><?php echo getStatusBadge($event['status']); ?></td>
                            <td data-label="Views"><?php echo number_format($event['views']); ?></td>
                            <td data-label="Actions">
                                <div class="actions">
                                    <a href="events-edit.php?id=<?php echo $event['id']; ?>" class="btn-icon" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="events-delete.php?id=<?php echo $event['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-icon btn-danger" title="Delete" onclick="return confirm('Delete this event?');"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($paginationData['total_pages'] > 1): ?>
                <div class="pagination-compact">
                    <span>Showing <?php echo $paginationData['offset'] + 1; ?>-<?php echo min($paginationData['offset'] + $perPage, $totalRecords); ?> of <?php echo $totalRecords; ?></span>
                    <div class="pagination">
                        <?php if ($paginationData['has_previous']): ?>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $eventType ? '&event_type=' . urlencode($eventType) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <span>Page <?php echo $page; ?> of <?php echo $paginationData['total_pages']; ?></span>
                        <?php if ($paginationData['has_next']): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $eventType ? '&event_type=' . urlencode($eventType) : ''; ?><?php echo $status ? '&status=' . urlencode($status) : ''; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><i class="fas fa-chevron-right"></i></a>
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

.title-cell img {
    width: 35px;
    height: 26px;
    object-fit: cover;
    border: 2px solid #FFC107;
}

.title-cell span {
    font-weight: 500;
    font-size: 0.8125rem;
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
}

.btn-icon:hover {
    border-color: #FFC107;
    background: #fff9e6;
    color: #000;
}

.btn-icon.btn-danger:hover {
    border-color: #dc3545;
    background: #f8d7da;
    color: #dc3545;
}

.empty-state {
    text-align: center;
    padding: 2rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 2.5rem;
    color: #dee2e6;
    margin-bottom: 0.75rem;
}

.empty-state p {
    margin: 0.5rem 0 0.75rem;
    font-size: 0.875rem;
}

.pagination-compact {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    border-top: 1px solid #dee2e6;
    font-size: 0.75rem;
}

.pagination {
    display: flex;
    gap: 0.25rem;
    align-items: center;
}

.pagination a {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #dee2e6;
    color: #495057;
    text-decoration: none;
    font-size: 0.75rem;
}

.pagination span {
    font-size: 0.75rem;
    color: #6c757d;
}

.badge {
    padding: 0.125rem 0.375rem;
    font-size: 0.6875rem;
    font-weight: 600;
    border: 2px solid;
}

.badge-info {
    background: #d1ecf1;
    border-color: #17a2b8;
    color: #0c5460;
}

.pagination a:hover {
    border-color: #FFC107;
    background: #fff9e6;
    color: #000;
}

.alert {
    padding: 0.625rem 0.75rem;
    margin-bottom: 0.75rem;
    border: 2px solid;
    font-size: 0.8125rem;
}

.alert-danger {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
}

.alert-success {
    background: #d4edda;
    border-color: #28a745;
    color: #155724;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .content-header-compact {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .header-actions {
        flex-direction: column;
    }
    
    .search-inline {
        flex-direction: column;
    }
    
    .form-control-sm {
        width: 100%;
    }
    
    .table-compact thead {
        display: none;
    }
    
    .table-compact,
    .table-compact tbody,
    .table-compact tr,
    .table-compact td {
        display: block;
        width: 100%;
    }
    
    .table-compact tr {
        margin-bottom: 0.75rem;
        border: 2px solid #dee2e6;
        background: #fff;
    }
    
    .table-compact td {
        text-align: right;
        padding: 0.375rem 0.5rem;
        position: relative;
        padding-left: 50%;
        border-bottom: 1px solid #eee;
    }
    
    .table-compact td:last-child {
        border-bottom: 0;
    }
    
    .table-compact td:before {
        content: attr(data-label);
        position: absolute;
        left: 0.5rem;
        width: 45%;
        padding-right: 0.5rem;
        font-weight: 600;
        text-align: left;
        font-size: 0.75rem;
        color: #495057;
        text-transform: uppercase;
    }
    
    .title-cell {
        justify-content: flex-end;
    }
    
    .actions {
        justify-content: flex-end;
    }
    
    .pagination-compact {
        flex-direction: column;
        gap: 0.5rem;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

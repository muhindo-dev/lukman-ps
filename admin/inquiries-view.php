<?php
/**
 * Inquiries - View & Reply
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'inquiries';
$pageTitle = 'View Inquiry';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid inquiry ID.'];
    header('Location: inquiries.php');
    exit;
}

$pdo = getDBConnection();

// Get inquiry
$inquiry = getRecordById('contact_inquiries', $id);

if (!$inquiry) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Inquiry not found.'];
    header('Location: inquiries.php');
    exit;
}

// Mark as read if new
if ($inquiry['status'] == 'new') {
    updateRecord('contact_inquiries', $id, ['status' => 'read']);
    $inquiry['status'] = 'read';
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $newStatus = $_POST['status'] ?? 'read';
        $result = updateRecord('contact_inquiries', $id, ['status' => $newStatus]);
        
        if ($result) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Status updated successfully.'];
        } else {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to update status.'];
        }
        header('Location: inquiries-view.php?id=' . $id);
        exit;
    }
    
    if ($action === 'send_reply') {
        $replyMessage = trim($_POST['reply_message'] ?? '');
        
        if (empty($replyMessage)) {
            $_SESSION['alert'] = ['type' => 'error', 'message' => 'Reply message cannot be empty.'];
        } else {
            // Update the record
            $result = updateRecord('contact_inquiries', $id, [
                'reply_message' => $replyMessage,
                'replied_at' => date('Y-m-d H:i:s'),
                'replied_by' => $currentAdmin['id'] ?? 1,
                'status' => 'replied'
            ]);
            
            if ($result) {
                // Send email reply
                $to = $inquiry['email'];
                $subject = "Re: " . $inquiry['subject'];
                $headers = "From: Lukman Primary School <noreply@lukmanps.ac.ug>\r\n";
                $headers .= "Reply-To: info@lukmanps.ac.ug\r\n";
                $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                
                $emailBody = "
                <html>
                <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                    <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                        <div style='background: #00723F; padding: 20px; text-align: center;'>
                            <h1 style='margin: 0; color: #fff;'>Lukman Primary School</h1>
                        </div>
                        <div style='padding: 20px; background: #fff; border: 1px solid #ddd;'>
                            <p>Dear {$inquiry['name']},</p>
                            <p>Thank you for contacting us. Here is our response to your inquiry:</p>
                            <div style='background: #f9f9f9; padding: 15px; margin: 15px 0; border-left: 4px solid #00723F;'>
                                " . nl2br(htmlspecialchars($replyMessage)) . "
                            </div>
                            <hr style='border: none; border-top: 1px solid #ddd; margin: 20px 0;'>
                            <p style='color: #666; font-size: 12px;'><strong>Your original message:</strong></p>
                            <p style='color: #666; font-size: 12px;'>" . nl2br(htmlspecialchars($inquiry['message'])) . "</p>
                        </div>
                        <div style='padding: 15px; background: #00723F; color: #fff; text-align: center; font-size: 12px;'>
                            <p style='margin: 0;'>Lukman Primary School - Excellence in Islamic &amp; UNC Education</p>
                        </div>
                    </div>
                </body>
                </html>";
                
                @mail($to, $subject, $emailBody, $headers);
                
                $_SESSION['alert'] = ['type' => 'success', 'message' => 'Reply sent successfully.'];
            } else {
                $_SESSION['alert'] = ['type' => 'error', 'message' => 'Failed to save reply.'];
            }
        }
        header('Location: inquiries-view.php?id=' . $id);
        exit;
    }
}

// Refresh inquiry data
$inquiry = getRecordById('contact_inquiries', $id);

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
        <h1><i class="fas fa-envelope-open-text"></i> View Inquiry</h1>
        <a href="inquiries.php" class="btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <div class="grid-layout">
        <div class="grid-main">
            <!-- Inquiry Details -->
            <div class="card card-compact">
                <div class="card-header">
                    <h3><i class="fas fa-message"></i> Inquiry Details</h3>
                    <?php echo getInquiryStatusBadge($inquiry['status'] ?? 'new'); ?>
                </div>
                <div class="card-body">
                    <div class="inquiry-meta">
                        <div class="meta-item">
                            <label>From</label>
                            <span><strong><?php echo htmlspecialchars($inquiry['name']); ?></strong></span>
                        </div>
                        <div class="meta-item">
                            <label>Email</label>
                            <span><a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>"><?php echo htmlspecialchars($inquiry['email']); ?></a></span>
                        </div>
                        <?php if ($inquiry['phone']): ?>
                        <div class="meta-item">
                            <label>Phone</label>
                            <span><a href="tel:<?php echo htmlspecialchars($inquiry['phone']); ?>"><?php echo htmlspecialchars($inquiry['phone']); ?></a></span>
                        </div>
                        <?php endif; ?>
                        <div class="meta-item">
                            <label>Subject</label>
                            <span><?php echo htmlspecialchars($inquiry['subject']); ?></span>
                        </div>
                        <div class="meta-item">
                            <label>Date</label>
                            <span><?php echo date('F d, Y \a\t h:i A', strtotime($inquiry['created_at'])); ?></span>
                        </div>
                        <?php if ($inquiry['ip_address']): ?>
                        <div class="meta-item">
                            <label>IP Address</label>
                            <span><?php echo htmlspecialchars($inquiry['ip_address']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="message-box">
                        <label>Message</label>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($inquiry['message'])); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reply Section -->
            <?php if ($inquiry['status'] !== 'replied'): ?>
            <div class="card card-compact">
                <div class="card-header">
                    <h3><i class="fas fa-reply"></i> Send Reply</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="send_reply">
                        <div class="form-group">
                            <label for="reply_message">Reply Message <span class="required">*</span></label>
                            <textarea 
                                id="reply_message" 
                                name="reply_message" 
                                rows="6" 
                                class="form-control"
                                placeholder="Type your reply here..."
                                required
                            ></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-sm btn-primary">
                                <i class="fas fa-paper-plane"></i> Send Reply
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card card-compact">
                <div class="card-header">
                    <h3><i class="fas fa-check-circle"></i> Reply Sent</h3>
                </div>
                <div class="card-body">
                    <div class="reply-info">
                        <small class="text-muted">Replied on <?php echo date('F d, Y \a\t h:i A', strtotime($inquiry['replied_at'])); ?></small>
                    </div>
                    <div class="message-box reply">
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($inquiry['reply_message'])); ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="grid-sidebar">
            <!-- Status Update -->
            <div class="card card-compact">
                <div class="card-header">
                    <h3><i class="fas fa-cog"></i> Status</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_status">
                        <div class="form-group">
                            <select name="status" class="form-control">
                                <option value="new" <?php echo ($inquiry['status'] == 'new') ? 'selected' : ''; ?>>New</option>
                                <option value="read" <?php echo ($inquiry['status'] == 'read') ? 'selected' : ''; ?>>Read</option>
                                <option value="replied" <?php echo ($inquiry['status'] == 'replied') ? 'selected' : ''; ?>>Replied</option>
                                <option value="archived" <?php echo ($inquiry['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-sm btn-secondary btn-block">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card card-compact">
                <div class="card-header">
                    <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>" class="btn-sm btn-secondary btn-block">
                            <i class="fas fa-envelope"></i> Email Directly
                        </a>
                        <?php if ($inquiry['phone']): ?>
                        <a href="tel:<?php echo htmlspecialchars($inquiry['phone']); ?>" class="btn-sm btn-secondary btn-block">
                            <i class="fas fa-phone"></i> Call
                        </a>
                        <?php endif; ?>
                        <a href="inquiries-delete.php?id=<?php echo $inquiry['id']; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-sm btn-danger btn-block" onclick="return confirm('Delete this inquiry?');">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
}

.grid-layout {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 1rem;
}

.grid-main {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.card {
    background: #fff;
    border: 2px solid #000;
}

.card-compact .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    border-bottom: 2px solid #000;
}

.card-compact .card-header h3 {
    margin: 0;
    font-size: 0.875rem;
    font-weight: 700;
}

.card-compact .card-body {
    padding: 1rem;
}

.inquiry-meta {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.meta-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.meta-item label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
}

.meta-item span {
    font-size: 0.875rem;
}

.meta-item a {
    color: #000;
    text-decoration: none;
}

.meta-item a:hover {
    color: #FFC107;
    text-decoration: underline;
}

.message-box {
    margin-top: 1rem;
}

.message-box label {
    display: block;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.message-content {
    background: #f8f9fa;
    padding: 1rem;
    border: 2px solid #dee2e6;
    line-height: 1.6;
    font-size: 0.9375rem;
}

.message-box.reply .message-content {
    background: #e8f5e9;
    border-color: #28a745;
}

.reply-info {
    margin-bottom: 0.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 0.375rem;
    font-size: 0.875rem;
}

.form-control {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    border: 2px solid #dee2e6;
    outline: none;
    font-family: inherit;
}

.form-control:focus {
    border-color: #FFC107;
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.form-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    cursor: pointer;
}

.btn-primary {
    background: #FFC107;
    border-color: #000;
    color: #000;
}

.btn-primary:hover {
    background: #e0a800;
}

.btn-secondary {
    background: #f8f9fa;
    border-color: #000;
    color: #000;
}

.btn-secondary:hover {
    background: #e9ecef;
}

.btn-danger {
    background: #fff;
    border-color: #dc3545;
    color: #dc3545;
}

.btn-danger:hover {
    background: #dc3545;
    color: #fff;
}

.btn-block {
    width: 100%;
    justify-content: center;
}

.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
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

.required {
    color: #dc3545;
}

.text-muted {
    color: #6c757d;
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
    .grid-layout {
        grid-template-columns: 1fr;
    }
    
    .inquiry-meta {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

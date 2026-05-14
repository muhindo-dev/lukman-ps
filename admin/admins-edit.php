<?php
/**
 * Admin Users - Edit
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'admins';
$pageTitle = 'Edit Admin User';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid admin ID.'];
    header('Location: admins.php');
    exit;
}

$admin = getRecordById('admin_users', $id);

if (!$admin) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Admin user not found.'];
    header('Location: admins.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    
    // Validation
    if (empty($username)) {
        $errors[] = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores.';
    }
    
    // Only validate password if provided
    if (!empty($password)) {
        if (strlen($password) < 6) {
            $errors[] = 'Password must be at least 6 characters.';
        }
        if ($password !== $confirmPassword) {
            $errors[] = 'Passwords do not match.';
        }
    }
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    
    // Check for duplicate username/email (excluding current record)
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already exists.';
        }
        
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        }
    }
    
    // Handle avatar upload
    $avatarPath = $admin['avatar'] ?? null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $fileName = $_FILES['avatar']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExt, $allowedTypes)) {
            $errors[] = 'Invalid image format. Allowed: JPG, PNG, GIF.';
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $errors[] = 'Image size must be less than 2MB.';
        } else {
            $uploadDir = '../uploads/admins/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $newFileName = 'admin_' . time() . '_' . uniqid() . '.' . $fileExt;
            $uploadPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadPath)) {
                // Delete old avatar
                if (!empty($admin['avatar']) && file_exists('../uploads/' . $admin['avatar'])) {
                    unlink('../uploads/' . $admin['avatar']);
                }
                $avatarPath = 'admins/' . $newFileName;
            } else {
                $errors[] = 'Failed to upload avatar.';
            }
        }
    }
    
    // Update record
    if (empty($errors)) {
        // Get actual columns from database to avoid errors
        $pdo = getDBConnection();
        $stmt = $pdo->query("DESCRIBE admin_users");
        $existingColumns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
        
        // Build data array with only existing columns
        $data = [];
        
        if (in_array('username', $existingColumns)) {
            $data['username'] = $username;
        }
        if (in_array('full_name', $existingColumns)) {
            $data['full_name'] = $fullName;
        }
        if (in_array('email', $existingColumns)) {
            $data['email'] = $email;
        }
        if (in_array('phone', $existingColumns)) {
            $data['phone'] = $phone ?: null;
        }
        if (in_array('status', $existingColumns)) {
            $data['status'] = $status;
        }
        if (in_array('avatar', $existingColumns)) {
            $data['avatar'] = $avatarPath;
        }
        
        // Only update password if provided
        if (!empty($password) && in_array('password', $existingColumns)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $result = updateRecord('admin_users', $id, $data);
        
        if ($result) {
            // Update session if editing own profile
            if ($id == $currentAdmin['id']) {
                $_SESSION['admin_name'] = $fullName;
                $_SESSION['admin_username'] = $username;
            }
            
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Admin user updated successfully.'];
            header('Location: admins.php');
            exit;
        } else {
            global $lastUpdateError;
            $errors[] = 'Failed to update admin user.' . (!empty($lastUpdateError) ? ' Error: ' . $lastUpdateError : '');
        }
    }
    
    // Update admin array with posted data for form
    $admin = array_merge($admin, [
        'username' => $username,
        'full_name' => $fullName,
        'email' => $email,
        'phone' => $phone,
        'status' => $status
    ]);
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-user-edit"></i> Edit Admin User</h1>
        <a href="admins.php" class="btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="grid-layout">
            <div class="grid-main">
                <!-- Account Information -->
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-user"></i> Account Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="username">Username <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($admin['username']); ?>"
                                    placeholder="Enter username"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($admin['email']); ?>"
                                    placeholder="Enter email"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">New Password</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-control"
                                    placeholder="Leave blank to keep current"
                                >
                                <small class="form-text">Minimum 6 characters. Leave blank to keep current password.</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    class="form-control"
                                    placeholder="Confirm new password"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-id-card"></i> Personal Information</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">Full Name <span class="required">*</span></label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($admin['full_name']); ?>"
                                    placeholder="Enter full name"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input 
                                    type="text" 
                                    id="phone" 
                                    name="phone" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($admin['phone'] ?? ''); ?>"
                                    placeholder="Enter phone number"
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Info -->
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-info-circle"></i> Account Info</h3>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Created</label>
                                <span><?php echo date('M d, Y H:i', strtotime($admin['created_at'])); ?></span>
                            </div>
                            <div class="info-item">
                                <label>Last Login</label>
                                <span><?php echo $admin['last_login'] ? date('M d, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid-sidebar">
                <!-- Role & Status -->
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-cog"></i> Settings</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" class="form-control" <?php echo ($id == $currentAdmin['id']) ? 'disabled' : ''; ?>>
                                <option value="active" <?php echo ($admin['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($admin['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                            <?php if ($id == $currentAdmin['id']): ?>
                                <input type="hidden" name="status" value="active">
                                <small class="form-text">Cannot deactivate your own account</small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Avatar -->
                <div class="card card-compact">
                    <div class="card-header">
                        <h3><i class="fas fa-image"></i> Avatar</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <div class="avatar-preview" id="avatarPreview">
                                <?php if (!empty($admin['avatar'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($admin['avatar']); ?>" alt="">
                                <?php else: ?>
                                    <i class="fas fa-user"></i>
                                <?php endif; ?>
                            </div>
                            <input 
                                type="file" 
                                id="avatar" 
                                name="avatar" 
                                class="form-control"
                                accept="image/*"
                                onchange="previewAvatar(this)"
                            >
                            <small class="form-text">JPG, PNG, GIF. Max 2MB.</small>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card card-compact">
                    <div class="card-body">
                        <button type="submit" class="btn-sm btn-primary btn-block">
                            <i class="fas fa-save"></i> Update Admin
                        </button>
                        <a href="admins.php" class="btn-sm btn-secondary btn-block">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewAvatar(input) {
    const preview = document.getElementById('avatarPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

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

.grid-sidebar {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.card {
    background: #fff;
    border: 2px solid #000;
}

.card-compact .card-header {
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group:last-child {
    margin-bottom: 0;
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
}

.form-control:focus {
    border-color: #FFC107;
}

.form-control:disabled {
    background: #e9ecef;
    cursor: not-allowed;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6c757d;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: #6c757d;
    font-weight: 600;
}

.info-item span {
    font-size: 0.875rem;
}

.avatar-preview {
    width: 100px;
    height: 100px;
    border: 2px solid #000;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    background: #f8f9fa;
    overflow: hidden;
}

.avatar-preview i {
    font-size: 3rem;
    color: #dee2e6;
}

.avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

.btn-secondary {
    background: #f8f9fa;
    border-color: #000;
    color: #000;
}

.btn-block {
    width: 100%;
    justify-content: center;
    margin-bottom: 0.5rem;
}

.btn-block:last-child {
    margin-bottom: 0;
}

.required {
    color: #dc3545;
}

.alert {
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border: 2px solid;
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
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>

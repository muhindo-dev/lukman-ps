<?php
/**
 * Admin Users - Add New
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'admins';
$pageTitle = 'Add Admin User';

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
    
    if (empty($password)) {
        $errors[] = 'Password is required.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match.';
    }
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    }
    
    // Check for duplicate username/email
    if (empty($errors)) {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $errors[] = 'Username already exists.';
        }
        
        $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Email already exists.';
        }
    }
    
    // Handle avatar upload
    $avatarPath = null;
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
                $avatarPath = 'admins/' . $newFileName;
            } else {
                $errors[] = 'Failed to upload avatar.';
            }
        }
    }
    
    // Insert record
    if (empty($errors)) {
        $data = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone ?: null,
            'status' => $status,
            'avatar' => $avatarPath
        ];
        
        $adminId = insertRecord('admin_users', $data);
        
        if ($adminId) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Admin user created successfully.'];
            header('Location: admins.php');
            exit;
        } else {
            $errors[] = 'Failed to create admin user.';
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-user-plus"></i> Add Admin User</h1>
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
                                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                    placeholder="Enter username"
                                    required
                                >
                                <small class="form-text">Letters, numbers, and underscores only</small>
                            </div>
                            <div class="form-group">
                                <label for="email">Email <span class="required">*</span></label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-control"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                    placeholder="Enter email"
                                    required
                                >
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password <span class="required">*</span></label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-control"
                                    placeholder="Enter password"
                                    required
                                >
                                <small class="form-text">Minimum 6 characters</small>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    class="form-control"
                                    placeholder="Confirm password"
                                    required
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
                                    value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>"
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
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                    placeholder="Enter phone number"
                                >
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
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?php echo (($_POST['status'] ?? 'active') == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo (($_POST['status'] ?? '') == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
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
                                <i class="fas fa-user"></i>
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
                            <i class="fas fa-save"></i> Create Admin
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

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6c757d;
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
}
</style>

<?php include 'includes/footer.php'; ?>

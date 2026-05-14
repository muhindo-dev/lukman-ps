<?php
/**
 * Our Team - Edit Member
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'team';
$pageTitle = 'Edit Team Member';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$member = getRecordById('team_members', $id);

if (!$member) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Team member not found.'];
    header('Location: team.php');
    exit;
}

$error = '';
$departments = ['Leadership', 'Management', 'Program Staff', 'Administrative', 'Volunteers', 'Advisors'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($name)) {
        $error = 'Name is required.';
    } elseif (empty($position)) {
        $error = 'Position is required.';
    } elseif (empty($department)) {
        $error = 'Please select a department.';
    }
    
    // Handle photo upload
    $photoPath = $member['photo'];
    if (!$error && isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK && $_FILES['photo']['size'] > 0) {
        $uploadDir = '../uploads/team/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
        
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $newName = uniqid('team_') . '.' . $ext;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadDir . $newName)) {
                if ($member['photo'] && file_exists('../uploads/' . $member['photo'])) {
                    unlink('../uploads/' . $member['photo']);
                }
                $photoPath = 'team/' . $newName;
            }
        }
    }
    
    if (!$error) {
        $slug = $member['slug'];
        if ($name !== $member['name']) {
            $slug = generateSlug($name);
            $slug = ensureUniqueSlug('team_members', $slug, $id);
        }
        
        $data = [
            'name' => $name,
            'slug' => $slug,
            'position' => $position,
            'department' => $department,
            'photo' => $photoPath,
            'bio' => $bio,
            'email' => $email ?: null,
            'phone' => $phone ?: null,
            'sort_order' => $sortOrder,
            'is_featured' => $isFeatured,
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $result = updateRecord('team_members', $id, $data);
        
        if ($result) {
            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Team member updated successfully!'];
            header('Location: team.php');
            exit;
        } else {
            $error = 'Failed to update team member.';
        }
    }
    
    // Update for redisplay
    $member = array_merge($member, compact('name', 'position', 'department', 'bio', 'email', 'phone', 'status', 'sortOrder'));
    $member['is_featured'] = $isFeatured;
    $member['sort_order'] = $sortOrder;
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-user-edit"></i> Edit Team Member</h1>
        <a href="team.php" class="btn-sm btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-user"></i> Member Information</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="form-group mb-3">
                            <label class="form-label required">Full Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Enter full name..." required value="<?php echo htmlspecialchars($member['name']); ?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label required">Position / Role</label>
                            <input type="text" name="position" class="form-control" placeholder="e.g. Executive Director, Program Manager..." required value="<?php echo htmlspecialchars($member['position']); ?>">
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Biography</label>
                            <textarea name="bio" class="form-control" rows="5" placeholder="Brief introduction about this team member..."><?php echo htmlspecialchars($member['bio'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group col-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="email@example.com" value="<?php echo htmlspecialchars($member['email'] ?? ''); ?>">
                            </div>
                            <div class="form-group col-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control" placeholder="+256 700 000000" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-cog"></i> Settings</h4>
                    </div>
                    <div class="card-body-compact">
                        <div class="form-group mb-3">
                            <label class="form-label required">Department</label>
                            <select name="department" class="form-control" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept; ?>" <?php echo ($member['department'] == $dept) ? 'selected' : ''; ?>><?php echo $dept; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="active" <?php echo ($member['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($member['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?php echo $member['sort_order'] ?? 0; ?>" min="0">
                            <small class="form-text">Lower = appears first</small>
                        </div>
                        
                        <hr class="my-3">
                        
                        <div class="form-group mb-3">
                            <label class="form-label">Photo</label>
                            <?php if ($member['photo']): ?>
                                <div class="current-photo">
                                    <img src="../uploads/<?php echo htmlspecialchars($member['photo']); ?>" alt="">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="form-text"><?php echo $member['photo'] ? 'Upload new to replace' : 'JPG, PNG, GIF, WebP'; ?></small>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_featured" value="1" <?php echo $member['is_featured'] ? 'checked' : ''; ?>>
                                <span>Featured on Homepage</span>
                            </label>
                        </div>
                        
                        <hr class="my-3">
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-save"></i> Update Member
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.content-header-compact { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding-bottom: 0.375rem; border-bottom: 2px solid #FFC107; }
.content-header-compact h1 { font-size: 1.25rem; margin: 0; font-weight: 700; }
.btn-sm { padding: 0.25rem 0.625rem; font-size: 0.8125rem; border: 2px solid; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.25rem; cursor: pointer; background: #fff; }
.btn-sm.btn-secondary { background: #6c757d; border-color: #6c757d; color: #fff; }

.row { display: flex; gap: 1rem; }
.col-lg-8 { flex: 0 0 65%; }
.col-lg-4 { flex: 0 0 calc(35% - 1rem); }

.card-compact { background: #fff; border: 2px solid #dee2e6; margin-bottom: 1rem; }
.card-header-compact { padding: 0.5rem 0.75rem; border-bottom: 2px solid #FFC107; background: #f8f9fa; }
.card-header-compact h4 { margin: 0; font-size: 0.875rem; font-weight: 600; }
.card-body-compact { padding: 0.75rem; }

.form-group { margin-bottom: 0; }
.form-label { display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.25rem; color: #495057; }
.form-label.required::after { content: ' *'; color: #dc3545; }
.form-control { width: 100%; padding: 0.375rem 0.5rem; font-size: 0.8125rem; border: 2px solid #dee2e6; box-sizing: border-box; }
.form-control:focus { border-color: #FFC107; outline: none; }
.form-text { font-size: 0.6875rem; color: #6c757d; margin-top: 0.125rem; }

.form-row { display: flex; gap: 0.75rem; }
.col-6 { flex: 1; }

.current-photo { margin-bottom: 0.5rem; }
.current-photo img { width: 100%; height: 120px; object-fit: cover; border: 2px solid #FFC107; }

.checkbox-label { display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.8125rem; }
.checkbox-label input { width: 16px; height: 16px; }

.btn { padding: 0.5rem 1rem; font-size: 0.875rem; border: 2px solid; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; gap: 0.375rem; }
.btn-primary { background: #FFC107; border-color: #FFC107; color: #000; }
.btn-primary:hover { background: #000; color: #FFC107; }
.btn-block { width: 100%; }

.alert { padding: 0.5rem 0.75rem; margin-bottom: 0.5rem; border: 2px solid; font-size: 0.8125rem; }
.alert-danger { background: #f8d7da; border-color: #dc3545; color: #721c24; }

hr { border: none; border-top: 1px solid #dee2e6; }
.my-3 { margin: 0.75rem 0; }

@media (max-width: 900px) {
    .row { flex-direction: column; }
    .col-lg-8, .col-lg-4 { flex: 1; }
}
</style>

<?php include 'includes/footer.php'; ?>

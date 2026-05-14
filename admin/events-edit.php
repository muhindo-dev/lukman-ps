<?php
/**
 * Events Management - Edit Event
 */
require_once 'config/auth.php';
require_once 'config/crud-helper.php';

// Check authentication
requireAdmin();
checkSessionTimeout();

$currentAdmin = getCurrentAdmin();
$currentPage = 'events';
$pageTitle = 'Edit Event';

$error = '';
$success = '';

// Get event ID
$eventId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$eventId) {
    header('Location: events.php');
    exit;
}

// Get event data
$event = getRecordById('events', $eventId);

if (!$event) {
    header('Location: events.php');
    exit;
}

// Event types
$eventTypes = ['Fundraiser', 'Workshop', 'Community Service', 'Campaign', 'Awareness', 'Volunteer Drive'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $eventType = sanitizeInput($_POST['event_type'] ?? '');
    $description = $_POST['description'] ?? '';
    $startDatetime = $_POST['start_datetime'] ?? '';
    $endDatetime = $_POST['end_datetime'] ?? '';
    $venueName = sanitizeInput($_POST['venue_name'] ?? '');
    $venueAddress = sanitizeInput($_POST['venue_address'] ?? '');
    $registrationRequired = isset($_POST['registration_required']) ? 1 : 0;
    $registrationLink = sanitizeInput($_POST['registration_link'] ?? '');
    $maxCapacity = (int)($_POST['max_capacity'] ?? 0);
    $status = $_POST['status'] ?? 'upcoming';
    
    // Validation
    $validation = validateRequiredFields($_POST, ['title', 'description', 'event_type', 'start_datetime', 'end_datetime', 'venue_name']);
    
    if (!$validation['success']) {
        $error = $validation['message'];
    } elseif (strtotime($endDatetime) < strtotime($startDatetime)) {
        $error = 'End date must be after start date';
    } else {
        // Generate slug if title changed
        $slug = $event['slug'];
        if ($title !== $event['title']) {
            $slug = generateSlug($title);
            $slug = ensureUniqueSlug('events', $slug, $eventId);
        }
        
        // Handle event image upload
        $eventImage = $event['featured_image'];
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            // Delete old image
            if ($eventImage) {
                deleteUploadedFile('../uploads/' . $eventImage);
            }
            
            $uploadResult = uploadImage($_FILES['event_image'], '../uploads/events/');
            if ($uploadResult['success']) {
                $eventImage = 'events/' . $uploadResult['filename'];
            } else {
                $error = 'Image upload failed: ' . $uploadResult['message'];
            }
        }
        
        if (!$error) {
            // Prepare data
            $data = [
                'title'          => $title,
                'slug'           => $slug,
                'description'    => $description,
                'featured_image' => $eventImage,
                'event_type'     => $eventType,
                'event_date'     => $startDatetime,
                'end_date'       => $endDatetime,
                'location'       => $venueName,
                'status'         => $status,
                'updated_at'     => date('Y-m-d H:i:s')
            ];
            
            // Update record
            $updated = updateRecord('events', $eventId, $data);
            
            if ($updated) {
                logAdminActivity('update', 'events', $eventId);
                $success = 'Event updated successfully!';
                
                // Refresh event data
                $event = getRecordById('events', $eventId);
            } else {
                $error = 'Failed to update event. Please try again.';
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="admin-content">
    <div class="content-header-compact">
        <h1><i class="fas fa-edit"></i> Edit Event</h1>
        <div class="header-actions">
            <a href="events.php" class="btn-sm btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="events-delete.php?id=<?php echo $eventId; ?>&token=<?php echo generateCSRFToken(); ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this event?');">
                <i class="fas fa-trash"></i>
            </a>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" id="eventForm">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-file-alt"></i> Event Details</h4>
                    </div>
                    <div class="card-body-compact">
                        <!-- Title -->
                        <div class="form-group">
                            <label for="title" class="form-label required">Event Title</label>
                            <input 
                                type="text" 
                                id="title" 
                                name="title" 
                                class="form-control" 
                                placeholder="Enter event title..."
                                required
                                maxlength="200"
                                value="<?php echo htmlspecialchars($event['title']); ?>"
                            >
                            <small class="form-text">Maximum 200 characters</small>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label for="description" class="form-label required">Description</label>
                            <input type="hidden" id="description" name="description" value="<?php echo htmlspecialchars($event['description']); ?>">
                            <div id="editor-container" style="height: 350px; background: #fff;"><?php echo $event['description']; ?></div>
                        </div>

                        <!-- Venue Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="venue_name" class="form-label required">Venue Name</label>
                                    <input 
                                        type="text" 
                                        id="venue_name" 
                                        name="venue_name" 
                                        class="form-control" 
                                        placeholder="e.g., Community Center"
                                        required
                                        maxlength="200"
                                        value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="venue_address" class="form-label">Venue Address</label>
                                    <input 
                                        type="text" 
                                        id="venue_address" 
                                        name="venue_address" 
                                        class="form-control" 
                                        placeholder="Full address (optional)"
                                        value="<?php echo htmlspecialchars($event['location'] ?? ''); ?>"
                                    >
                                </div>
                            </div>
                        </div>

                        <!-- Date & Time -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_datetime" class="form-label required">Start Date & Time</label>
                                    <input 
                                        type="datetime-local" 
                                        id="start_datetime" 
                                        name="start_datetime" 
                                        class="form-control"
                                        required
                                        value="<?php echo date('Y-m-d\TH:i', strtotime($event['event_date'])); ?>"
                                    >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_datetime" class="form-label required">End Date & Time</label>
                                    <input 
                                        type="datetime-local" 
                                        id="end_datetime" 
                                        name="end_datetime" 
                                        class="form-control"
                                        required
                                        value="<?php echo $event['end_date'] ? date('Y-m-d\TH:i', strtotime($event['end_date'])) : ''; ?>"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Settings Card (Combined) -->
                <div class="card-compact">
                    <div class="card-header-compact">
                        <h4><i class="fas fa-cog"></i> Event Settings</h4>
                    </div>
                    <div class="card-body-compact">
                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label required">Status</label>
                            <select id="status" name="status" class="form-control" required>
                                <option value="upcoming" <?php echo ($event['status'] == 'upcoming') ? 'selected' : ''; ?>>Upcoming</option>
                                <option value="ongoing" <?php echo ($event['status'] == 'ongoing') ? 'selected' : ''; ?>>Ongoing</option>
                                <option value="completed" <?php echo ($event['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="cancelled" <?php echo ($event['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>

                        <hr class="my-3">

                        <!-- Event Type -->
                        <div class="form-group">
                            <label for="event_type" class="form-label required">Event Type</label>
                            <select id="event_type" name="event_type" class="form-control" required>
                                <option value="">Select Type</option>
                                <?php foreach ($eventTypes as $type): ?>
                                    <option value="<?php echo $type; ?>" <?php echo ($event['event_type'] == $type) ? 'selected' : ''; ?>>
                                        <?php echo $type; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <hr class="my-3">

                        <!-- Registration -->
                        <div class="form-group">
                            <label class="form-label">Registration</label>
                            <div class="form-check">
                                <input 
                                    type="checkbox" 
                                    id="registration_required" 
                                    name="registration_required" 
                                    class="form-check-input"
                                    <?php echo $event['registration_required'] ? 'checked' : ''; ?>
                                >
                                <label for="registration_required" class="form-check-label">Registration Required</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="registration_link" class="form-label">Registration Link</label>
                            <input 
                                type="url" 
                                id="registration_link" 
                                name="registration_link" 
                                class="form-control" 
                                placeholder="https://..."
                                value="<?php echo htmlspecialchars($event['registration_link']); ?>"
                            >
                            <small class="form-text">External registration/ticket URL</small>
                        </div>

                        <div class="form-group">
                            <label for="max_capacity" class="form-label">Max Capacity</label>
                            <input 
                                type="number" 
                                id="max_capacity" 
                                name="max_capacity" 
                                class="form-control" 
                                placeholder="0 = unlimited"
                                min="0"
                                value="<?php echo htmlspecialchars($event['max_capacity']); ?>"
                            >
                            <small class="form-text">Current: <?php echo $event['current_registrations']; ?> registered</small>
                        </div>

                        <hr class="my-3">

                        <!-- Event Image -->
                        <?php if ($event['featured_image']): ?>
                            <div class="form-group">
                                <label class="form-label">Current Image:</label>
                                <img src="../uploads/<?php echo htmlspecialchars($event['featured_image']); ?>" alt="" style="max-width: 100%; height: auto; border: 2px solid #dee2e6; margin-bottom: 0.5rem;">
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="event_image" class="form-label">Event Image</label>
                            <input 
                                type="file" 
                                id="event_image" 
                                name="event_image" 
                                class="form-control" 
                                accept="image/*"
                            >
                            <small class="form-text"><?php echo $event['featured_image'] ? 'Upload new or keep current. ' : ''; ?>1200x630px. Max 5MB</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <hr class="my-3">

                        <!-- Event Info -->
                        <div class="form-group">
                            <div class="post-meta">
                                <small><strong>Created:</strong> <?php echo formatDateTime($event['created_at']); ?></small><br>
                                <small><strong>Views:</strong> <?php echo number_format($event['views']); ?> | <strong>Slug:</strong> <?php echo htmlspecialchars($event['slug']); ?></small>
                            </div>
                        </div>

                        <hr class="my-3">

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Event
                            </button>
                            <a href="events.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Quill.js Editor -->
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill editor
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        placeholder: 'Enter event description...',
        modules: {
            toolbar: [
                [{ 'header': [2, 3, 4, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        }
    });

    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        // Get Quill content and update hidden input
        var description = quill.root.innerHTML;
        document.getElementById('description').value = description;
        
        // Check if description is empty (only has empty tags)
        var textContent = quill.getText().trim();
        if (!textContent || textContent === '') {
            e.preventDefault();
            alert('Please enter the event description');
            quill.focus();
            return false;
        }

        // Validate dates
        var startDate = new Date(document.getElementById('start_datetime').value);
        var endDate = new Date(document.getElementById('end_datetime').value);
        
        if (endDate < startDate) {
            e.preventDefault();
            alert('End date must be after start date');
            return false;
        }
    });

    // Image preview
    document.getElementById('event_image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" style="max-width: 100%; height: auto; border: 2px solid #dee2e6;">';
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = '';
        }
    });
});
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

.header-actions {
    display: flex;
    gap: 0.375rem;
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

.btn-sm.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.btn-sm.btn-danger {
    background: #dc3545;
    border-color: #dc3545;
    color: #fff;
}

.card-compact {
    background: #fff;
    border: 2px solid #dee2e6;
    margin-bottom: 0.75rem;
}

.card-header-compact {
    padding: 0.5rem 0.75rem;
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.card-header-compact h4 {
    margin: 0;
    font-size: 0.9375rem;
    font-weight: 600;
}

.card-body-compact {
    padding: 0.75rem;
}

.form-group {
    margin-bottom: 0.75rem;
}

.form-label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: 600;
    font-size: 0.8125rem;
}

.form-label.required::after {
    content: ' *';
    color: #dc3545;
}

.form-control {
    width: 100%;
    padding: 0.375rem 0.5rem;
    border: 2px solid #dee2e6;
    font-size: 0.8125rem;
}

.form-control:focus {
    outline: none;
    border-color: #FFC107;
    background: #fff9e6;
}

.form-text {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #6c757d;
}

.form-check {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.form-check-input {
    width: 16px;
    height: 16px;
    border: 2px solid #dee2e6;
    cursor: pointer;
}

.form-check-label {
    font-size: 0.8125rem;
    cursor: pointer;
}

.btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    cursor: pointer;
}

.btn-primary {
    background: #FFC107;
    border-color: #FFC107;
    color: #000;
}

.btn-primary:hover {
    background: #000;
    color: #FFC107;
}

.btn-secondary {
    background: #6c757d;
    border-color: #6c757d;
    color: #fff;
}

.d-grid {
    display: grid;
}

.gap-2 {
    gap: 0.5rem;
}

.my-3 {
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}

.mt-2 {
    margin-top: 0.5rem;
}

hr {
    border: 0;
    border-top: 1px solid #dee2e6;
    margin: 0.75rem 0;
}

.post-meta {
    font-size: 0.75rem;
    color: #6c757d;
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
</style>

<?php include 'includes/footer.php'; ?>

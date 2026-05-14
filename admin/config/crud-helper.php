<?php
/**
 * Reusable CRUD Helper Functions
 * Provides common database operations for all modules
 */

/**
 * Get all records from a table with optional filters
 * @param string $table Table name
 * @param array $filters Associative array of column => value filters
 * @param string $orderBy Order by clause (e.g., "created_at DESC")
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Array of records
 */
function getAllRecords($table, $filters = [], $orderBy = 'id DESC', $limit = null, $offset = 0) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return [];
        
        $sql = "SELECT * FROM $table";
        $params = [];
        
        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $column => $value) {
                $conditions[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Get all records error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get a single record by ID
 * @param string $table Table name
 * @param int $id Record ID
 * @return array|null Record or null if not found
 */
function getRecordById($table, $id) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return null;
        
        $stmt = $pdo->prepare("SELECT * FROM $table WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        
    } catch (PDOException $e) {
        error_log("Get record by ID error: " . $e->getMessage());
        return null;
    }
}

/**
 * Insert a new record
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|bool Inserted ID on success, false on failure
 */
function insertRecord($table, $data) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return false;
        
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $sql = "INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array_values($data));
        
        return $result ? $pdo->lastInsertId() : false;
        
    } catch (PDOException $e) {
        error_log("Insert record error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update a record by ID
 * @param string $table Table name
 * @param int $id Record ID
 * @param array $data Associative array of column => value
 * @return bool Success status
 */
function updateRecord($table, $id, $data) {
    global $lastUpdateError;
    try {
        $pdo = getDBConnection();
        if (!$pdo) {
            $lastUpdateError = "Database connection failed";
            return false;
        }
        
        $sets = [];
        $values = [];
        
        foreach ($data as $column => $value) {
            $sets[] = "`$column` = ?";
            $values[] = $value;
        }
        
        $values[] = $id;
        
        $sql = "UPDATE `$table` SET " . implode(', ', $sets) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);
        
        if (!$result) {
            $lastUpdateError = implode(", ", $stmt->errorInfo());
        }
        
        return $result;
        
    } catch (PDOException $e) {
        $lastUpdateError = $e->getMessage();
        error_log("Update record error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a record by ID
 * @param string $table Table name
 * @param int $id Record ID
 * @return bool Success status
 */
function deleteRecord($table, $id) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return false;
        
        $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
        return $stmt->execute([$id]);
        
    } catch (PDOException $e) {
        error_log("Delete record error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get total count of records with optional filters
 * @param string $table Table name
 * @param array $filters Associative array of column => value filters
 * @return int Total count
 */
function getRecordCount($table, $filters = []) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return 0;
        
        $sql = "SELECT COUNT(*) FROM $table";
        $params = [];
        
        if (!empty($filters)) {
            $conditions = [];
            foreach ($filters as $column => $value) {
                $conditions[] = "$column = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Get record count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Generate a URL-friendly slug from a string
 * @param string $text Text to convert
 * @return string Slug
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    $text = trim($text, '-');
    return $text;
}

/**
 * Ensure unique slug for a table
 * @param string $table Table name
 * @param string $slug Base slug
 * @param int $excludeId ID to exclude (for updates)
 * @return string Unique slug
 */
function ensureUniqueSlug($table, $slug, $excludeId = null) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return $slug;
        
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = "SELECT COUNT(*) FROM $table WHERE slug = ?";
            $params = [$slug];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            if ($stmt->fetchColumn() == 0) {
                return $slug;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
    } catch (PDOException $e) {
        error_log("Ensure unique slug error: " . $e->getMessage());
        return $slug . '-' . time();
    }
}

/**
 * Upload and process an image file
 * @param array $file File from $_FILES
 * @param string $uploadDir Upload directory path
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array Result with success status and filename/message
 */
function uploadImage($file, $uploadDir = '../uploads/', $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], $maxSize = 5242880) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload'];
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload error occurred'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds maximum allowed (' . ($maxSize / 1048576) . 'MB)'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Create upload directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => false, 'message' => 'Failed to move uploaded file'];
    }
    
    return ['success' => true, 'filename' => $filename, 'path' => $filepath];
}

/**
 * Delete an uploaded file
 * @param string $filepath File path
 * @return bool Success status
 */
function deleteUploadedFile($filepath) {
    if (file_exists($filepath) && is_file($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Get pagination data
 * @param int $totalRecords Total number of records
 * @param int $currentPage Current page number
 * @param int $perPage Records per page
 * @return array Pagination data
 */
function getPaginationData($totalRecords, $currentPage = 1, $perPage = 20) {
    $totalPages = ceil($totalRecords / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_records' => $totalRecords,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Render pagination links
 * @param array $paginationData Pagination data from getPaginationData()
 * @param string $baseUrl Base URL for pagination links
 * @return string HTML pagination
 */
function renderPagination($paginationData, $baseUrl) {
    if ($paginationData['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<nav><ul class="pagination">';
    
    // Previous button
    if ($paginationData['has_previous']) {
        $prevPage = $paginationData['current_page'] - 1;
        $html .= '<li><a href="' . $baseUrl . '&page=' . $prevPage . '">Previous</a></li>';
    }
    
    // Page numbers
    for ($i = 1; $i <= $paginationData['total_pages']; $i++) {
        $active = $i == $paginationData['current_page'] ? 'active' : '';
        $html .= '<li class="' . $active . '"><a href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($paginationData['has_next']) {
        $nextPage = $paginationData['current_page'] + 1;
        $html .= '<li><a href="' . $baseUrl . '&page=' . $nextPage . '">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Validate required fields
 * @param array $data Data to validate
 * @param array $requiredFields Array of required field names
 * @return array Result with success status and message
 */
function validateRequiredFields($data, $requiredFields) {
    $missingFields = [];
    
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $missingFields[] = ucfirst(str_replace('_', ' ', $field));
        }
    }
    
    if (!empty($missingFields)) {
        return [
            'success' => false,
            'message' => 'Missing required fields: ' . implode(', ', $missingFields)
        ];
    }
    
    return ['success' => true];
}

/**
 * Format date for display
 * @param string $date Date string
 * @param string $format Output format
 * @return string Formatted date
 */
function formatDate($date, $format = 'M d, Y') {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format date and time for display
 * @param string $datetime DateTime string
 * @param string $format Output format
 * @return string Formatted datetime
 */
function formatDateTime($datetime, $format = 'M d, Y g:i A') {
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Truncate text to specified length
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to append if truncated
 * @return string Truncated text
 */
function truncateText($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge HTML
 * @param string $status Status value
 * @param array $statusMap Map of status => class
 * @return string Badge HTML
 */
function getStatusBadge($status, $statusMap = []) {
    $defaultMap = [
        'active' => 'success',
        'inactive' => 'secondary',
        'published' => 'success',
        'draft' => 'warning',
        'archived' => 'secondary',
        'pending' => 'warning',
        'approved' => 'success',
        'rejected' => 'danger',
        'completed' => 'success',
        'ongoing' => 'primary',
        'upcoming' => 'info',
        'cancelled' => 'danger',
        'paused' => 'warning'
    ];
    
    $map = array_merge($defaultMap, $statusMap);
    $class = $map[$status] ?? 'secondary';
    
    return '<span class="badge badge-' . $class . '">' . ucfirst($status) . '</span>';
}

/**
 * Search records with LIKE query
 * @param string $table Table name
 * @param array $searchFields Fields to search in
 * @param string $searchTerm Search term
 * @param array $additionalFilters Additional WHERE conditions
 * @param string $orderBy Order by clause
 * @param int $limit Limit number of results
 * @param int $offset Offset for pagination
 * @return array Array of records
 */
function searchRecords($table, $searchFields, $searchTerm, $additionalFilters = [], $orderBy = 'id DESC', $limit = null, $offset = 0) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return [];
        
        $sql = "SELECT * FROM $table WHERE ";
        $conditions = [];
        $params = [];
        
        // Search conditions
        foreach ($searchFields as $field) {
            $conditions[] = "$field LIKE ?";
            $params[] = "%$searchTerm%";
        }
        $sql .= "(" . implode(' OR ', $conditions) . ")";
        
        // Additional filters
        if (!empty($additionalFilters)) {
            foreach ($additionalFilters as $column => $value) {
                $sql .= " AND $column = ?";
                $params[] = $value;
            }
        }
        
        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Search records error: " . $e->getMessage());
        return [];
    }
}

/**
 * Get count for search results
 * @param string $table Table name
 * @param array $searchFields Fields to search in
 * @param string $searchTerm Search term
 * @param array $additionalFilters Additional WHERE conditions
 * @return int Total count
 */
function getSearchCount($table, $searchFields, $searchTerm, $additionalFilters = []) {
    try {
        $pdo = getDBConnection();
        if (!$pdo) return 0;
        
        $sql = "SELECT COUNT(*) FROM $table WHERE ";
        $conditions = [];
        $params = [];
        
        // Search conditions
        foreach ($searchFields as $field) {
            $conditions[] = "$field LIKE ?";
            $params[] = "%$searchTerm%";
        }
        $sql .= "(" . implode(' OR ', $conditions) . ")";
        
        // Additional filters
        if (!empty($additionalFilters)) {
            foreach ($additionalFilters as $column => $value) {
                $sql .= " AND $column = ?";
                $params[] = $value;
            }
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
        
    } catch (PDOException $e) {
        error_log("Get search count error: " . $e->getMessage());
        return 0;
    }
}

/**
 * Handle AJAX response
 * @param bool $success Success status
 * @param string $message Message
 * @param mixed $data Additional data
 */
function sendJsonResponse($success, $message, $data = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Sanitize filename
 * @param string $filename Original filename
 * @return string Sanitized filename
 */
function sanitizeFilename($filename) {
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename);
    return $filename;
}

/**
 * Create thumbnail from image
 * @param string $sourcePath Source image path
 * @param string $destPath Destination thumbnail path
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return bool Success status
 */
function createThumbnail($sourcePath, $destPath, $maxWidth = 300, $maxHeight = 300) {
    if (!file_exists($sourcePath)) {
        return false;
    }
    
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }
    
    list($width, $height, $type) = $imageInfo;
    
    // Load source image
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$source) {
        return false;
    }
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    $newWidth = intval($width * $ratio);
    $newHeight = intval($height * $ratio);
    
    // Create thumbnail
    $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and GIF
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
        imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save thumbnail
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($thumbnail, $destPath, 85);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($thumbnail, $destPath, 8);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($thumbnail, $destPath);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($thumbnail, $destPath, 85);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($thumbnail);
    
    return $result;
}

/**
 * Get file extension from filename
 * @param string $filename Filename
 * @return string Extension
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Check if file is an image
 * @param string $filename Filename or path
 * @return bool True if image, false otherwise
 */
function isImageFile($filename) {
    $extension = getFileExtension($filename);
    return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

/**
 * Format file size for display
 * @param int $bytes File size in bytes
 * @return string Formatted size
 */
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

/**
 * Validate email format
 * @param string $email Email address
 * @return bool True if valid, false otherwise
 */
function isValidEmailFormat($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL format
 * @param string $url URL
 * @return bool True if valid, false otherwise
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Generate random string
 * @param int $length Length of string
 * @return string Random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Log error to file
 * @param string $message Error message
 * @param string $logFile Log file path
 */
function logError($message, $logFile = '../logs/errors.log') {
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    error_log($logMessage, 3, $logFile);
}

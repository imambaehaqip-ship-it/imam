<?php
/**
 * Helper Functions
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

/**
 * Sanitize input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = Database::getInstance();
    $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
    return $db->fetch($sql, ['id' => $_SESSION['user_id']]);
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit();
}

/**
 * Set flash message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Check if flash message exists
 */
function hasFlash() {
    return isset($_SESSION['flash']);
}

/**
 * Format price to Indonesian Rupiah
 */
function formatPrice($price) {
    return 'Rp ' . number_format((float)$price, 0, ',', '.');
}

/**
 * Alias for formatPrice used by admin pages
 */
function formatRupiah($price) {
    return formatPrice($price);
}

/**
 * Get bootstrap badge color for order status
 */
function getStatusBadgeColor($status) {
    switch ($status) {
        case 'pending':
        case 'menunggu':
            return 'warning';
        case 'processed':
        case 'diproses':
            return 'info';
        case 'shipped':
        case 'dikirim':
            return 'primary';
        case 'completed':
        case 'selesai':
            return 'success';
        case 'cancelled':
        case 'dibatalkan':
            return 'danger';
        default:
            return 'secondary';
    }
}

/**
 * Get friendly order status label
 */
function getStatusLabel($status) {
    switch ($status) {
        case 'pending':
        case 'menunggu':
            return 'Pending';
        case 'processed':
        case 'diproses':
            return 'Diproses';
        case 'shipped':
        case 'dikirim':
            return 'Dikirim';
        case 'completed':
        case 'selesai':
            return 'Selesai';
        case 'cancelled':
        case 'dibatalkan':
            return 'Dibatalkan';
        default:
            return ucfirst((string)$status);
    }
}

/**
 * Format date
 */
function formatDate($date, $format = 'd F Y') {
    $timestamp = strtotime($date);
    $months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $day = date('d', $timestamp);
    $month = $months[date('n', $timestamp) - 1];
    $year = date('Y', $timestamp);
    
    return str_replace(['F', 'Y'], [$month, $year], date($format, $timestamp));
}

/**
 * Generate slug from string
 */
function generateSlug($string) {
    $slug = strtolower($string);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Upload file
 */
function uploadFile($file, $destination, $allowedTypes = null) {
    if ($allowedTypes === null) {
        $allowedTypes = ALLOWED_IMAGE_TYPES;
    }
    
    // Check if file was uploaded
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'File size exceeds maximum limit'];
    }
    
    // Check file type
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . time() . '.' . $fileExt;
    $filepath = $destination . $filename;
    
    // Create directory if not exists
    if (!is_dir($destination)) {
        mkdir($destination, 0777, true);
    }
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to upload file'];
}

/**
 * Delete file
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Generate order number
 */
function generateOrderNumber() {
    $prefix = 'MAMZ';
    $date = date('Ymd');
    $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
    return $prefix . $date . $random;
}

/**
 * Calculate discount price
 */
function calculateDiscountPrice($price, $discount) {
    if ($discount > 0) {
        if ($discount >= 100) {
            return 0;
        }
        return $price - ($price * ($discount / 100));
    }
    return $price;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Get cart count
 */
function getCartCount() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    $db = Database::getInstance();
    $userId = getCurrentUserId();
    $sql = "SELECT SUM(quantity) as total FROM keranjang WHERE user_id = :user_id";
    $result = $db->fetch($sql, ['user_id' => $userId]);
    
    return $result ? (int)$result['total'] : 0;
}

/**
 * Get cart total
 */
function getCartTotal() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    $db = Database::getInstance();
    $userId = getCurrentUserId();
    
    $sql = "SELECT SUM(k.quantity * (p.harga - (p.harga * p.diskon / 100))) as total 
            FROM keranjang k 
            JOIN produk p ON k.produk_id = p.id 
            WHERE k.user_id = :user_id";
    $result = $db->fetch($sql, ['user_id' => $userId]);
    
    return $result ? (float)$result['total'] : 0;
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

/**
 * Send JSON response
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Pagination
 */
function paginate($totalItems, $itemsPerPage, $currentPage) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'items_per_page' => $itemsPerPage,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'offset' => $offset,
        'has_next' => $currentPage < $totalPages,
        'has_prev' => $currentPage > 1
    ];
}

/**
 * Get pagination links
 */
function getPaginationLinks($pagination, $baseUrl) {
    $links = '';
    
    if ($pagination['has_prev']) {
        $links .= '<a href="' . $baseUrl . '?page=' . ($pagination['current_page'] - 1) . '" class="pagination-link">&laquo; Prev</a>';
    }
    
    for ($i = 1; $i <= $pagination['total_pages']; $i++) {
        $active = $i == $pagination['current_page'] ? 'active' : '';
        $links .= '<a href="' . $baseUrl . '?page=' . $i . '" class="pagination-link ' . $active . '">' . $i . '</a>';
    }
    
    if ($pagination['has_next']) {
        $links .= '<a href="' . $baseUrl . '?page=' . ($pagination['current_page'] + 1) . '" class="pagination-link">Next &raquo;</a>';
    }
    
    return $links;
}

/**
 * Check if product is in wishlist
 */
function isInWishlist($productId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $db = Database::getInstance();
    $userId = getCurrentUserId();
    $sql = "SELECT id FROM wishlist WHERE user_id = :user_id AND produk_id = :produk_id LIMIT 1";
    $result = $db->fetch($sql, ['user_id' => $userId, 'produk_id' => $productId]);
    
    return $result !== false;
}

/**
 * Get product rating stars
 */
function getRatingStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fas fa-star text-warning"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $stars .= '<i class="fas fa-star-half-alt text-warning"></i>';
        } else {
            $stars .= '<i class="far fa-star text-warning"></i>';
        }
    }
    return $stars;
}

/**
 * Time ago
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        return floor($diff / 60) . ' menit yang lalu';
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . ' jam yang lalu';
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . ' hari yang lalu';
    } elseif ($diff < 2592000) {
        return floor($diff / 604800) . ' minggu yang lalu';
    } elseif ($diff < 31536000) {
        return floor($diff / 2592000) . ' bulan yang lalu';
    } else {
        return floor($diff / 31536000) . ' tahun yang lalu';
    }
}

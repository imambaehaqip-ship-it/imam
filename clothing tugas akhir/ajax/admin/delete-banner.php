<?php
/**
 * AJAX Handler - Delete Banner
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

require_once BASE_PATH . '/models/Banner.php';

$bannerModel = new Banner();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID banner tidak valid']);
    exit;
}

$result = $bannerModel->deleteBanner($id);

if ($result) {
    echo json_encode(['success' => true, 'message' => 'Banner berhasil dihapus']);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal menghapus banner']);
}

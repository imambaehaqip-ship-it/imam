<?php
/**
 * AJAX Wishlist Handler
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Wishlist.php';

header('Content-Type: application/json');

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request']);
}

// Check if user is logged in
if (!isLoggedIn()) {
    sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
}

$wishlistModel = new Wishlist();
$action = $_POST['action'] ?? 'add';

switch ($action) {
    case 'add':
        $productId = (int)$_POST['product_id'];
        $userId = getCurrentUserId();
        
        $data = [
            'user_id' => $userId,
            'produk_id' => $productId
        ];
        
        $result = $wishlistModel->addToWishlist($data);
        
        if ($result) {
            sendJsonResponse(['success' => true, 'message' => 'Produk ditambahkan ke wishlist']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Produk sudah ada di wishlist']);
        }
        break;
        
    case 'remove':
        $productId = (int)$_POST['product_id'];
        $userId = getCurrentUserId();
        
        $result = $wishlistModel->removeFromWishlist($userId, $productId);
        
        if ($result) {
            sendJsonResponse(['success' => true, 'message' => 'Produk dihapus dari wishlist']);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Gagal menghapus dari wishlist']);
        }
        break;
        
    default:
        sendJsonResponse(['success' => false, 'message' => 'Invalid action']);
}

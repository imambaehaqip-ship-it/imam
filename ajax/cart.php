<?php
/**
 * AJAX Cart Handler
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Cart.php';

header('Content-Type: application/json');

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request']);
}

// Check if user is logged in
if (!isLoggedIn()) {
    sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
}

$cartModel = new Cart();
$action = $_POST['action'] ?? 'add';

switch ($action) {
    case 'add':
        $productId = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        $ukuran = sanitize($_POST['ukuran'] ?? '');
        $warna = sanitize($_POST['warna'] ?? '');
        $userId = getCurrentUserId();
        
        if ($quantity < 1) {
            sendJsonResponse(['success' => false, 'message' => 'Quantity minimal 1']);
        }
        
        $data = [
            'user_id' => $userId,
            'produk_id' => $productId,
            'quantity' => $quantity,
            'ukuran' => $ukuran,
            'warna' => $warna
        ];
        
        $result = $cartModel->addToCart($data);
        
        if ($result) {
            $cartCount = $cartModel->getCartCount($userId);
            sendJsonResponse([
                'success' => true,
                'message' => 'Produk ditambahkan ke keranjang',
                'cart_count' => $cartCount
            ]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Gagal menambahkan ke keranjang']);
        }
        break;
        
    case 'update':
        $cartId = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        $userId = getCurrentUserId();
        
        if ($quantity < 1) {
            sendJsonResponse(['success' => false, 'message' => 'Quantity minimal 1']);
        }
        
        $result = $cartModel->updateCartQuantity($cartId, $quantity);
        
        if ($result) {
            $cartTotal = $cartModel->getCartTotal($userId);
            sendJsonResponse([
                'success' => true,
                'message' => 'Keranjang diupdate',
                'cart_total' => $cartTotal
            ]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Gagal mengupdate keranjang']);
        }
        break;
        
    case 'remove':
        $cartId = (int)$_POST['cart_id'];
        $userId = getCurrentUserId();
        
        $result = $cartModel->removeFromCart($cartId);
        
        if ($result) {
            $cartCount = $cartModel->getCartCount($userId);
            $cartTotal = $cartModel->getCartTotal($userId);
            sendJsonResponse([
                'success' => true,
                'message' => 'Produk dihapus dari keranjang',
                'cart_count' => $cartCount,
                'cart_total' => $cartTotal
            ]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Gagal menghapus dari keranjang']);
        }
        break;
        
    default:
        sendJsonResponse(['success' => false, 'message' => 'Invalid action']);
}

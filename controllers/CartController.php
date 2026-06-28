<?php
/**
 * Cart Controller
 * Mamz Clothing - Fashion Marketplace
 */

require_once BASE_PATH . '/models/Cart.php';
require_once BASE_PATH . '/models/Product.php';

class CartController {
    private $cartModel;
    private $productModel;
    
    public function __construct() {
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }
    
    /**
     * Show cart page
     */
    public function index() {
        requireLogin();
        $userId = getCurrentUserId();
        $cartItems = $this->cartModel->getCartByUserId($userId);
        $cartTotal = $this->cartModel->getCartTotal($userId);
        
        require_once BASE_PATH . '/views/user/cart.php';
    }
    
    /**
     * Add to cart (AJAX)
     */
    public function add() {
        if (!isLoggedIn()) {
            sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)$_POST['product_id'];
            $quantity = (int)$_POST['quantity'];
            $ukuran = isset($_POST['ukuran']) ? sanitize($_POST['ukuran']) : '';
            $warna = isset($_POST['warna']) ? sanitize($_POST['warna']) : '';
            $userId = getCurrentUserId();
            
            // Check product availability
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                sendJsonResponse(['success' => false, 'message' => 'Produk tidak ditemukan']);
            }
            
            if ($product['stok'] < $quantity) {
                sendJsonResponse(['success' => false, 'message' => 'Stok tidak mencukupi']);
            }
            
            $data = [
                'user_id' => $userId,
                'produk_id' => $productId,
                'quantity' => $quantity,
                'ukuran' => $ukuran,
                'warna' => $warna
            ];
            
            $result = $this->cartModel->addToCart($data);
            
            if ($result) {
                $cartCount = $this->cartModel->getCartCount($userId);
                sendJsonResponse([
                    'success' => true, 
                    'message' => 'Produk ditambahkan ke keranjang',
                    'cart_count' => $cartCount
                ]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Gagal menambahkan ke keranjang']);
            }
        }
    }
    
    /**
     * Update cart quantity (AJAX)
     */
    public function update() {
        if (!isLoggedIn()) {
            sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = (int)$_POST['cart_id'];
            $quantity = (int)$_POST['quantity'];
            $userId = getCurrentUserId();
            
            if ($quantity < 1) {
                sendJsonResponse(['success' => false, 'message' => 'Quantity minimal 1']);
            }
            
            $result = $this->cartModel->updateCartQuantity($cartId, $quantity);
            
            if ($result) {
                $cartTotal = $this->cartModel->getCartTotal($userId);
                sendJsonResponse([
                    'success' => true, 
                    'message' => 'Keranjang diupdate',
                    'cart_total' => $cartTotal
                ]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Gagal mengupdate keranjang']);
            }
        }
    }
    
    /**
     * Remove from cart (AJAX)
     */
    public function remove() {
        if (!isLoggedIn()) {
            sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartId = (int)$_POST['cart_id'];
            $userId = getCurrentUserId();
            
            $result = $this->cartModel->removeFromCart($cartId);
            
            if ($result) {
                $cartCount = $this->cartModel->getCartCount($userId);
                $cartTotal = $this->cartModel->getCartTotal($userId);
                sendJsonResponse([
                    'success' => true, 
                    'message' => 'Produk dihapus dari keranjang',
                    'cart_count' => $cartCount,
                    'cart_total' => $cartTotal
                ]);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Gagal menghapus dari keranjang']);
            }
        }
    }
    
    /**
     * Clear cart
     */
    public function clear() {
        requireLogin();
        $userId = getCurrentUserId();
        $this->cartModel->clearCart($userId);
        setFlash('success', 'Keranjang dikosongkan');
        redirect(SITE_URL . '/cart.php');
    }
}

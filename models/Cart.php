<?php
/**
 * Cart Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Cart {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get cart by user ID
     */
    public function getCartByUserId($userId) {
        $sql = "SELECT c.*, p.nama_produk, p.harga, p.diskon, p.foto_utama, p.stok,
                (p.harga - (p.harga * p.diskon / 100)) as harga_diskon,
                (c.quantity * (p.harga - (p.harga * p.diskon / 100))) as subtotal
                FROM keranjang c 
                JOIN produk p ON c.produk_id = p.id 
                WHERE c.user_id = :user_id 
                ORDER BY c.created_at DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get cart item
     */
    public function getCartItem($userId, $productId) {
        $sql = "SELECT * FROM keranjang WHERE user_id = :user_id AND produk_id = :produk_id LIMIT 1";
        return $this->db->fetch($sql, ['user_id' => $userId, 'produk_id' => $productId]);
    }
    
    /**
     * Add to cart
     */
    public function addToCart($data) {
        // Check if item already exists in cart
        $existing = $this->getCartItem($data['user_id'], $data['produk_id']);
        
        if ($existing) {
            // Update quantity
            $newQuantity = $existing['quantity'] + $data['quantity'];
            return $this->updateCartQuantity($existing['id'], $newQuantity);
        }
        
        return $this->db->insert('keranjang', $data);
    }
    
    /**
     * Update cart quantity
     */
    public function updateCartQuantity($cartId, $quantity) {
        return $this->db->update('keranjang', ['quantity' => $quantity], 'id = :id', ['id' => $cartId]);
    }
    
    /**
     * Update cart item
     */
    public function updateCartItem($cartId, $data) {
        return $this->db->update('keranjang', $data, 'id = :id', ['id' => $cartId]);
    }
    
    /**
     * Remove from cart
     */
    public function removeFromCart($cartId) {
        return $this->db->delete('keranjang', 'id = :id', ['id' => $cartId]);
    }
    
    /**
     * Clear cart
     */
    public function clearCart($userId) {
        return $this->db->delete('keranjang', 'user_id = :user_id', ['user_id' => $userId]);
    }
    
    /**
     * Get cart total
     */
    public function getCartTotal($userId) {
        $sql = "SELECT SUM(c.quantity * (p.harga - (p.harga * p.diskon / 100))) as total 
                FROM keranjang c 
                JOIN produk p ON c.produk_id = p.id 
                WHERE c.user_id = :user_id";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        return $result ? (float)$result['total'] : 0;
    }
    
    /**
     * Get cart count
     */
    public function getCartCount($userId) {
        $sql = "SELECT SUM(quantity) as total FROM keranjang WHERE user_id = :user_id";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        return $result ? (int)$result['total'] : 0;
    }
}

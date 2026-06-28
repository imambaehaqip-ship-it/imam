<?php
/**
 * Wishlist Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Wishlist {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get wishlist by user ID
     */
    public function getWishlistByUserId($userId) {
        $sql = "SELECT w.*, p.nama_produk, p.harga, p.diskon, p.foto_utama, p.slug, p.rating,
                (p.harga - (p.harga * p.diskon / 100)) as harga_diskon,
                k.nama_kategori
                FROM wishlist w 
                JOIN produk p ON w.produk_id = p.id 
                JOIN kategori k ON p.kategori_id = k.id
                WHERE w.user_id = :user_id 
                ORDER BY w.created_at DESC";
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    /**
     * Check if product is in wishlist
     */
    public function isInWishlist($userId, $productId) {
        $sql = "SELECT id FROM wishlist WHERE user_id = :user_id AND produk_id = :produk_id LIMIT 1";
        $result = $this->db->fetch($sql, ['user_id' => $userId, 'produk_id' => $productId]);
        return $result !== false;
    }
    
    /**
     * Add to wishlist
     */
    public function addToWishlist($data) {
        // Check if already exists
        if ($this->isInWishlist($data['user_id'], $data['produk_id'])) {
            return false;
        }
        return $this->db->insert('wishlist', $data);
    }
    
    /**
     * Remove from wishlist
     */
    public function removeFromWishlist($userId, $productId) {
        return $this->db->delete('wishlist', 'user_id = :user_id AND produk_id = :produk_id', 
            ['user_id' => $userId, 'produk_id' => $productId]);
    }
    
    /**
     * Clear wishlist
     */
    public function clearWishlist($userId) {
        return $this->db->delete('wishlist', 'user_id = :user_id', ['user_id' => $userId]);
    }
    
    /**
     * Get wishlist count
     */
    public function getWishlistCount($userId) {
        $sql = "SELECT COUNT(*) as total FROM wishlist WHERE user_id = :user_id";
        $result = $this->db->fetch($sql, ['user_id' => $userId]);
        return $result ? (int)$result['total'] : 0;
    }
}

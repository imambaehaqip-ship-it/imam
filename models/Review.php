<?php
/**
 * Review Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Review {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get review by ID
     */
    public function getReviewById($id) {
        $sql = "SELECT r.*, u.nama_lengkap, p.nama_produk, p.slug as produk_slug 
                FROM review r 
                JOIN users u ON r.user_id = u.id 
                JOIN produk p ON r.produk_id = p.id 
                WHERE r.id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get reviews by product ID
     */
    public function getReviewsByProductId($productId, $limit = null, $offset = 0) {
        $sql = "SELECT r.*, u.nama_lengkap 
                FROM review r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.produk_id = :produk_id AND r.status = 'aktif' 
                ORDER BY r.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['produk_id' => $productId, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['produk_id' => $productId]);
    }
    
    /**
     * Get reviews by user ID
     */
    public function getReviewsByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT r.*, p.nama_produk, p.slug as produk_slug, p.foto_utama 
                FROM review r 
                JOIN produk p ON r.produk_id = p.id 
                WHERE r.user_id = :user_id 
                ORDER BY r.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get all reviews
     */
    public function getAllReviews($limit = null, $offset = 0, $statusFilter = '') {
        $sql = "SELECT r.id, r.rating, r.komentar as ulasan, r.status, r.created_at as tanggal, r.produk_id,
                u.nama_lengkap as nama_user, p.nama_produk, p.slug as produk_slug
                FROM review r 
                JOIN users u ON r.user_id = u.id 
                JOIN produk p ON r.produk_id = p.id 
                WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND r.status = :status";
            $params['status'] = $statusFilter;
        }

        $sql .= " ORDER BY r.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Create new review
     */
    public function createReview($data) {
        return $this->db->insert('review', $data);
    }
    
    /**
     * Update review
     */
    public function updateReview($id, $data) {
        return $this->db->update('review', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update review status
     */
    public function updateReviewStatus($id, $status) {
        return $this->db->update('review', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete review
     */
    public function deleteReview($id) {
        return $this->db->delete('review', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total reviews
     */
    public function countReviews($statusFilter = '') {
        $sql = "SELECT COUNT(*) as total FROM review WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND status = :status";
            $params['status'] = $statusFilter;
        }

        $result = $this->db->fetch($sql, $params);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count reviews by product
     */
    public function countReviewsByProduct($productId) {
        $sql = "SELECT COUNT(*) as total FROM review WHERE produk_id = :produk_id AND status = 'aktif'";
        $result = $this->db->fetch($sql, ['produk_id' => $productId]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Get average rating by product
     */
    public function getAverageRating($productId) {
        $sql = "SELECT AVG(rating) as average, COUNT(*) as count 
                FROM review 
                WHERE produk_id = :produk_id AND status = 'aktif'";
        $result = $this->db->fetch($sql, ['produk_id' => $productId]);
        return $result ? ['average' => (float)$result['average'], 'count' => (int)$result['count']] : ['average' => 0, 'count' => 0];
    }
    
    /**
     * Check if user has reviewed product
     */
    public function hasUserReviewed($userId, $productId) {
        $sql = "SELECT id FROM review WHERE user_id = :user_id AND produk_id = :produk_id LIMIT 1";
        $result = $this->db->fetch($sql, ['user_id' => $userId, 'produk_id' => $productId]);
        return $result !== false;
    }
}

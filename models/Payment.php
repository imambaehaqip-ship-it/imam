<?php
/**
 * Payment Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Payment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get payment by ID
     */
    public function getPaymentById($id) {
        $sql = "SELECT p.*, o.nomor_pesanan, o.total_harga 
                FROM pembayaran p 
                JOIN pesanan o ON p.pesanan_id = o.id 
                WHERE p.id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get payment by order ID
     */
    public function getPaymentByOrderId($orderId) {
        $sql = "SELECT * FROM pembayaran WHERE pesanan_id = :pesanan_id LIMIT 1";
        return $this->db->fetch($sql, ['pesanan_id' => $orderId]);
    }
    
    /**
     * Get all payments
     */
    public function getAllPayments($limit = null, $offset = 0) {
        $sql = "SELECT p.*, o.nomor_pesanan, o.total_harga, u.nama_lengkap 
                FROM pembayaran p 
                JOIN pesanan o ON p.pesanan_id = o.id 
                JOIN users u ON o.user_id = u.id 
                ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get payments by status
     */
    public function getPaymentsByStatus($status, $limit = null, $offset = 0) {
        $sql = "SELECT p.*, o.nomor_pesanan, o.total_harga, u.nama_lengkap 
                FROM pembayaran p 
                JOIN pesanan o ON p.pesanan_id = o.id 
                JOIN users u ON o.user_id = u.id 
                WHERE p.status = :status 
                ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['status' => $status, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    /**
     * Create new payment
     */
    public function createPayment($data) {
        return $this->db->insert('pembayaran', $data);
    }
    
    /**
     * Update payment
     */
    public function updatePayment($id, $data) {
        return $this->db->update('pembayaran', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status, $catatan = null) {
        $data = ['status' => $status];
        if ($catatan !== null) {
            $data['catatan_admin'] = $catatan;
        }
        return $this->db->update('pembayaran', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total payments
     */
    public function countPayments() {
        $sql = "SELECT COUNT(*) as total FROM pembayaran";
        $result = $this->db->fetch($sql);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count payments by status
     */
    public function countPaymentsByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM pembayaran WHERE status = :status";
        $result = $this->db->fetch($sql, ['status' => $status]);
        return $result ? $result['total'] : 0;
    }
}

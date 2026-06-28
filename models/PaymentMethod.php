<?php
/**
 * PaymentMethod Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class PaymentMethod {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get payment method by ID
     */
    public function getPaymentMethodById($id) {
        $sql = "SELECT * FROM metode_pembayaran WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get all active payment methods
     */
    public function getAllActivePaymentMethods() {
        $sql = "SELECT * FROM metode_pembayaran WHERE status = 'aktif' ORDER BY urutan ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get all payment methods
     */
    public function getAllPaymentMethods($limit = null, $offset = 0) {
        $sql = "SELECT * FROM metode_pembayaran ORDER BY urutan ASC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Create new payment method
     */
    public function createPaymentMethod($data) {
        return $this->db->insert('metode_pembayaran', $data);
    }
    
    /**
     * Update payment method
     */
    public function updatePaymentMethod($id, $data) {
        return $this->db->update('metode_pembayaran', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update payment method status
     */
    public function updatePaymentMethodStatus($id, $status) {
        return $this->db->update('metode_pembayaran', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete payment method
     */
    public function deletePaymentMethod($id) {
        return $this->db->delete('metode_pembayaran', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total payment methods
     */
    public function countPaymentMethods() {
        $sql = "SELECT COUNT(*) as total FROM metode_pembayaran";
        $result = $this->db->fetch($sql);
        return $result ? $result['total'] : 0;
    }
}

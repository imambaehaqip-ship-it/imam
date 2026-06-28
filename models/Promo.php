<?php
/**
 * Promo Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Promo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get promo by ID
     */
    public function getPromoById($id) {
        $sql = "SELECT * FROM promo WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get promo by code
     */
    public function getPromoByCode($code) {
        $sql = "SELECT * FROM promo WHERE kode_promo = :kode_promo LIMIT 1";
        return $this->db->fetch($sql, ['kode_promo' => $code]);
    }
    
    /**
     * Get all active promos
     */
    public function getAllActivePromos() {
        $sql = "SELECT * FROM promo 
                WHERE status = 'aktif' 
                AND tanggal_mulai <= CURDATE() 
                AND tanggal_selesai >= CURDATE() 
                ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get all promos
     */
    public function getAllPromos($limit = null, $offset = 0) {
        $sql = "SELECT * FROM promo ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Create new promo
     */
    public function createPromo($data) {
        return $this->db->insert('promo', $data);
    }
    
    /**
     * Update promo
     */
    public function updatePromo($id, $data) {
        return $this->db->update('promo', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update promo status
     */
    public function updatePromoStatus($id, $status) {
        return $this->db->update('promo', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete promo
     */
    public function deletePromo($id) {
        return $this->db->delete('promo', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Validate promo code
     */
    public function validatePromo($code, $totalBelanja) {
        $promo = $this->getPromoByCode($code);
        
        if (!$promo) {
            return ['valid' => false, 'message' => 'Kode promo tidak valid'];
        }
        
        if ($promo['status'] !== 'aktif') {
            return ['valid' => false, 'message' => 'Kode promo tidak aktif'];
        }
        
        $today = date('Y-m-d');
        if ($promo['tanggal_mulai'] > $today || $promo['tanggal_selesai'] < $today) {
            return ['valid' => false, 'message' => 'Kode promo sudah kadaluarsa'];
        }
        
        if ($totalBelanja < $promo['minimal_belanja']) {
            return ['valid' => false, 'message' => 'Minimal belanja Rp ' . number_format($promo['minimal_belanja'], 0, ',', '.')];
        }
        
        if ($promo['kuota'] > 0 && $promo['kuota_terpakai'] >= $promo['kuota']) {
            return ['valid' => false, 'message' => 'Kuota promo habis'];
        }
        
        return ['valid' => true, 'promo' => $promo];
    }
    
    /**
     * Calculate discount
     */
    public function calculateDiscount($promo, $totalBelanja) {
        $discount = 0;
        
        if ($promo['tipe_diskon'] === 'persentase') {
            $discount = $totalBelanja * ($promo['nilai_diskon'] / 100);
            if ($promo['maksimal_diskon'] > 0 && $discount > $promo['maksimal_diskon']) {
                $discount = $promo['maksimal_diskon'];
            }
        } else {
            $discount = $promo['nilai_diskon'];
        }
        
        return $discount;
    }
    
    /**
     * Increment promo usage
     */
    public function incrementPromoUsage($promoId) {
        $sql = "UPDATE promo SET kuota_terpakai = kuota_terpakai + 1 WHERE id = :id";
        return $this->db->query($sql, ['id' => $promoId]);
    }
}

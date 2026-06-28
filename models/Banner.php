<?php
/**
 * Banner Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Banner {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get banner by ID
     */
    public function getBannerById($id) {
        $sql = "SELECT * FROM banner WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get all active banners by type
     */
    public function getActiveBannersByType($type) {
        $sql = "SELECT * FROM banner WHERE tipe = :tipe AND status = 'aktif' ORDER BY urutan ASC";
        return $this->db->fetchAll($sql, ['tipe' => $type]);
    }
    
    /**
     * Get all active banners
     */
    public function getAllActiveBanners() {
        $sql = "SELECT * FROM banner WHERE status = 'aktif' ORDER BY urutan ASC";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get all banners
     */
    public function getAllBanners($limit = null, $offset = 0) {
        $sql = "SELECT * FROM banner ORDER BY urutan ASC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Create new banner
     */
    public function createBanner($data) {
        return $this->db->insert('banner', $data);
    }
    
    /**
     * Update banner
     */
    public function updateBanner($id, $data) {
        return $this->db->update('banner', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update banner status
     */
    public function updateBannerStatus($id, $status) {
        return $this->db->update('banner', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete banner
     */
    public function deleteBanner($id) {
        return $this->db->delete('banner', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total banners
     */
    public function countBanners() {
        $sql = "SELECT COUNT(*) as total FROM banner";
        $result = $this->db->fetch($sql);
        return $result ? $result['total'] : 0;
    }
}

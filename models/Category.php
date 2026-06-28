<?php
/**
 * Category Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Category {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get category by ID
     */
    public function getCategoryById($id) {
        $sql = "SELECT * FROM kategori WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get category by slug
     */
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM kategori WHERE slug = :slug LIMIT 1";
        return $this->db->fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * Get all categories
     */
    public function getAllCategories($status = 'aktif') {
        $sql = "SELECT k.id, k.nama_kategori as nama, k.slug, k.deskripsi, k.icon, k.urutan, k.status, k.created_at, k.updated_at,
                (SELECT COUNT(*) FROM produk p WHERE p.kategori_id = k.id) as jumlah_produk
                FROM kategori k
                WHERE k.status = :status
                ORDER BY k.urutan ASC, k.nama_kategori ASC";
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    /**
     * Count total categories
     */
    public function countCategories() {
        $sql = "SELECT COUNT(*) as total FROM kategori";
        $result = $this->db->fetch($sql);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Create new category
     */
    public function createCategory($data) {
        $mappedData = $data;
        if (isset($mappedData['nama']) && !isset($mappedData['nama_kategori'])) {
            $mappedData['nama_kategori'] = $mappedData['nama'];
            unset($mappedData['nama']);
        }
        return $this->db->insert('kategori', $mappedData);
    }
    
    /**
     * Update category
     */
    public function updateCategory($id, $data) {
        $mappedData = $data;
        if (isset($mappedData['nama']) && !isset($mappedData['nama_kategori'])) {
            $mappedData['nama_kategori'] = $mappedData['nama'];
            unset($mappedData['nama']);
        }
        return $this->db->update('kategori', $mappedData, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete category
     */
    public function deleteCategory($id) {
        return $this->db->delete('kategori', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update category status
     */
    public function updateCategoryStatus($id, $status) {
        return $this->db->update('kategori', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Get category with product count
     */
    public function getCategoriesWithProductCount() {
        $sql = "SELECT k.*, COUNT(p.id) as product_count 
                FROM kategori k 
                LEFT JOIN produk p ON k.id = p.kategori_id AND p.status = 'aktif'
                WHERE k.status = 'aktif'
                GROUP BY k.id 
                ORDER BY k.urutan ASC";
        return $this->db->fetchAll($sql);
    }
}

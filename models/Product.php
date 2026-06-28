<?php
/**
 * Product Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get product by ID
     */
    public function getProductById($id) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get product by slug
     */
    public function getProductBySlug($slug) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.slug = :slug LIMIT 1";
        return $this->db->fetch($sql, ['slug' => $slug]);
    }
    
    /**
     * Get all products
     */
    public function getAllProducts($limit = null, $offset = 0, $search = '', $categoryFilter = 0) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.nama_produk LIKE :search OR p.sku LIKE :search OR k.nama_kategori LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($categoryFilter)) {
            $sql .= " AND p.kategori_id = :kategori_id";
            $params['kategori_id'] = $categoryFilter;
        }

        $sql .= " ORDER BY p.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get products by category
     */
    public function getProductsByCategory($categoryId, $limit = null, $offset = 0) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.kategori_id = :kategori_id AND p.status = 'aktif' 
                ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['kategori_id' => $categoryId, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['kategori_id' => $categoryId]);
    }
    
    /**
     * Get featured products
     */
    public function getFeaturedProducts($limit = 8) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.is_featured = TRUE AND p.status = 'aktif' 
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Get popular products
     */
    public function getPopularProducts($limit = 8) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.is_popular = TRUE AND p.status = 'aktif' 
                ORDER BY p.total_terjual DESC 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Get latest products
     */
    public function getLatestProducts($limit = 8) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.status = 'aktif' 
                ORDER BY p.created_at DESC 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Search products
     */
    public function searchProducts($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.status = 'aktif' AND 
                (p.nama_produk LIKE :keyword OR 
                 p.deskripsi LIKE :keyword OR 
                 k.nama_kategori LIKE :keyword) 
                ORDER BY p.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['keyword' => "%{$keyword}%", 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['keyword' => "%{$keyword}%"]);
    }
    
    /**
     * Filter products
     */
    public function filterProducts($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.status = 'aktif'";
        $params = [];
        
        if (!empty($filters['kategori_id'])) {
            $sql .= " AND p.kategori_id = :kategori_id";
            $params['kategori_id'] = $filters['kategori_id'];
        }
        
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.harga >= :min_price";
            $params['min_price'] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.harga <= :max_price";
            $params['max_price'] = $filters['max_price'];
        }
        
        if (!empty($filters['ukuran'])) {
            $sql .= " AND p.ukuran LIKE :ukuran";
            $params['ukuran'] = "%{$filters['ukuran']}%";
        }
        
        if (!empty($filters['warna'])) {
            $sql .= " AND p.warna LIKE :warna";
            $params['warna'] = "%{$filters['warna']}%";
        }
        
        // Sorting
        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = 'p.harga ASC';
                    break;
                case 'price_desc':
                    $orderBy = 'p.harga DESC';
                    break;
                case 'name_asc':
                    $orderBy = 'p.nama_produk ASC';
                    break;
                case 'name_desc':
                    $orderBy = 'p.nama_produk DESC';
                    break;
                case 'popular':
                    $orderBy = 'p.total_terjual DESC';
                    break;
                case 'rating':
                    $orderBy = 'p.rating DESC';
                    break;
            }
        }
        
        $sql .= " ORDER BY {$orderBy}";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count total products
     */
    public function countProducts($search = '', $categoryFilter = 0) {
        $sql = "SELECT COUNT(*) as total FROM produk p JOIN kategori k ON p.kategori_id = k.id WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (p.nama_produk LIKE :search OR p.sku LIKE :search OR k.nama_kategori LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($categoryFilter)) {
            $sql .= " AND p.kategori_id = :kategori_id";
            $params['kategori_id'] = $categoryFilter;
        }

        $result = $this->db->fetch($sql, $params);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count products by category
     */
    public function countProductsByCategory($categoryId) {
        $sql = "SELECT COUNT(*) as total FROM produk WHERE kategori_id = :kategori_id";
        $result = $this->db->fetch($sql, ['kategori_id' => $categoryId]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count search results
     */
    public function countSearchResults($keyword) {
        $sql = "SELECT COUNT(*) as total 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.status = 'aktif' AND 
                (p.nama_produk LIKE :keyword OR 
                 p.deskripsi LIKE :keyword OR 
                 k.nama_kategori LIKE :keyword)";
        $result = $this->db->fetch($sql, ['keyword' => "%{$keyword}%"]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Create new product
     */
    public function createProduct($data) {
        return $this->db->insert('produk', $data);
    }
    
    /**
     * Update product
     */
    public function updateProduct($id, $data) {
        return $this->db->update('produk', $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Get top products for dashboard
     */
    public function getTopProducts($limit = 5) {
        $sql = "SELECT p.nama_produk as nama, p.foto_utama as gambar, p.harga, SUM(dp.quantity) as total_sold
                FROM detail_pesanan dp
                JOIN produk p ON dp.produk_id = p.id
                GROUP BY dp.produk_id
                ORDER BY total_sold DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Delete product
     */
    public function deleteProduct($id) {
        return $this->db->delete('produk', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update product stock
     */
    public function updateStock($id, $quantity) {
        $sql = "UPDATE produk SET stok = stok - :quantity WHERE id = :id";
        return $this->db->query($sql, ['quantity' => $quantity, 'id' => $id]);
    }
    
    /**
     * Update product rating
     */
    public function updateRating($productId) {
        $sql = "UPDATE produk p 
                SET rating = (
                    SELECT COALESCE(AVG(rating), 0) 
                    FROM review 
                    WHERE produk_id = :produk_id AND status = 'aktif'
                ),
                total_review = (
                    SELECT COUNT(*) 
                    FROM review 
                    WHERE produk_id = :produk_id AND status = 'aktif'
                )
                WHERE id = :produk_id";
        return $this->db->query($sql, ['produk_id' => $productId]);
    }
    
    /**
     * Update total sold
     */
    public function updateTotalSold($productId, $quantity) {
        $sql = "UPDATE produk SET total_terjual = total_terjual + :quantity WHERE id = :id";
        return $this->db->query($sql, ['quantity' => $quantity, 'id' => $productId]);
    }
    
    /**
     * Get related products
     */
    public function getRelatedProducts($productId, $categoryId, $limit = 4) {
        $sql = "SELECT p.*, k.nama_kategori, k.slug as kategori_slug 
                FROM produk p 
                JOIN kategori k ON p.kategori_id = k.id 
                WHERE p.kategori_id = :kategori_id 
                AND p.id != :id 
                AND p.status = 'aktif' 
                ORDER BY RAND() 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['kategori_id' => $categoryId, 'id' => $productId, 'limit' => $limit]);
    }
}

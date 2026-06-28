<?php
/**
 * Order Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get order by ID
     */
    public function getOrderById($id) {
        $sql = "SELECT o.id, o.user_id, o.nomor_pesanan as no_order, o.total_harga as total, o.status_pesanan as status,
                o.nama_penerima, o.email_penerima as email, o.nomor_hp as telepon_penerima,
                o.provinsi, o.kota, o.kecamatan, o.kelurahan, o.kode_pos, o.detail_alamat as alamat_pengiriman,
                o.catatan, o.created_at as tanggal, o.updated_at as updated_at,
                u.nama_lengkap, u.email as user_email
                FROM pesanan o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get order by order number
     */
    public function getOrderByNumber($orderNumber) {
        $sql = "SELECT o.*, u.nama_lengkap, u.email as user_email 
                FROM pesanan o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.nomor_pesanan = :nomor_pesanan LIMIT 1";
        return $this->db->fetch($sql, ['nomor_pesanan' => $orderNumber]);
    }
    
    /**
     * Get orders by user ID
     */
    public function getOrdersByUserId($userId, $limit = null, $offset = 0) {
        $sql = "SELECT o.*, 
                (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id = o.id) as item_count
                FROM pesanan o 
                WHERE o.user_id = :user_id 
                ORDER BY o.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['user_id' => $userId]);
    }
    
    /**
     * Get all orders
     */
    public function getAllOrders($limit = null, $offset = 0, $statusFilter = '') {
        $sql = "SELECT o.id, o.nomor_pesanan as no_order, o.total_harga as total, o.status_pesanan as status,
                o.nama_penerima, o.email_penerima as email, o.created_at as tanggal,
                u.nama_lengkap, u.email as user_email,
                (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id = o.id) as item_count
                FROM pesanan o 
                JOIN users u ON o.user_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND o.status_pesanan = :status_pesanan";
            $params['status_pesanan'] = $statusFilter;
        }

        $sql .= " ORDER BY o.created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get orders by status
     */
    public function getOrdersByStatus($status, $limit = null, $offset = 0) {
        $sql = "SELECT o.*, u.nama_lengkap, u.email as user_email,
                (SELECT COUNT(*) FROM detail_pesanan WHERE pesanan_id = o.id) as item_count
                FROM pesanan o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.status_pesanan = :status_pesanan 
                ORDER BY o.created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['status_pesanan' => $status, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['status_pesanan' => $status]);
    }
    
    /**
     * Create new order
     */
    public function createOrder($data) {
        return $this->db->insert('pesanan', $data);
    }
    
    /**
     * Update order
     */
    public function updateOrder($id, $data) {
        return $this->db->update('pesanan', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus($id, $status, $catatan = '') {
        $data = ['status_pesanan' => $status];
        if ($catatan !== '') {
            $data['catatan_admin'] = $catatan;
        }
        return $this->db->update('pesanan', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update payment status
     */
    public function updatePaymentStatus($id, $status) {
        return $this->db->update('pesanan', ['status_pembayaran' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total orders
     */
    public function countOrders($statusFilter = '') {
        $sql = "SELECT COUNT(*) as total FROM pesanan WHERE 1=1";
        $params = [];

        if (!empty($statusFilter)) {
            $sql .= " AND status_pesanan = :status_pesanan";
            $params['status_pesanan'] = $statusFilter;
        }

        $result = $this->db->fetch($sql, $params);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count orders by status
     */
    public function countOrdersByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan = :status_pesanan";
        $result = $this->db->fetch($sql, ['status_pesanan' => $status]);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Get order details
     */
    public function getOrderDetails($orderId) {
        $sql = "SELECT dp.produk_id, dp.nama_produk, dp.harga, dp.quantity as jumlah, dp.subtotal,
                p.foto_utama as gambar
                FROM detail_pesanan dp 
                JOIN produk p ON dp.produk_id = p.id 
                WHERE dp.pesanan_id = :pesanan_id";
        return $this->db->fetchAll($sql, ['pesanan_id' => $orderId]);
    }
    
    /**
     * Create order detail
     */
    public function createOrderDetail($data) {
        return $this->db->insert('detail_pesanan', $data);
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue() {
        $sql = "SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan IN ('selesai', 'dikirim')";
        $result = $this->db->fetch($sql);
        return $result ? (float)$result['total'] : 0;
    }
    
    /**
     * Get daily sales
     */
    public function getDailySales($days = 30) {
        $sql = "SELECT DATE(created_at) as date, SUM(total_harga) as total, COUNT(*) as orders 
                FROM pesanan 
                WHERE status_pesanan IN ('selesai', 'dikirim') 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                GROUP BY DATE(created_at) 
                ORDER BY date ASC";
        return $this->db->fetchAll($sql, ['days' => $days]);
    }
    
    /**
     * Get monthly sales
     */
    public function getMonthlySales($months = 12) {
        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') as month, SUM(total_harga) as total, COUNT(*) as orders 
                FROM pesanan 
                WHERE status_pesanan IN ('selesai', 'dikirim') 
                AND created_at >= DATE_SUB(CURDATE(), INTERVAL :months MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m') 
                ORDER BY month ASC";
        return $this->db->fetchAll($sql, ['months' => $months]);
    }

    /**
     * Get recent orders for dashboard
     */
    public function getRecentOrders($limit = 5) {
        $sql = "SELECT o.id, o.nomor_pesanan as no_order, o.total_harga as total, o.status_pesanan as status, o.created_at as tanggal, u.nama_lengkap as nama_penerima
                FROM pesanan o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    /**
     * Get top selling products
     */
    public function getTopSellingProducts($limit = 10) {
        $sql = "SELECT dp.nama_produk, SUM(dp.quantity) as total_sold, SUM(dp.subtotal) as total_revenue 
                FROM detail_pesanan dp 
                JOIN pesanan p ON dp.pesanan_id = p.id 
                WHERE p.status_pesanan IN ('selesai', 'dikirim') 
                GROUP BY dp.nama_produk 
                ORDER BY total_sold DESC 
                LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
}

<?php
/**
 * User Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }
    
    /**
     * Get all users
     */
    public function getAllUsers($limit = null, $offset = 0, $search = '', $roleFilter = '') {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nama_lengkap LIKE :search OR email LIKE :search OR nomor_hp LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($roleFilter)) {
            $sql .= " AND role = :role";
            $params['role'] = $roleFilter;
        }

        $sql .= " ORDER BY created_at DESC";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params['limit'] = $limit;
            $params['offset'] = $offset;
        }

        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count total users
     */
    public function countUsers($search = '', $roleFilter = '') {
        $sql = "SELECT COUNT(*) as total FROM users WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (nama_lengkap LIKE :search OR email LIKE :search OR nomor_hp LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        if (!empty($roleFilter)) {
            $sql .= " AND role = :role";
            $params['role'] = $roleFilter;
        }

        $result = $this->db->fetch($sql, $params);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Create new user
     */
    public function createUser($data) {
        return $this->db->insert('users', $data);
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $data) {
        return $this->db->update('users', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete user
     */
    public function deleteUser($id) {
        return $this->db->delete('users', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update user status
     */
    public function updateUserStatus($id, $status) {
        return $this->db->update('users', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Search users
     */
    public function searchUsers($keyword) {
        $sql = "SELECT * FROM users WHERE 
                nama_lengkap LIKE :keyword OR 
                email LIKE :keyword OR 
                nomor_hp LIKE :keyword 
                ORDER BY created_at DESC";
        $param = ['keyword' => "%{$keyword}%"];
        return $this->db->fetchAll($sql, $param);
    }
}

<?php
/**
 * Contact Model
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/database/Database.php';

class Contact {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get contact by ID
     */
    public function getContactById($id) {
        $sql = "SELECT * FROM kontak WHERE id = :id LIMIT 1";
        return $this->db->fetch($sql, ['id' => $id]);
    }
    
    /**
     * Get all contacts
     */
    public function getAllContacts($limit = null, $offset = 0) {
        $sql = "SELECT * FROM kontak ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get contacts by status
     */
    public function getContactsByStatus($status, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM kontak WHERE status = :status ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            return $this->db->fetchAll($sql, ['status' => $status, 'limit' => $limit, 'offset' => $offset]);
        }
        return $this->db->fetchAll($sql, ['status' => $status]);
    }
    
    /**
     * Create new contact
     */
    public function createContact($data) {
        return $this->db->insert('kontak', $data);
    }
    
    /**
     * Update contact
     */
    public function updateContact($id, $data) {
        return $this->db->update('kontak', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Update contact status
     */
    public function updateContactStatus($id, $status) {
        return $this->db->update('kontak', ['status' => $status], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete contact
     */
    public function deleteContact($id) {
        return $this->db->delete('kontak', 'id = :id', ['id' => $id]);
    }
    
    /**
     * Count total contacts
     */
    public function countContacts() {
        $sql = "SELECT COUNT(*) as total FROM kontak";
        $result = $this->db->fetch($sql);
        return $result ? $result['total'] : 0;
    }
    
    /**
     * Count contacts by status
     */
    public function countContactsByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM kontak WHERE status = :status";
        $result = $this->db->fetch($sql, ['status' => $status]);
        return $result ? $result['total'] : 0;
    }
}

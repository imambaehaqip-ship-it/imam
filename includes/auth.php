<?php
/**
 * Authentication Functions
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/includes/functions.php';

/**
 * Login user
 */
function login($email, $password) {
    $db = Database::getInstance();
    
    $sql = "SELECT * FROM users WHERE email = :email AND status = 'aktif' LIMIT 1";
    $user = $db->fetch($sql, ['email' => $email]);
    
    if ($user && password_verify($password, $user['password'])) {
        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['user_nama'] = $user['nama_lengkap'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['logged_in'] = true;
        
        // Update last login
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $user['id']]);
        
        return ['success' => true, 'user' => $user];
    }
    
    return ['success' => false, 'message' => 'Email atau password salah'];
}

/**
 * Register user
 */
function register($data) {
    $db = Database::getInstance();
    
    // Check if email already exists
    $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
    $existing = $db->fetch($sql, ['email' => $data['email']]);
    
    if ($existing) {
        return ['success' => false, 'message' => 'Email sudah terdaftar'];
    }
    
    // Hash password
    $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    $data['role'] = 'user';
    $data['status'] = 'aktif';
    
    // Insert user
    $userId = $db->insert('users', $data);
    
    if ($userId) {
        return ['success' => true, 'user_id' => $userId];
    }
    
    return ['success' => false, 'message' => 'Gagal mendaftar'];
}

/**
 * Logout user
 */
function logout() {
    // Destroy session
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 42000, '/');
    }
    
    session_destroy();
    
    return true;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu');
        redirect(SITE_URL . '/login.php');
    }
}

/**
 * Require admin
 */
function requireAdmin() {
    if (!isAdmin()) {
        if (isLoggedIn()) {
            setFlash('error', 'Akses ditolak');
            redirect(SITE_URL . '/index.php');
        }

        setFlash('error', 'Silakan login sebagai admin');
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Update user profile
 */
function updateProfile($userId, $data) {
    $db = Database::getInstance();
    
    // If password is provided, hash it
    if (isset($data['password']) && !empty($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    } else {
        unset($data['password']);
    }
    
    $result = $db->update('users', $data, 'id = :id', ['id' => $userId]);
    
    if ($result !== false) {
        // Update session
        if (isset($data['nama_lengkap'])) {
            $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        }
        
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Gagal mengupdate profil'];
}

/**
 * Change password
 */
function changePassword($userId, $currentPassword, $newPassword) {
    $db = Database::getInstance();
    
    // Verify current password
    $sql = "SELECT password FROM users WHERE id = :id LIMIT 1";
    $user = $db->fetch($sql, ['id' => $userId]);
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Password saat ini salah'];
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $result = $db->update('users', ['password' => $hashedPassword], 'id = :id', ['id' => $userId]);
    
    if ($result !== false) {
        return ['success' => true];
    }
    
    return ['success' => false, 'message' => 'Gagal mengubah password'];
}

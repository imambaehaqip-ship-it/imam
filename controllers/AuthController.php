<?php
/**
 * Auth Controller
 * Mamz Clothing - Fashion Marketplace
 */

require_once BASE_PATH . '/includes/auth.php';
require_once BASE_PATH . '/models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    /**
     * Show login page
     */
    public function showLogin() {
        if (isLoggedIn()) {
            redirect(SITE_URL . '/index.php');
        }
        require_once BASE_PATH . '/views/auth/login.php';
    }
    
    /**
     * Handle login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitize($_POST['email']);
            $password = $_POST['password'];
            
            $result = login($email, $password);
            
            if ($result['success']) {
                if ($result['user']['role'] === 'admin') {
                    setFlash('success', 'Selamat datang, ' . $result['user']['nama_lengkap']);
                    redirect(SITE_URL . '/admin/dashboard.php');
                } else {
                    setFlash('success', 'Selamat datang, ' . $result['user']['nama_lengkap']);
                    redirect(SITE_URL . '/index.php');
                }
            } else {
                setFlash('error', $result['message']);
                redirect(SITE_URL . '/login.php');
            }
        }
    }
    
    /**
     * Show register page
     */
    public function showRegister() {
        if (isLoggedIn()) {
            redirect(SITE_URL . '/index.php');
        }
        require_once BASE_PATH . '/views/auth/register.php';
    }
    
    /**
     * Handle register
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_lengkap' => sanitize($_POST['nama_lengkap']),
                'email' => sanitize($_POST['email']),
                'password' => $_POST['password'],
                'nomor_hp' => sanitize($_POST['nomor_hp']),
                'alamat' => sanitize($_POST['alamat']),
                'kota' => sanitize($_POST['kota']),
                'provinsi' => sanitize($_POST['provinsi']),
                'kode_pos' => sanitize($_POST['kode_pos'])
            ];
            
            // Validate
            if (empty($data['nama_lengkap']) || empty($data['email']) || empty($data['password'])) {
                setFlash('error', 'Semua field wajib diisi');
                redirect(SITE_URL . '/register.php');
            }
            
            if (!validateEmail($data['email'])) {
                setFlash('error', 'Email tidak valid');
                redirect(SITE_URL . '/register.php');
            }
            
            if (strlen($data['password']) < 6) {
                setFlash('error', 'Password minimal 6 karakter');
                redirect(SITE_URL . '/register.php');
            }
            
            $result = register($data);
            
            if ($result['success']) {
                setFlash('success', 'Pendaftaran berhasil. Silakan login.');
                redirect(SITE_URL . '/login.php');
            } else {
                setFlash('error', $result['message']);
                redirect(SITE_URL . '/register.php');
            }
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        logout();
        setFlash('success', 'Anda telah logout');
        redirect(SITE_URL . '/login.php');
    }
    
    /**
     * Show profile page
     */
    public function showProfile() {
        requireLogin();
        $user = getCurrentUser();
        require_once BASE_PATH . '/views/user/profile.php';
    }
    
    /**
     * Update profile
     */
    public function updateProfile() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = getCurrentUserId();
            $data = [
                'nama_lengkap' => sanitize($_POST['nama_lengkap']),
                'nomor_hp' => sanitize($_POST['nomor_hp']),
                'alamat' => sanitize($_POST['alamat']),
                'kota' => sanitize($_POST['kota']),
                'provinsi' => sanitize($_POST['provinsi']),
                'kode_pos' => sanitize($_POST['kode_pos'])
            ];
            
            // Handle photo upload
            if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] === 0) {
                $upload = uploadFile($_FILES['foto_profil'], UPLOAD_PATH);
                if ($upload['success']) {
                    // Delete old photo
                    $user = $this->userModel->getUserById($userId);
                    if ($user['foto_profil']) {
                        deleteFile(UPLOAD_PATH . $user['foto_profil']);
                    }
                    $data['foto_profil'] = $upload['filename'];
                }
            }
            
            $result = updateProfile($userId, $data);
            
            if ($result['success']) {
                setFlash('success', 'Profil berhasil diupdate');
                redirect(SITE_URL . '/profile.php');
            } else {
                setFlash('error', $result['message']);
                redirect(SITE_URL . '/profile.php');
            }
        }
    }
    
    /**
     * Show change password page
     */
    public function showChangePassword() {
        requireLogin();
        require_once BASE_PATH . '/views/user/change-password.php';
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = getCurrentUserId();
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if ($newPassword !== $confirmPassword) {
                setFlash('error', 'Password baru tidak cocok');
                redirect(SITE_URL . '/change-password.php');
            }
            
            if (strlen($newPassword) < 6) {
                setFlash('error', 'Password minimal 6 karakter');
                redirect(SITE_URL . '/change-password.php');
            }
            
            $result = changePassword($userId, $currentPassword, $newPassword);
            
            if ($result['success']) {
                setFlash('success', 'Password berhasil diubah');
                redirect(SITE_URL . '/profile.php');
            } else {
                setFlash('error', $result['message']);
                redirect(SITE_URL . '/change-password.php');
            }
        }
    }
}

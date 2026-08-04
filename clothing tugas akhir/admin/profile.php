<?php
/**
 * Admin Profile Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

// Check if user is admin
requireAdmin();

require_once BASE_PATH . '/models/User.php';

$userModel = new User();

$userId = $_SESSION['user_id'];
$user = $userModel->getUserById($userId);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_lengkap' => sanitize($_POST['nama_lengkap']),
        'email' => sanitize($_POST['email']),
        'nomor_hp' => sanitize($_POST['nomor_hp']),
        'alamat' => sanitize($_POST['alamat'])
    ];
    
    if (empty($data['nama_lengkap']) || empty($data['email'])) {
        setFlash('error', 'Nama dan email wajib diisi');
        redirect(SITE_URL . '/admin/profile.php');
    }
    
    if (!validateEmail($data['email'])) {
        setFlash('error', 'Email tidak valid');
        redirect(SITE_URL . '/admin/profile.php');
    }
    
    // Check if email is already taken by another user
    $existingUser = $userModel->getUserByEmail($data['email']);
    if ($existingUser && $existingUser['id'] != $userId) {
        setFlash('error', 'Email sudah digunakan oleh pengguna lain');
        redirect(SITE_URL . '/admin/profile.php');
    }
    
    // Handle password change
    if (!empty($_POST['password'])) {
        if ($_POST['password'] !== $_POST['password_confirm']) {
            setFlash('error', 'Konfirmasi password tidak cocok');
            redirect(SITE_URL . '/admin/profile.php');
        }
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    $result = $userModel->updateUser($userId, $data);
    
    if ($result) {
        setFlash('success', 'Profil berhasil diperbarui');
        redirect(SITE_URL . '/admin/profile.php');
    } else {
        setFlash('error', 'Gagal memperbarui profil');
        redirect(SITE_URL . '/admin/profile.php');
    }
}

$pageTitle = 'Profil Admin';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Profil Admin</h2>
    <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
    </a>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card text-center">
            <div class="card-body">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 60px;">
                        <?php echo strtoupper(substr($user['nama_lengkap'], 0, 1)); ?>
                    </div>
                </div>
                <h5 class="card-title"><?php echo htmlspecialchars($user['nama_lengkap']); ?></h5>
                <p class="card-text text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="badge bg-danger">Admin</span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Edit Profil</h5>
                
                <form method="POST" action="">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="nomor_hp" class="form-label">Nomor HP</label>
                        <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="<?php echo htmlspecialchars($user['nomor_hp'] ?? ''); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3">Ganti Password</h6>
                    <p class="text-muted small mb-3">Biarkan kosong jika tidak ingin mengubah password</p>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirm" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

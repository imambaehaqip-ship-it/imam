<?php
/**
 * Admin User Form Page
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

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user = null;
$isEdit = false;

if ($userId > 0) {
    $user = $userModel->getUserById($userId);
    if (!$user) {
        setFlash('error', 'Pengguna tidak ditemukan');
        redirect(SITE_URL . '/admin/users.php');
    }
    $isEdit = true;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama_lengkap' => sanitize($_POST['nama_lengkap']),
        'email' => sanitize($_POST['email']),
        'nomor_hp' => sanitize($_POST['nomor_hp']),
        'alamat' => sanitize($_POST['alamat']),
        'kota' => sanitize($_POST['kota']),
        'provinsi' => sanitize($_POST['provinsi']),
        'kode_pos' => sanitize($_POST['kode_pos']),
        'role' => sanitize($_POST['role']),
        'status' => sanitize($_POST['status'])
    ];
    
    if (empty($data['nama_lengkap']) || empty($data['email'])) {
        setFlash('error', 'Nama dan email wajib diisi');
        redirect(SITE_URL . '/admin/user-form.php' . ($isEdit ? '?id=' . $userId : ''));
    }
    
    if (!validateEmail($data['email'])) {
        setFlash('error', 'Email tidak valid');
        redirect(SITE_URL . '/admin/user-form.php' . ($isEdit ? '?id=' . $userId : ''));
    }
    
    // Check if email is already taken
    $existingUser = $userModel->getUserByEmail($data['email']);
    if ($existingUser && (!$isEdit || $existingUser['id'] != $userId)) {
        setFlash('error', 'Email sudah digunakan');
        redirect(SITE_URL . '/admin/user-form.php' . ($isEdit ? '?id=' . $userId : ''));
    }
    
    // Handle password
    if (!empty($_POST['password'])) {
        $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    
    if ($isEdit) {
        $result = $userModel->updateUser($userId, $data);
        if ($result) {
            setFlash('success', 'Pengguna berhasil diperbarui');
            redirect(SITE_URL . '/admin/users.php');
        }
    } else {
        if (empty($_POST['password'])) {
            setFlash('error', 'Password wajib diisi untuk pengguna baru');
            redirect(SITE_URL . '/admin/user-form.php');
        }
        $result = $userModel->createUser($data);
        if ($result) {
            setFlash('success', 'Pengguna berhasil ditambahkan');
            redirect(SITE_URL . '/admin/users.php');
        }
    }
    
    setFlash('error', 'Gagal menyimpan pengguna');
    redirect(SITE_URL . '/admin/user-form.php' . ($isEdit ? '?id=' . $userId : ''));
}

$pageTitle = $isEdit ? 'Edit Pengguna' : 'Tambah Pengguna';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?php echo $pageTitle; ?></h2>
    <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap *</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" value="<?php echo $isEdit ? htmlspecialchars($user['nama_lengkap']) : ''; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $isEdit ? htmlspecialchars($user['email']) : ''; ?>" required>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label"><?php echo $isEdit ? 'Password (Biarkan kosong jika tidak ingin mengubah)' : 'Password *'; ?></label>
                    <input type="password" class="form-control" id="password" name="password" <?php echo !$isEdit ? 'required' : ''; ?>>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="nomor_hp" class="form-label">Nomor HP</label>
                    <input type="text" class="form-control" id="nomor_hp" name="nomor_hp" value="<?php echo $isEdit ? htmlspecialchars($user['nomor_hp'] ?? '') : ''; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $isEdit ? htmlspecialchars($user['alamat'] ?? '') : ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kota" class="form-label">Kota</label>
                    <input type="text" class="form-control" id="kota" name="kota" value="<?php echo $isEdit ? htmlspecialchars($user['kota'] ?? '') : ''; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="provinsi" class="form-label">Provinsi</label>
                    <input type="text" class="form-control" id="provinsi" name="provinsi" value="<?php echo $isEdit ? htmlspecialchars($user['provinsi'] ?? '') : ''; ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="kode_pos" class="form-label">Kode Pos</label>
                    <input type="text" class="form-control" id="kode_pos" name="kode_pos" value="<?php echo $isEdit ? htmlspecialchars($user['kode_pos'] ?? '') : ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="user" <?php echo $isEdit && $user['role'] == 'user' ? 'selected' : (!$isEdit ? 'selected' : ''); ?>>User</option>
                        <option value="admin" <?php echo $isEdit && $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="aktif" <?php echo $isEdit && $user['status'] == 'aktif' ? 'selected' : (!$isEdit ? 'selected' : ''); ?>>Aktif</option>
                        <option value="nonaktif" <?php echo $isEdit && $user['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo SITE_URL; ?>/admin/users.php" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Simpan Perubahan' : 'Tambah Pengguna'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

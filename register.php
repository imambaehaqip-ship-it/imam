<?php
/**
 * Register Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

if (isLoggedIn()) {
    redirect(SITE_URL . '/index.php');
}

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
    
    if ($_POST['password'] !== $_POST['confirm_password']) {
        setFlash('error', 'Password tidak cocok');
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

$pageTitle = 'Register';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<!-- Register Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Daftar</h2>
                            <p class="text-muted">Buat akun baru</p>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" name="nama_lengkap" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password *</label>
                                <input type="password" class="form-control" name="password" required minlength="6">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password *</label>
                                <input type="password" class="form-control" name="confirm_password" required minlength="6">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Nomor HP</label>
                                <input type="tel" class="form-control" name="nomor_hp">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="alamat" rows="2"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Kota</label>
                                    <input type="text" class="form-control" name="kota" value="Mataram">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Provinsi</label>
                                    <input type="text" class="form-control" name="provinsi" value="Nusa Tenggara Barat">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" name="kode_pos">
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Daftar</button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Sudah punya akun? <a href="<?php echo SITE_URL; ?>/login.php">Login</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

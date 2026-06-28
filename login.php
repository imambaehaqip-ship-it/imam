<?php
/**
 * Login Page
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
    }
}

$pageTitle = 'Login';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<!-- Login Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Login</h2>
                            <p class="text-muted">Masuk ke akun Anda</p>
                        </div>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">Ingat Saya</label>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                        </form>
                        
                        <div class="text-center">
                            <p class="mb-0">Belum punya akun? <a href="<?php echo SITE_URL; ?>/register.php">Daftar Sekarang</a></p>
                            <p class="mt-2 mb-0"><a href="<?php echo SITE_URL; ?>/admin/login.php">Login Admin</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

<?php
/**
 * Admin Login Page
 * Mamz Clothing - Fashion Marketplace
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

if (isAdmin()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

if (isLoggedIn() && !isAdmin()) {
    setFlash('error', 'Akun Anda bukan admin');
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
            setFlash('error', 'Akun ini bukan admin');
        }
    } else {
        setFlash('error', $result['message']);
    }
}

$pageTitle = 'Login Admin';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="card-title">Login Admin</h2>
                            <p class="text-muted">Masuk ke panel administrasi</p>
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

                            <button type="submit" class="btn btn-primary w-100">Login Admin</button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="<?php echo SITE_URL; ?>/login.php">Kembali ke Login User</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

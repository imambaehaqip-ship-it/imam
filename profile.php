<?php
/**
 * User Profile Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    setFlash('error', 'Silakan login terlebih dahulu');
    redirect(SITE_URL . '/login.php');
}

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Order.php';

$userModel = new User();
$orderModel = new Order();

$userId = $_SESSION['user_id'];
$user = $userModel->getUserById($userId);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama' => sanitize($_POST['nama']),
        'email' => sanitize($_POST['email']),
        'telepon' => sanitize($_POST['telepon']),
        'alamat' => sanitize($_POST['alamat'])
    ];
    
    if (empty($data['nama']) || empty($data['email'])) {
        setFlash('error', 'Nama dan email wajib diisi');
        redirect(SITE_URL . '/profile.php');
    }
    
    if (!validateEmail($data['email'])) {
        setFlash('error', 'Email tidak valid');
        redirect(SITE_URL . '/profile.php');
    }
    
    // Check if email is already taken by another user
    $existingUser = $userModel->getUserByEmail($data['email']);
    if ($existingUser && $existingUser['id'] != $userId) {
        setFlash('error', 'Email sudah digunakan oleh pengguna lain');
        redirect(SITE_URL . '/profile.php');
    }
    
    $result = $userModel->updateUser($userId, $data);
    
    if ($result) {
        setFlash('success', 'Profil berhasil diperbarui');
        redirect(SITE_URL . '/profile.php');
    } else {
        setFlash('error', 'Gagal memperbarui profil');
        redirect(SITE_URL . '/profile.php');
    }
}

// Get recent orders
$recentOrders = $orderModel->getOrdersByUserId($userId, 5);

$pageTitle = 'Profil Saya';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<!-- Page Header -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profil Saya</li>
                    </ol>
                </nav>
                <h1 class="page-title">Profil Saya</h1>
            </div>
        </div>
    </div>
</section>

<!-- Profile Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px; font-size: 40px;">
                                <?php echo strtoupper(substr($user['nama'], 0, 1)); ?>
                            </div>
                        </div>
                        <h5 class="card-title"><?php echo htmlspecialchars($user['nama']); ?></h5>
                        <p class="text-muted mb-3"><?php echo htmlspecialchars($user['email']); ?></p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo SITE_URL; ?>/profile.php" class="btn btn-primary active">Profil</a>
                            <a href="<?php echo SITE_URL; ?>/order-history.php" class="btn btn-outline-primary">Riwayat Pesanan</a>
                            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-danger">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Profile Form -->
            <div class="col-lg-9">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Profil</h5>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="telepon" class="form-label">Nomor Telepon</label>
                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user['telepon'] ?? ''); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0">Pesanan Terbaru</h5>
                            <a href="<?php echo SITE_URL; ?>/order-history.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                        </div>
                        
                        <?php if (empty($recentOrders)): ?>
                            <p class="text-muted text-center py-3">Belum ada pesanan</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No. Order</th>
                                            <th>Tanggal</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentOrders as $order): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($order['no_order']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                                <td><?php echo formatRupiah($order['total']); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                                        <?php echo getStatusLabel($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo SITE_URL; ?>/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

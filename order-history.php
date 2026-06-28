<?php
/**
 * Order History Page
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

require_once BASE_PATH . '/models/Order.php';

$orderModel = new Order();

$userId = $_SESSION['user_id'];
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$orders = $orderModel->getOrdersByUserId($userId, $perPage, $offset);
$totalOrders = $orderModel->countOrdersByUserId($userId);
$totalPages = ceil($totalOrders / $perPage);

$pageTitle = 'Riwayat Pesanan';
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
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Pesanan</li>
                    </ol>
                </nav>
                <h1 class="page-title">Riwayat Pesanan</h1>
            </div>
        </div>
    </div>
</section>

<!-- Orders Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada pesanan</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($orders as $order): ?>
                    <div class="col-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-6">
                                        <h6 class="mb-1">Order #<?php echo htmlspecialchars($order['no_order']); ?></h6>
                                        <p class="text-muted mb-0 small"><?php echo date('d/m/Y H:i', strtotime($order['tanggal'])); ?></p>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                            <?php echo getStatusLabel($order['status']); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="mb-1"><strong>Total:</strong> <?php echo formatRupiah($order['total']); ?></p>
                                        <p class="mb-0 small text-muted">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            <?php echo htmlspecialchars($order['alamat_pengiriman']); ?>
                                        </p>
                                    </div>
                                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                        <a href="<?php echo SITE_URL; ?>/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Detail
                                        </a>
                                        <?php if ($order['status'] == 'pending' && empty($order['bukti_pembayaran'])): ?>
                                            <a href="<?php echo SITE_URL; ?>/payment-confirmation.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-upload me-1"></i>Konfirmasi Pembayaran
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

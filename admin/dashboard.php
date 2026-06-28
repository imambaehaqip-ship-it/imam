<?php
/**
 * Admin Dashboard
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
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Order.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Review.php';

$userModel = new User();
$productModel = new Product();
$orderModel = new Order();
$categoryModel = new Category();
$reviewModel = new Review();

// Get statistics
$totalUsers = $userModel->countUsers();
$totalProducts = $productModel->countProducts();
$totalOrders = $orderModel->countOrders();
$totalRevenue = $orderModel->getTotalRevenue();
$pendingOrders = $orderModel->countOrdersByStatus('pending');
$totalCategories = $categoryModel->countCategories();
$totalReviews = $reviewModel->countReviews();

// Get recent orders
$recentOrders = $orderModel->getRecentOrders(5);

// Get top products
$topProducts = $productModel->getTopProducts(5);

// Get monthly sales for chart
$monthlySales = $orderModel->getMonthlySales();

$pageTitle = 'Dashboard';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<!-- Stats Cards -->
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Pengguna</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($totalUsers); ?></h3>
                    </div>
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Produk</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($totalProducts); ?></h3>
                    </div>
                    <div class="stats-icon bg-success">
                        <i class="fas fa-box"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Pesanan</h6>
                        <h3 class="card-title mb-0"><?php echo number_format($totalOrders); ?></h3>
                    </div>
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2">Total Pendapatan</h6>
                        <h3 class="card-title mb-0"><?php echo formatRupiah($totalRevenue); ?></h3>
                    </div>
                    <div class="stats-icon bg-info">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Penjualan Bulanan</h5>
                <canvas id="salesChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Status Pesanan</h5>
                <canvas id="orderStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Top Products -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Pesanan Terbaru</h5>
                    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted text-center py-3">Belum ada pesanan</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Order</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($order['no_order']); ?></td>
                                        <td><?php echo htmlspecialchars($order['nama_penerima']); ?></td>
                                        <td><?php echo formatRupiah($order['total']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                                <?php echo getStatusLabel($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title mb-0">Produk Terlaris</h5>
                    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                
                <?php if (empty($topProducts)): ?>
                    <p class="text-muted text-center py-3">Belum ada data</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($topProducts as $index => $product): ?>
                            <div class="list-group-item d-flex align-items-center">
                                <span class="badge bg-primary me-3"><?php echo $index + 1; ?></span>
                                <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>" class="rounded me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0"><?php echo htmlspecialchars($product['nama']); ?></h6>
                                    <small class="text-muted"><?php echo $product['total_sold']; ?> terjual</small>
                                </div>
                                <span class="text-muted"><?php echo formatRupiah($product['harga']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Penjualan',
            data: <?php echo json_encode(array_column($monthlySales, 'total')); ?>,
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13, 110, 253, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Order Status Chart
const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
new Chart(orderStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Diproses', 'Dikirim', 'Selesai', 'Dibatalkan'],
        datasets: [{
            data: [
                <?php echo $orderModel->countOrdersByStatus('pending'); ?>,
                <?php echo $orderModel->countOrdersByStatus('processed'); ?>,
                <?php echo $orderModel->countOrdersByStatus('shipped'); ?>,
                <?php echo $orderModel->countOrdersByStatus('completed'); ?>,
                <?php echo $orderModel->countOrdersByStatus('cancelled'); ?>
            ],
            backgroundColor: ['#ffc107', '#0d6efd', '#17a2b8', '#198754', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

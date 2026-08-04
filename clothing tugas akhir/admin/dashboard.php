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

// Greeting based on time of day
$hour = (int) date('H');
if ($hour < 11) {
    $greeting = 'Selamat pagi';
} elseif ($hour < 15) {
    $greeting = 'Selamat siang';
} elseif ($hour < 19) {
    $greeting = 'Selamat sore';
} else {
    $greeting = 'Selamat malam';
}
$adminName = $_SESSION['user_nama'] ?? 'Admin';
$firstName = explode(' ', trim($adminName))[0];

// Indonesian date
$hariList = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
$bulanList = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
$today = $hariList[date('w')] . ', ' . date('j') . ' ' . $bulanList[(int)date('n')] . ' ' . date('Y');

// Map order status to the badge CSS class used in admin.css
function dashStatusClass($status) {
    $map = [
        'pending' => 'pending', 'menunggu' => 'pending',
        'processed' => 'diproses', 'diproses' => 'diproses',
        'shipped' => 'dikirim', 'dikirim' => 'dikirim',
        'completed' => 'selesai', 'selesai' => 'selesai',
        'cancelled' => 'dibatalkan', 'dibatalkan' => 'dibatalkan',
    ];
    return $map[$status] ?? 'pending';
}

$pageTitle = 'Dashboard';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<!-- Welcome Banner -->
<div class="dash-welcome">
    <div class="dash-welcome-text">
        <span class="dash-welcome-date"><?php echo $today; ?></span>
        <h2><?php echo $greeting; ?>, <?php echo htmlspecialchars($firstName); ?> 👋</h2>
        <p>Begini kabar toko <?php echo htmlspecialchars(SITE_NAME); ?> hari ini.</p>
    </div>
    <?php if ($pendingOrders > 0): ?>
    <a href="<?php echo SITE_URL; ?>/admin/orders.php?status=pending" class="dash-welcome-alert">
        <i class="fas fa-bell"></i>
        <span><strong><?php echo $pendingOrders; ?></strong> pesanan menunggu diproses</span>
        <i class="fas fa-arrow-right"></i>
    </a>
    <?php endif; ?>
    <div class="dash-welcome-deco"><i class="fas fa-shirt"></i></div>
</div>

<!-- Stats Cards -->
<div class="dash-stats-grid">
    <div class="dash-stat-card">
        <div class="dash-stat-icon grad-blue"><i class="fas fa-users"></i></div>
        <div class="dash-stat-info">
            <h6>Total Pengguna</h6>
            <h3><?php echo number_format($totalUsers); ?></h3>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="dash-stat-icon grad-emerald"><i class="fas fa-shirt"></i></div>
        <div class="dash-stat-info">
            <h6>Total Produk</h6>
            <h3><?php echo number_format($totalProducts); ?></h3>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="dash-stat-icon grad-amber"><i class="fas fa-bag-shopping"></i></div>
        <div class="dash-stat-info">
            <h6>Total Pesanan</h6>
            <h3><?php echo number_format($totalOrders); ?></h3>
            <?php if ($pendingOrders > 0): ?>
                <span class="dash-stat-tag"><?php echo $pendingOrders; ?> pending</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="dash-stat-card">
        <div class="dash-stat-icon grad-violet"><i class="fas fa-sack-dollar"></i></div>
        <div class="dash-stat-info">
            <h6>Total Pendapatan</h6>
            <h3><?php echo formatRupiah($totalRevenue); ?></h3>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h5><i class="fas fa-chart-line me-2"></i>Penjualan Bulanan</h5>
                    <p>Performa penjualan 12 bulan terakhir</p>
                </div>
            </div>
            <div class="dash-card-body">
                <canvas id="salesChart" height="280"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="dash-card h-100">
            <div class="dash-card-header">
                <div>
                    <h5><i class="fas fa-chart-pie me-2"></i>Status Pesanan</h5>
                    <p>Distribusi status saat ini</p>
                </div>
            </div>
            <div class="dash-card-body">
                <canvas id="orderStatusChart" height="280"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders & Top Products -->
<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="dash-card">
            <div class="dash-card-header">
                <div>
                    <h5><i class="fas fa-receipt me-2"></i>Pesanan Terbaru</h5>
                    <p>5 transaksi paling baru masuk</p>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="dash-link-btn">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($recentOrders)): ?>
                <div class="dash-empty">
                    <i class="fas fa-inbox"></i>
                    <p>Belum ada pesanan masuk</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="admin-table dash-table">
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
                                    <td><span class="dash-order-no"><?php echo htmlspecialchars($order['no_order']); ?></span></td>
                                    <td><?php echo htmlspecialchars($order['nama_penerima']); ?></td>
                                    <td><strong><?php echo formatRupiah($order['total']); ?></strong></td>
                                    <td>
                                        <span class="status-badge <?php echo dashStatusClass($order['status']); ?>">
                                            <?php echo getStatusLabel($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted"><?php echo date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="dash-card">
            <div class="dash-card-header">
                <div>
                    <h5><i class="fas fa-fire me-2"></i>Produk Terlaris</h5>
                    <p>Berdasarkan jumlah terjual</p>
                </div>
                <a href="<?php echo SITE_URL; ?>/admin/products.php" class="dash-link-btn">
                    Lihat Semua <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <?php if (empty($topProducts)): ?>
                <div class="dash-empty">
                    <i class="fas fa-box-open"></i>
                    <p>Belum ada data penjualan</p>
                </div>
            <?php else: ?>
                <div class="dash-product-list">
                    <?php foreach ($topProducts as $index => $product): ?>
                        <div class="dash-product-item">
                            <span class="dash-rank rank-<?php echo $index + 1; ?>"><?php echo $index + 1; ?></span>
                            <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['gambar']); ?>" alt="<?php echo htmlspecialchars($product['nama']); ?>">
                            <div class="dash-product-info">
                                <h6><?php echo htmlspecialchars($product['nama']); ?></h6>
                                <small><?php echo $product['total_sold']; ?> terjual</small>
                            </div>
                            <span class="dash-product-price"><?php echo formatRupiah($product['harga']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Sales Chart
const salesCtx = document.getElementById('salesChart').getContext('2d');
const salesGradient = salesCtx.createLinearGradient(0, 0, 0, 280);
salesGradient.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
salesGradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

new Chart(salesCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
        datasets: [{
            label: 'Penjualan',
            data: <?php echo json_encode(array_column($monthlySales, 'total')); ?>,
            borderColor: '#2563EB',
            backgroundColor: salesGradient,
            borderWidth: 3,
            pointBackgroundColor: '#2563EB',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#0F172A',
                padding: 12,
                cornerRadius: 8,
                displayColors: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: '#F1F5F9' },
                ticks: { color: '#94A3B8' }
            },
            x: {
                grid: { display: false },
                ticks: { color: '#94A3B8' }
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
            backgroundColor: ['#F59E0B', '#2563EB', '#3B82F6', '#10B981', '#EF4444'],
            borderWidth: 3,
            borderColor: '#fff',
            hoverOffset: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '68%',
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 16,
                    usePointStyle: true,
                    pointStyle: 'circle',
                    color: '#475569',
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: '#0F172A',
                padding: 12,
                cornerRadius: 8
            }
        }
    }
});
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>
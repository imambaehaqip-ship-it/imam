<?php
/**
 * Admin Orders Page
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

require_once BASE_PATH . '/models/Order.php';

$orderModel = new Order();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$orders = $orderModel->getAllOrders($perPage, $offset, $statusFilter);
$totalOrders = $orderModel->countOrders($statusFilter);
$totalPages = ceil($totalOrders / $perPage);

$pageTitle = 'Kelola Pesanan';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Kelola Pesanan</h2>
    <div class="d-flex gap-2">
        <select class="form-select" onchange="location.href='<?php echo SITE_URL; ?>/admin/orders.php?status='+this.value" style="width: auto;">
            <option value="">Semua Status</option>
            <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="processed" <?php echo $statusFilter == 'processed' ? 'selected' : ''; ?>>Diproses</option>
            <option value="shipped" <?php echo $statusFilter == 'shipped' ? 'selected' : ''; ?>>Dikirim</option>
            <option value="completed" <?php echo $statusFilter == 'completed' ? 'selected' : ''; ?>>Selesai</option>
            <option value="cancelled" <?php echo $statusFilter == 'cancelled' ? 'selected' : ''; ?>>Dibatalkan</option>
        </select>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada pesanan ditemukan</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Order</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($order['no_order']); ?></strong>
                                </td>
                                <td>
                                    <div><?php echo htmlspecialchars($order['nama_penerima']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                </td>
                                <td><?php echo formatRupiah($order['total']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                        <?php echo getStatusLabel($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($order['bukti_pembayaran'])): ?>
                                        <span class="badge bg-success">Sudah Upload</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Belum Upload</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($order['tanggal'])); ?></td>
                                <td>
                                    <a href="<?php echo SITE_URL; ?>/admin/order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

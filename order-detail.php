<?php
/**
 * Order Detail Page
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
require_once BASE_PATH . '/models/OrderDetail.php';

$orderModel = new Order();
$orderDetailModel = new OrderDetail();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];

$order = $orderModel->getOrderById($orderId);

if (!$order || $order['user_id'] != $userId) {
    setFlash('error', 'Pesanan tidak ditemukan');
    redirect(SITE_URL . '/order-history.php');
}

$orderDetails = $orderDetailModel->getOrderDetailsByOrderId($orderId);

$pageTitle = 'Detail Pesanan #' . $order['no_order'];
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
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/order-history.php">Riwayat Pesanan</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Detail Pesanan</li>
                    </ol>
                </nav>
                <h1 class="page-title">Detail Pesanan #<?php echo htmlspecialchars($order['no_order']); ?></h1>
            </div>
        </div>
    </div>
</section>

<!-- Order Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Order Info -->
            <div class="col-lg-8 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Pesanan</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>No. Order:</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($order['no_order']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal:</strong></p>
                                <p class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order['tanggal'])); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status:</strong></p>
                                <span class="badge bg-<?php echo getStatusBadgeColor($order['status']); ?>">
                                    <?php echo getStatusLabel($order['status']); ?>
                                </span>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Metode Pembayaran:</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($order['metode_pembayaran']); ?></p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <p class="mb-1"><strong>Alamat Pengiriman:</strong></p>
                            <p class="text-muted">
                                <?php echo htmlspecialchars($order['nama_penerima']); ?><br>
                                <?php echo htmlspecialchars($order['telepon_penerima']); ?><br>
                                <?php echo htmlspecialchars($order['alamat_pengiriman']); ?>
                                <?php if (!empty($order['catatan'])): ?>
                                    <br><small class="text-muted">Catatan: <?php echo htmlspecialchars($order['catatan']); ?></small>
                                <?php endif; ?>
                            </p>
                        </div>
                        
                        <?php if (!empty($order['kode_promo'])): ?>
                            <div class="mb-3">
                                <p class="mb-1"><strong>Kode Promo:</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($order['kode_promo']); ?> (Diskon: <?php echo formatRupiah($order['diskon']); ?>)</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Item Pesanan</h5>
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th>Harga</th>
                                        <th>Jumlah</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orderDetails as $detail): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($detail['gambar']); ?>" alt="<?php echo htmlspecialchars($detail['nama_produk']); ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0"><?php echo htmlspecialchars($detail['nama_produk']); ?></h6>
                                                        <small class="text-muted">
                                                            <?php if (!empty($detail['ukuran'])) echo 'Ukuran: ' . htmlspecialchars($detail['ukuran']); ?>
                                                            <?php if (!empty($detail['warna'])) echo ' | Warna: ' . htmlspecialchars($detail['warna']); ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?php echo formatRupiah($detail['harga']); ?></td>
                                            <td><?php echo $detail['jumlah']; ?></td>
                                            <td><?php echo formatRupiah($detail['subtotal']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                        <td><?php echo formatRupiah($order['subtotal']); ?></td>
                                    </tr>
                                    <?php if ($order['diskon'] > 0): ?>
                                        <tr>
                                            <td colspan="3" class="text-end"><strong>Diskon:</strong></td>
                                            <td class="text-success">-<?php echo formatRupiah($order['diskon']); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Ongkir:</strong></td>
                                        <td><?php echo formatRupiah($order['ongkir']); ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td class="fw-bold"><?php echo formatRupiah($order['total']); ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Payment Info -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Pembayaran</h5>
                        
                        <?php if (!empty($order['bukti_pembayaran'])): ?>
                            <div class="mb-3">
                                <p class="mb-2"><strong>Bukti Pembayaran:</strong></p>
                                <img src="<?php echo SITE_URL; ?>/uploads/payment/<?php echo htmlspecialchars($order['bukti_pembayaran']); ?>" alt="Bukti Pembayaran" class="img-fluid rounded mb-2">
                                <p class="small text-muted">Diupload pada: <?php echo date('d/m/Y H:i', strtotime($order['tanggal_upload_bukti'])); ?></p>
                            </div>
                            
                            <?php if (!empty($order['catatan_admin'])): ?>
                                <div class="alert alert-info">
                                    <p class="mb-0"><strong>Catatan Admin:</strong></p>
                                    <p class="mb-0 small"><?php echo htmlspecialchars($order['catatan_admin']); ?></p>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php if ($order['status'] == 'pending'): ?>
                                <div class="alert alert-warning">
                                    <p class="mb-0">Silakan upload bukti pembayaran untuk memproses pesanan Anda.</p>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/payment-confirmation.php?id=<?php echo $order['id']; ?>" class="btn btn-primary w-100">
                                    <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                                </a>
                            <?php else: ?>
                                <p class="text-muted">Belum ada bukti pembayaran</p>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Bank Info -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Transfer</h5>
                        
                        <div class="mb-3">
                            <p class="mb-1"><strong>Bank BCA:</strong></p>
                            <p class="text-muted">123-456-7890</p>
                            <p class="text-muted">a.n. Mamz Clothing</p>
                        </div>
                        
                        <div class="mb-3">
                            <p class="mb-1"><strong>Bank Mandiri:</strong></p>
                            <p class="text-muted">098-765-4321</p>
                            <p class="text-muted">a.n. Mamz Clothing</p>
                        </div>
                        
                        <div>
                            <p class="mb-1"><strong>E-Wallet (OVO/Gopay/Dana):</strong></p>
                            <p class="text-muted">0812-3456-7890</p>
                            <p class="text-muted">a.n. Mamz Clothing</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

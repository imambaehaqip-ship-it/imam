<?php
/**
 * Admin Order Detail Page
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

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$order = $orderModel->getOrderById($orderId);

if (!$order) {
    setFlash('error', 'Pesanan tidak ditemukan');
    redirect(SITE_URL . '/admin/orders.php');
}

$orderDetails = $orderModel->getOrderDetails($orderId);

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $newStatus = sanitize($_POST['status']);
    $catatan = sanitize($_POST['catatan_admin'] ?? '');
    
    $result = $orderModel->updateOrderStatus($orderId, $newStatus, $catatan);
    
    if ($result) {
        setFlash('success', 'Status pesanan berhasil diperbarui');
        redirect(SITE_URL . '/admin/order-detail.php?id=' . $orderId);
    } else {
        setFlash('error', 'Gagal memperbarui status');
        redirect(SITE_URL . '/admin/order-detail.php?id=' . $orderId);
    }
}

$pageTitle = 'Detail Pesanan #' . $order['no_order'];
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Detail Pesanan #<?php echo htmlspecialchars($order['no_order']); ?></h2>
    <a href="<?php echo SITE_URL; ?>/admin/orders.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <!-- Order Info -->
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
                        <p class="text-muted">Transfer Bank</p>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Informasi Pelanggan:</strong></p>
                    <p class="text-muted">
                        <strong>Nama:</strong> <?php echo htmlspecialchars($order['nama_penerima']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?><br>
                        <strong>Telepon:</strong> <?php echo htmlspecialchars($order['telepon_penerima'] ?? '-'); ?>
                    </p>
                </div>
                
                <div class="mb-3">
                    <p class="mb-1"><strong>Alamat Pengiriman:</strong></p>
                    <p class="text-muted"><?php echo htmlspecialchars($order['alamat_pengiriman'] ?? '-'); ?></p>
                    <?php if (!empty($order['catatan'])): ?>
                        <p class="text-muted"><small><strong>Catatan:</strong> <?php echo htmlspecialchars($order['catatan']); ?></small></p>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($order['catatan'])): ?>
                    <div class="mb-3">
                        <p class="mb-1"><strong>Catatan:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($order['catatan']); ?></p>
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
                                            <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($detail['gambar'] ?? ''); ?>" alt="<?php echo htmlspecialchars($detail['nama_produk']); ?>" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
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
                                <td><?php echo formatRupiah($order['total']); ?></td>
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
    
    <div class="col-lg-4">
        <!-- Update Status -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-4">Update Status</h5>
                
                <form method="POST" action="">
                    <input type="hidden" name="update_status" value="1">
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status Pesanan</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="diproses" <?php echo $order['status'] == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                            <option value="dikirim" <?php echo $order['status'] == 'dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                            <option value="selesai" <?php echo $order['status'] == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                            <option value="dibatalkan" <?php echo $order['status'] == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_admin" class="form-label">Catatan Admin</label>
                        <textarea class="form-control" id="catatan_admin" name="catatan_admin" rows="3"><?php echo htmlspecialchars($order['catatan_admin'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-2"></i>Update Status
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Payment Proof -->
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-4">Bukti Pembayaran</h5>
                
                <?php 
                $paymentProof = $orderModel->getPaymentProofByOrderId($orderId);
                if ($paymentProof): 
                ?>
                    <div class="mb-3">
                        <p class="mb-2"><strong>Status:</strong></p>
                        <span class="badge bg-<?php 
                            echo ($paymentProof['status'] == 'diterima') ? 'success' : 
                                 (($paymentProof['status'] == 'ditolak') ? 'danger' : 'warning'); 
                        ?>">
                            <?php 
                            echo ($paymentProof['status'] == 'menunggu_verifikasi') ? 'Menunggu Verifikasi' :
                                 (($paymentProof['status'] == 'diterima') ? 'Diterima' : 'Ditolak');
                            ?>
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Bukti Transfer:</strong></p>
                        <img src="<?php echo SITE_URL; ?>/uploads/payment/<?php echo htmlspecialchars($paymentProof['bukti_pembayaran']); ?>" alt="Bukti Pembayaran" class="img-fluid rounded mb-2" style="max-width: 100%; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#paymentProofModal">
                        <p class="small text-muted mb-0">Klik untuk memperbesar</p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Tanggal Upload:</strong></p>
                        <p class="text-muted"><?php echo date('d/m/Y H:i', strtotime($paymentProof['created_at'])); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Metode Pembayaran:</strong></p>
                        <p class="text-muted"><?php echo htmlspecialchars($paymentProof['metode_pembayaran']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Nominal Transfer:</strong></p>
                        <p class="text-muted"><?php echo formatRupiah($paymentProof['nominal_transfer']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-2"><strong>Tanggal Transfer:</strong></p>
                        <p class="text-muted"><?php echo date('d/m/Y', strtotime($paymentProof['tanggal_transfer'])); ?></p>
                    </div>
                    
                    <?php if (!empty($paymentProof['catatan_admin'])): ?>
                        <div class="alert alert-info">
                            <p class="mb-0"><strong>Catatan Admin:</strong></p>
                            <p class="mb-0 small"><?php echo htmlspecialchars($paymentProof['catatan_admin']); ?></p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada bukti pembayaran yang diupload</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Payment Proof -->
<div class="modal fade" id="paymentProofModal" tabindex="-1" aria-labelledby="paymentProofModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentProofModalLabel">Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <?php 
                $paymentProof = $orderModel->getPaymentProofByOrderId($orderId);
                if ($paymentProof): 
                ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/payment/<?php echo htmlspecialchars($paymentProof['bukti_pembayaran']); ?>" alt="Bukti Pembayaran" class="img-fluid">
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

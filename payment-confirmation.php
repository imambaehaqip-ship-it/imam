<?php
/**
 * Payment Confirmation Page
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

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId = $_SESSION['user_id'];

$order = $orderModel->getOrderById($orderId);

if (!$order || $order['user_id'] != $userId) {
    setFlash('error', 'Pesanan tidak ditemukan');
    redirect(SITE_URL . '/order-history.php');
}

if ($order['status'] != 'pending') {
    setFlash('error', 'Pesanan ini tidak memerlukan konfirmasi pembayaran');
    redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
}

// Handle payment proof upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
        setFlash('error', 'Silakan upload bukti pembayaran');
        redirect(SITE_URL . '/payment-confirmation.php?id=' . $orderId);
    }
    
    $file = $_FILES['bukti_pembayaran'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxSize = MAX_FILE_SIZE;
    
    if (!in_array($file['type'], $allowedTypes)) {
        setFlash('error', 'Format file tidak valid. Hanya JPG, PNG, dan WebP yang diperbolehkan.');
        redirect(SITE_URL . '/payment-confirmation.php?id=' . $orderId);
    }
    
    if ($file['size'] > $maxSize) {
        setFlash('error', 'Ukuran file terlalu besar. Maksimal ' . ($maxSize / 1024 / 1024) . 'MB.');
        redirect(SITE_URL . '/payment-confirmation.php?id=' . $orderId);
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'payment_' . $order['no_order'] . '_' . time() . '.' . $extension;
    $uploadPath = UPLOAD_PATH . '/payment/' . $filename;
    
    if (!is_dir(UPLOAD_PATH . '/payment')) {
        mkdir(UPLOAD_PATH . '/payment', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $result = $orderModel->updatePaymentProof($orderId, $filename);
        
        if ($result) {
            setFlash('success', 'Bukti pembayaran berhasil diupload. Admin akan memverifikasi pembayaran Anda.');
            redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
        } else {
            setFlash('error', 'Gagal mengupdate bukti pembayaran');
            redirect(SITE_URL . '/payment-confirmation.php?id=' . $orderId);
        }
    } else {
        setFlash('error', 'Gagal mengupload file');
        redirect(SITE_URL . '/payment-confirmation.php?id=' . $orderId);
    }
}

$pageTitle = 'Konfirmasi Pembayaran';
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
                        <li class="breadcrumb-item active" aria-current="page">Konfirmasi Pembayaran</li>
                    </ol>
                </nav>
                <h1 class="page-title">Konfirmasi Pembayaran</h1>
            </div>
        </div>
    </div>
</section>

<!-- Payment Confirmation Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Pesanan</h5>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>No. Order:</strong></p>
                                <p class="text-muted"><?php echo htmlspecialchars($order['no_order']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Total Pembayaran:</strong></p>
                                <p class="text-primary fw-bold fs-5"><?php echo formatRupiah($order['total']); ?></p>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <p class="mb-0"><i class="fas fa-info-circle me-2"></i>Silakan transfer ke salah satu rekening berikut dan upload bukti pembayaran.</p>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Transfer</h5>
                        
                        <div class="mb-4">
                            <div class="p-3 bg-light rounded">
                                <p class="mb-1"><strong>Bank BCA:</strong></p>
                                <p class="mb-0 fs-5"><?php echo '123-456-7890'; ?></p>
                                <p class="mb-0 text-muted">a.n. Mamz Clothing</p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="p-3 bg-light rounded">
                                <p class="mb-1"><strong>Bank Mandiri:</strong></p>
                                <p class="mb-0 fs-5"><?php echo '098-765-4321'; ?></p>
                                <p class="mb-0 text-muted">a.n. Mamz Clothing</p>
                            </div>
                        </div>
                        
                        <div>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-1"><strong>E-Wallet (OVO/Gopay/Dana):</strong></p>
                                <p class="mb-0 fs-5"><?php echo '0812-3456-7890'; ?></p>
                                <p class="mb-0 text-muted">a.n. Mamz Clothing</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Upload Bukti Pembayaran</h5>
                        
                        <form method="POST" action="" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="bukti_pembayaran" class="form-label">Bukti Transfer</label>
                                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/jpg,image/png,image/webp" required>
                                <div class="form-text">Format: JPG, PNG, WebP. Maksimal: <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?>MB.</div>
                            </div>
                            
                            <div class="alert alert-warning">
                                <p class="mb-0 small"><i class="fas fa-exclamation-triangle me-2"></i>Pastikan bukti transfer jelas dan dapat dibaca. Pastikan nominal dan tanggal transfer terlihat.</p>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-2"></i>Upload Bukti Pembayaran
                            </button>
                            <a href="<?php echo SITE_URL; ?>/order-detail.php?id=<?php echo $orderId; ?>" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

<?php
/**
 * Checkout Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Cart.php';
require_once BASE_PATH . '/models/PaymentMethod.php';

requireLogin();

$cartModel = new Cart();
$paymentMethodModel = new PaymentMethod();

$userId = getCurrentUserId();
$cartItems = $cartModel->getCartByUserId($userId);
$cartTotal = $cartModel->getCartTotal($userId);

if (empty($cartItems)) {
    setFlash('error', 'Keranjang kosong');
    redirect(SITE_URL . '/products.php');
}

$user = getCurrentUser();
$paymentMethods = $paymentMethodModel->getAllActivePaymentMethods();

$pageTitle = 'Checkout';
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
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/cart.php">Keranjang</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Checkout</li>
                    </ol>
                </nav>
                <h1 class="page-title">Checkout</h1>
            </div>
        </div>
    </div>
</section>

<!-- Checkout Section -->
<section class="py-5">
    <div class="container">
        <form action="<?php echo SITE_URL; ?>/checkout-process.php" method="POST">
            <div class="row">
                <!-- Shipping Information -->
                <div class="col-lg-8 mb-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Penerima</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nama Lengkap *</label>
                                    <input type="text" class="form-control" name="nama_penerima" 
                                           value="<?php echo $user['nama_lengkap']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email *</label>
                                    <input type="email" class="form-control" name="email_penerima" 
                                           value="<?php echo $user['email']; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nomor HP *</label>
                                    <input type="tel" class="form-control" name="nomor_hp" 
                                           value="<?php echo $user['nomor_hp']; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Alamat Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Provinsi *</label>
                                    <input type="text" class="form-control" name="provinsi" 
                                           value="<?php echo $user['provinsi'] ?: 'Nusa Tenggara Barat'; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kota *</label>
                                    <input type="text" class="form-control" name="kota" 
                                           value="<?php echo $user['kota'] ?: 'Mataram'; ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kecamatan</label>
                                    <input type="text" class="form-control" name="kecamatan" 
                                           value="<?php echo $user['kecamatan'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kelurahan</label>
                                    <input type="text" class="form-control" name="kelurahan" 
                                           value="<?php echo $user['kelurahan'] ?? ''; ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Kode Pos</label>
                                    <input type="text" class="form-control" name="kode_pos" 
                                           value="<?php echo $user['kode_pos'] ?? ''; ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Detail Alamat *</label>
                                    <textarea class="form-control" name="detail_alamat" rows="3" required><?php echo $user['alamat'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Catatan (Opsional)</h5>
                        </div>
                        <div class="card-body">
                            <textarea class="form-control" name="catatan" rows="3" placeholder="Tambahkan catatan untuk pesanan Anda..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Ringkasan Pesanan</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between mb-3">
                                    <div>
                                        <h6 class="mb-0"><?php echo $item['nama_produk']; ?></h6>
                                        <small class="text-muted">Qty: <?php echo $item['quantity']; ?></small>
                                    </div>
                                    <span><?php echo formatPrice($item['subtotal']); ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span><?php echo formatPrice($cartTotal); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ongkos Kirim</span>
                                <span>Dihitung nanti</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total</strong>
                                <strong><?php echo formatPrice($cartTotal); ?></strong>
                            </div>
                            
                            <!-- Promo Code -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="kode_promo" placeholder="Kode Promo">
                                    <button class="btn btn-outline-secondary" type="button">Terapkan</button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-lock me-2"></i>Buat Pesanan
                            </button>
                            
                            <p class="text-center text-muted mt-3 mb-0">
                                <small>
                                    <i class="fas fa-lock me-1"></i>
                                    Pembayaran Anda aman dan terenkripsi
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

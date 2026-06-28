<?php
/**
 * Cart Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Cart.php';

requireLogin();

$cartModel = new Cart();
$userId = getCurrentUserId();
$cartItems = $cartModel->getCartByUserId($userId);
$cartTotal = $cartModel->getCartTotal($userId);

$pageTitle = 'Keranjang';
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
                        <li class="breadcrumb-item active" aria-current="page">Keranjang</li>
                    </ol>
                </nav>
                <h1 class="page-title">Keranjang Belanja</h1>
            </div>
        </div>
    </div>
</section>

<!-- Cart Section -->
<section class="py-5">
    <div class="container">
        <?php if (!empty($cartItems)): ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Produk</th>
                                            <th>Harga</th>
                                            <th>Quantity</th>
                                            <th>Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($cartItems as $item): ?>
                                            <tr id="cart-item-<?php echo $item['id']; ?>">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo $item['foto_utama'] ?: 'placeholder.jpg'; ?>" 
                                                             alt="<?php echo $item['nama_produk']; ?>" 
                                                             class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-1"><?php echo $item['nama_produk']; ?></h6>
                                                            <small class="text-muted">
                                                                <?php if ($item['ukuran']): ?>Ukuran: <?php echo $item['ukuran']; ?><?php endif; ?>
                                                                <?php if ($item['warna']): ?> | Warna: <?php echo $item['warna']; ?><?php endif; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span id="price-<?php echo $item['id']; ?>" data-price="<?php echo $item['harga_diskon']; ?>">
                                                        <?php echo formatPrice($item['harga_diskon']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="input-group" style="width: 120px;">
                                                        <button class="btn btn-outline-secondary update-qty" 
                                                                data-cart-id="<?php echo $item['id']; ?>" 
                                                                data-action="decrease">-</button>
                                                        <input type="number" class="form-control text-center" 
                                                               id="qty-<?php echo $item['id']; ?>" 
                                                               value="<?php echo $item['quantity']; ?>" 
                                                               min="1" max="<?php echo $item['stok']; ?>" readonly>
                                                        <button class="btn btn-outline-secondary update-qty" 
                                                                data-cart-id="<?php echo $item['id']; ?>" 
                                                                data-action="increase">+</button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span id="subtotal-<?php echo $item['id']; ?>">
                                                        <?php echo formatPrice($item['subtotal']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm remove-from-cart" 
                                                            data-cart-id="<?php echo $item['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Ringkasan Pesanan</h5>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal</span>
                                <span id="cart-total"><?php echo formatPrice($cartTotal); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Ongkos Kirim</span>
                                <span>Dihitung saat checkout</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total</strong>
                                <strong id="cart-total"><?php echo formatPrice($cartTotal); ?></strong>
                            </div>
                            
                            <a href="<?php echo SITE_URL; ?>/checkout.php" class="btn btn-primary btn-lg w-100 mb-2">
                                <i class="fas fa-lock me-2"></i>Checkout
                            </a>
                            
                            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline w-100">
                                <i class="fas fa-arrow-left me-2"></i>Lanjut Belanja
                            </a>
                            
                            <a href="<?php echo SITE_URL; ?>/cart.php?clear=1" class="btn btn-link text-danger w-100 mt-2 clear-cart">
                                <i class="fas fa-trash me-1"></i>Kosongkan Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                <h3>Keranjang Kosong</h3>
                <p class="text-muted mb-4">Anda belum menambahkan produk ke keranjang</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Mulai Belanja
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

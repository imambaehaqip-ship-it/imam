<?php
/**
 * Checkout Process
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Cart.php';
require_once BASE_PATH . '/models/Order.php';
require_once BASE_PATH . '/models/PaymentMethod.php';
require_once BASE_PATH . '/models/Promo.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/checkout.php');
}

$cartModel = new Cart();
$orderModel = new Order();
$paymentMethodModel = new PaymentMethodModel();
$promoModel = new Promo();

$userId = getCurrentUserId();
$cartItems = $cartModel->getCartByUserId($userId);
$cartTotal = $cartModel->getCartTotal($userId);

if (empty($cartItems)) {
    setFlash('error', 'Keranjang kosong');
    redirect(SITE_URL . '/products.php');
}

// Validate stock
foreach ($cartItems as $item) {
    if ($item['stok'] < $item['quantity']) {
        setFlash('error', 'Stok ' . $item['nama_produk'] . ' tidak mencukupi');
        redirect(SITE_URL . '/cart.php');
    }
}

// Apply promo code if provided
$discount = 0;
$promoId = null;
if (!empty($_POST['kode_promo'])) {
    $promoResult = $promoModel->validatePromo($_POST['kode_promo'], $cartTotal);
    if ($promoResult['valid']) {
        $discount = $promoModel->calculateDiscount($promoResult['promo'], $cartTotal);
        $promoId = $promoResult['promo']['id'];
        $promoModel->incrementPromoUsage($promoId);
    }
}

$finalTotal = $cartTotal - $discount;

// Create order
$orderData = [
    'user_id' => $userId,
    'nomor_pesanan' => generateOrderNumber(),
    'total_harga' => $finalTotal,
    'status_pesanan' => 'pending',
    'status_pembayaran' => 'menunggu',
    'nama_penerima' => sanitize($_POST['nama_penerima']),
    'email_penerima' => sanitize($_POST['email_penerima']),
    'nomor_hp' => sanitize($_POST['nomor_hp']),
    'provinsi' => sanitize($_POST['provinsi']),
    'kota' => sanitize($_POST['kota']),
    'kecamatan' => sanitize($_POST['kecamatan']),
    'kelurahan' => sanitize($_POST['kelurahan']),
    'kode_pos' => sanitize($_POST['kode_pos']),
    'detail_alamat' => sanitize($_POST['detail_alamat']),
    'catatan' => sanitize($_POST['catatan'])
];

$orderModel->createOrder($orderData);
$orderId = $orderModel->getDb()->lastInsertId();

// Create order details
foreach ($cartItems as $item) {
    $detailData = [
        'pesanan_id' => $orderId,
        'produk_id' => $item['produk_id'],
        'nama_produk' => $item['nama_produk'],
        'harga' => $item['harga_diskon'],
        'quantity' => $item['quantity'],
        'ukuran' => $item['ukuran'],
        'warna' => $item['warna'],
        'subtotal' => $item['subtotal']
    ];
    $orderModel->createOrderDetail($detailData);
    
    // Update stock and total sold
    $orderModel->updateStock($item['produk_id'], $item['quantity']);
    $orderModel->updateTotalSold($item['produk_id'], $item['quantity']);
}

// Clear cart
$cartModel->clearCart($userId);

setFlash('success', 'Pesanan berhasil dibuat');
redirect(SITE_URL . '/order-detail.php?id=' . $orderId);

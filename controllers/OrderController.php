<?php
/**
 * Order Controller
 * Mamz Clothing - Fashion Marketplace
 */

require_once BASE_PATH . '/models/Order.php';
require_once BASE_PATH . '/models/Cart.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/models/PaymentMethod.php';
require_once BASE_PATH . '/models/Promo.php';

class OrderController {
    private $orderModel;
    private $cartModel;
    private $paymentModel;
    private $paymentMethodModel;
    private $promoModel;
    
    public function __construct() {
        $this->orderModel = new Order();
        $this->cartModel = new Cart();
        $this->paymentModel = new Payment();
        $this->paymentMethodModel = new PaymentMethod();
        $this->promoModel = new Promo();
    }
    
    /**
     * Show checkout page
     */
    public function checkout() {
        requireLogin();
        $userId = getCurrentUserId();
        $cartItems = $this->cartModel->getCartByUserId($userId);
        $cartTotal = $this->cartModel->getCartTotal($userId);
        
        if (empty($cartItems)) {
            setFlash('error', 'Keranjang kosong');
            redirect(SITE_URL . '/products.php');
        }
        
        $user = getCurrentUser();
        $paymentMethods = $this->paymentMethodModel->getAllActivePaymentMethods();
        
        require_once BASE_PATH . '/views/user/checkout.php';
    }
    
    /**
     * Process checkout
     */
    public function process() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = getCurrentUserId();
            $cartItems = $this->cartModel->getCartByUserId($userId);
            $cartTotal = $this->cartModel->getCartTotal($userId);
            
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
                $promoResult = $this->promoModel->validatePromo($_POST['kode_promo'], $cartTotal);
                if ($promoResult['valid']) {
                    $discount = $this->promoModel->calculateDiscount($promoResult['promo'], $cartTotal);
                    $promoId = $promoResult['promo']['id'];
                    $this->promoModel->incrementPromoUsage($promoId);
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
            
            $this->orderModel->createOrder($orderData);
            $orderId = $this->orderModel->getDb()->lastInsertId();
            
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
                $this->orderModel->createOrderDetail($detailData);
                
                // Update stock and total sold
                $this->orderModel->updateStock($item['produk_id'], $item['quantity']);
                $this->orderModel->updateTotalSold($item['produk_id'], $item['quantity']);
            }
            
            // Clear cart
            $this->cartModel->clearCart($userId);
            
            setFlash('success', 'Pesanan berhasil dibuat');
            redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
        }
    }
    
    /**
     * Show order detail
     */
    public function showDetail($orderId) {
        requireLogin();
        $userId = getCurrentUserId();
        
        $order = $this->orderModel->getOrderById($orderId);
        
        if (!$order || $order['user_id'] != $userId) {
            setFlash('error', 'Pesanan tidak ditemukan');
            redirect(SITE_URL . '/my-orders.php');
        }
        
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
        $paymentMethods = $this->paymentMethodModel->getAllActivePaymentMethods();
        
        require_once BASE_PATH . '/views/user/order-detail.php';
    }
    
    /**
     * Show my orders
     */
    public function myOrders() {
        requireLogin();
        $userId = getCurrentUserId();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $orders = $this->orderModel->getOrdersByUserId($userId, $limit, $offset);
        $total = count($this->orderModel->getOrdersByUserId($userId));
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/user/my-orders.php';
    }
    
    /**
     * Upload payment proof
     */
    public function uploadPayment() {
        requireLogin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $userId = getCurrentUserId();
            
            $order = $this->orderModel->getOrderById($orderId);
            if (!$order || $order['user_id'] != $userId) {
                setFlash('error', 'Pesanan tidak ditemukan');
                redirect(SITE_URL . '/my-orders.php');
            }
            
            // Check if payment already exists
            $existingPayment = $this->paymentModel->getPaymentByOrderId($orderId);
            if ($existingPayment) {
                setFlash('error', 'Bukti pembayaran sudah diupload');
                redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
            }
            
            // Handle file upload
            if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== 0) {
                setFlash('error', 'Silakan upload bukti pembayaran');
                redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
            }
            
            $upload = uploadFile($_FILES['bukti_pembayaran'], PAYMENT_UPLOAD_PATH);
            if (!$upload['success']) {
                setFlash('error', $upload['message']);
                redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
            }
            
            $paymentData = [
                'pesanan_id' => $orderId,
                'metode_pembayaran' => sanitize($_POST['metode_pembayaran']),
                'nama_pengirim' => sanitize($_POST['nama_pengirim']),
                'nominal_transfer' => (float)$_POST['nominal_transfer'],
                'tanggal_transfer' => $_POST['tanggal_transfer'],
                'bukti_pembayaran' => $upload['filename'],
                'status' => 'menunggu_verifikasi'
            ];
            
            $result = $this->paymentModel->createPayment($paymentData);
            
            if ($result) {
                setFlash('success', 'Bukti pembayaran berhasil diupload');
                redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
            } else {
                setFlash('error', 'Gagal upload bukti pembayaran');
                redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
            }
        }
    }
    
    /**
     * Cancel order
     */
    public function cancel($orderId) {
        requireLogin();
        $userId = getCurrentUserId();
        
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order || $order['user_id'] != $userId) {
            setFlash('error', 'Pesanan tidak ditemukan');
            redirect(SITE_URL . '/my-orders.php');
        }
        
        if ($order['status_pesanan'] !== 'pending') {
            setFlash('error', 'Pesanan tidak dapat dibatalkan');
            redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
        }
        
        $this->orderModel->updateOrderStatus($orderId, 'dibatalkan');
        setFlash('success', 'Pesanan berhasil dibatalkan');
        redirect(SITE_URL . '/order-detail.php?id=' . $orderId);
    }
}

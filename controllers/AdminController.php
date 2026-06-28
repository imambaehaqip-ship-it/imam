<?php
/**
 * Admin Controller
 * Mamz Clothing - Fashion Marketplace
 */

require_once BASE_PATH . '/models/User.php';
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Order.php';
require_once BASE_PATH . '/models/Payment.php';
require_once BASE_PATH . '/models/Review.php';
require_once BASE_PATH . '/models/Promo.php';
require_once BASE_PATH . '/models/Banner.php';
require_once BASE_PATH . '/models/Contact.php';
require_once BASE_PATH . '/models/PaymentMethod.php';

class AdminController {
    private $userModel;
    private $productModel;
    private $categoryModel;
    private $orderModel;
    private $paymentModel;
    private $reviewModel;
    private $promoModel;
    private $bannerModel;
    private $contactModel;
    private $paymentMethodModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->orderModel = new Order();
        $this->paymentModel = new Payment();
        $this->reviewModel = new Review();
        $this->promoModel = new Promo();
        $this->bannerModel = new Banner();
        $this->contactModel = new Contact();
        $this->paymentMethodModel = new PaymentMethod();
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard() {
        requireAdmin();
        
        $stats = [
            'total_products' => $this->productModel->countProducts(),
            'total_categories' => $this->categoryModel->countCategories(),
            'total_users' => $this->userModel->countUsers(),
            'total_orders' => $this->orderModel->countOrders(),
            'total_revenue' => $this->orderModel->getTotalRevenue(),
            'pending_orders' => $this->orderModel->countOrdersByStatus('pending'),
            'pending_payments' => $this->paymentModel->countPaymentsByStatus('menunggu_verifikasi')
        ];
        
        $dailySales = $this->orderModel->getDailySales(30);
        $monthlySales = $this->orderModel->getMonthlySales(12);
        $topProducts = $this->orderModel->getTopSellingProducts(10);
        $recentOrders = $this->orderModel->getAllOrders(10);
        
        require_once BASE_PATH . '/views/admin/dashboard.php';
    }
    
    /**
     * Show all products
     */
    public function products() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = ADMIN_PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $products = $this->productModel->getAllProducts($limit, $offset, null);
        $total = $this->productModel->countProducts(null);
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/products.php';
    }
    
    /**
     * Show add product form
     */
    public function addProduct() {
        requireAdmin();
        $categories = $this->categoryModel->getAllCategories();
        require_once BASE_PATH . '/views/admin/product-form.php';
    }
    
    /**
     * Handle add product
     */
    public function storeProduct() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle main image upload
            $fotoUtama = '';
            if (isset($_FILES['foto_utama']) && $_FILES['foto_utama']['error'] === 0) {
                $upload = uploadFile($_FILES['foto_utama'], PRODUCT_UPLOAD_PATH);
                if ($upload['success']) {
                    $fotoUtama = $upload['filename'];
                }
            }
            
            // Handle gallery images
            $fotoGaleri = '';
            if (isset($_FILES['foto_galeri'])) {
                $galleryFiles = [];
                foreach ($_FILES['foto_galeri']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['foto_galeri']['error'][$key] === 0) {
                        $file = [
                            'name' => $_FILES['foto_galeri']['name'][$key],
                            'type' => $_FILES['foto_galeri']['type'][$key],
                            'tmp_name' => $_FILES['foto_galeri']['tmp_name'][$key],
                            'error' => $_FILES['foto_galeri']['error'][$key],
                            'size' => $_FILES['foto_galeri']['size'][$key]
                        ];
                        $upload = uploadFile($file, PRODUCT_UPLOAD_PATH);
                        if ($upload['success']) {
                            $galleryFiles[] = $upload['filename'];
                        }
                    }
                }
                $fotoGaleri = implode(',', $galleryFiles);
            }
            
            $namaProduk = sanitize($_POST['nama_produk'] ?? $_POST['nama'] ?? '');
            $slug = generateSlug($namaProduk ?: 'produk');
            $slug = $slug ? $slug . '-' . time() : 'produk-' . time();

            $data = [
                'kategori_id' => (int)($_POST['kategori_id'] ?? 0),
                'nama_produk' => $namaProduk,
                'slug' => $slug,
                'deskripsi' => sanitize($_POST['deskripsi'] ?? ''),
                'harga' => (float)($_POST['harga'] ?? 0),
                'diskon' => !empty($_POST['diskon']) ? (float)$_POST['diskon'] : 0,
                'stok' => (int)($_POST['stok'] ?? 0),
                'ukuran' => sanitize($_POST['ukuran'] ?? ''),
                'warna' => sanitize($_POST['warna'] ?? ''),
                'foto_utama' => $fotoUtama,
                'foto_galeri' => $fotoGaleri,
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
                'status' => sanitize($_POST['status'] ?? 'aktif')
            ];
            
            $result = $this->productModel->createProduct($data);
            
            if ($result) {
                setFlash('success', 'Produk berhasil ditambahkan');
                redirect(SITE_URL . '/admin/products.php');
            } else {
                setFlash('error', 'Gagal menambahkan produk');
                redirect(SITE_URL . '/admin/product-add.php');
            }
        }
    }
    
    /**
     * Show edit product form
     */
    public function editProduct($id) {
        requireAdmin();
        $product = $this->productModel->getProductById($id);
        $categories = $this->categoryModel->getAllCategories();
        require_once BASE_PATH . '/views/admin/product-form.php';
    }
    
    /**
     * Handle update product
     */
    public function updateProduct($id) {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = $this->productModel->getProductById($id);
            
            $namaProduk = sanitize($_POST['nama_produk'] ?? $_POST['nama'] ?? '');
            $slug = generateSlug($namaProduk ?: 'produk');
            $slug = $slug ? $slug . '-' . time() : 'produk-' . time();

            $data = [
                'kategori_id' => (int)($_POST['kategori_id'] ?? 0),
                'nama_produk' => $namaProduk,
                'slug' => $slug,
                'deskripsi' => sanitize($_POST['deskripsi'] ?? ''),
                'harga' => (float)($_POST['harga'] ?? 0),
                'diskon' => !empty($_POST['diskon']) ? (float)$_POST['diskon'] : 0,
                'stok' => (int)($_POST['stok'] ?? 0),
                'ukuran' => sanitize($_POST['ukuran'] ?? ''),
                'warna' => sanitize($_POST['warna'] ?? ''),
                'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
                'is_popular' => isset($_POST['is_popular']) ? 1 : 0,
                'status' => sanitize($_POST['status'] ?? 'aktif')
            ];
            
            // Handle main image upload
            if (isset($_FILES['foto_utama']) && $_FILES['foto_utama']['error'] === 0) {
                $upload = uploadFile($_FILES['foto_utama'], PRODUCT_UPLOAD_PATH);
                if ($upload['success']) {
                    // Delete old photo
                    if ($product['foto_utama']) {
                        deleteFile(PRODUCT_UPLOAD_PATH . $product['foto_utama']);
                    }
                    $data['foto_utama'] = $upload['filename'];
                }
            }
            
            // Handle gallery images
            if (isset($_FILES['foto_galeri'])) {
                $galleryFiles = [];
                foreach ($_FILES['foto_galeri']['tmp_name'] as $key => $tmp_name) {
                    if ($_FILES['foto_galeri']['error'][$key] === 0) {
                        $file = [
                            'name' => $_FILES['foto_galeri']['name'][$key],
                            'type' => $_FILES['foto_galeri']['type'][$key],
                            'tmp_name' => $_FILES['foto_galeri']['tmp_name'][$key],
                            'error' => $_FILES['foto_galeri']['error'][$key],
                            'size' => $_FILES['foto_galeri']['size'][$key]
                        ];
                        $upload = uploadFile($file, PRODUCT_UPLOAD_PATH);
                        if ($upload['success']) {
                            $galleryFiles[] = $upload['filename'];
                        }
                    }
                }
                if (!empty($galleryFiles)) {
                    $data['foto_galeri'] = implode(',', $galleryFiles);
                }
            }
            
            $result = $this->productModel->updateProduct($id, $data);
            
            if ($result) {
                setFlash('success', 'Produk berhasil diupdate');
                redirect(SITE_URL . '/admin/products.php');
            } else {
                setFlash('error', 'Gagal mengupdate produk');
                redirect(SITE_URL . '/admin/product-edit.php?id=' . $id);
            }
        }
    }
    
    /**
     * Delete product
     */
    public function deleteProduct($id) {
        requireAdmin();
        
        $product = $this->productModel->getProductById($id);
        
        // Delete photos
        if ($product['foto_utama']) {
            deleteFile(PRODUCT_UPLOAD_PATH . $product['foto_utama']);
        }
        if ($product['foto_galeri']) {
            $galeri = explode(',', $product['foto_galeri']);
            foreach ($galeri as $foto) {
                deleteFile(PRODUCT_UPLOAD_PATH . trim($foto));
            }
        }
        
        $result = $this->productModel->deleteProduct($id);
        
        if ($result) {
            setFlash('success', 'Produk berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus produk');
        }
        
        redirect(SITE_URL . '/admin/products.php');
    }
    
    /**
     * Show all categories
     */
    public function categories() {
        requireAdmin();
        $categories = $this->categoryModel->getAllCategories(null);
        require_once BASE_PATH . '/views/admin/categories.php';
    }
    
    /**
     * Handle add/update category
     */
    public function saveCategory() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_kategori' => sanitize($_POST['nama_kategori']),
                'slug' => generateSlug($_POST['nama_kategori']),
                'deskripsi' => sanitize($_POST['deskripsi']),
                'icon' => sanitize($_POST['icon']),
                'urutan' => (int)$_POST['urutan'],
                'status' => sanitize($_POST['status'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $result = $this->categoryModel->updateCategory((int)$_POST['id'], $data);
                $message = 'Kategori berhasil diupdate';
            } else {
                $result = $this->categoryModel->createCategory($data);
                $message = 'Kategori berhasil ditambahkan';
            }
            
            if ($result) {
                setFlash('success', $message);
            } else {
                setFlash('error', 'Gagal menyimpan kategori');
            }
            
            redirect(SITE_URL . '/admin/categories.php');
        }
    }
    
    /**
     * Delete category
     */
    public function deleteCategory($id) {
        requireAdmin();
        $result = $this->categoryModel->deleteCategory($id);
        
        if ($result) {
            setFlash('success', 'Kategori berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus kategori');
        }
        
        redirect(SITE_URL . '/admin/categories.php');
    }
    
    /**
     * Show all orders
     */
    public function orders() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
        
        if ($status) {
            $orders = $this->orderModel->getOrdersByStatus($status, $limit, $offset);
            $total = $this->orderModel->countOrdersByStatus($status);
        } else {
            $orders = $this->orderModel->getAllOrders($limit, $offset);
            $total = $this->orderModel->countOrders();
        }
        
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/orders.php';
    }
    
    /**
     * Show order detail
     */
    public function orderDetail($orderId) {
        requireAdmin();
        $order = $this->orderModel->getOrderById($orderId);
        $orderDetails = $this->orderModel->getOrderDetails($orderId);
        $payment = $this->paymentModel->getPaymentByOrderId($orderId);
        
        require_once BASE_PATH . '/views/admin/order-detail.php';
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int)$_POST['order_id'];
            $status = sanitize($_POST['status']);
            
            $result = $this->orderModel->updateOrderStatus($orderId, $status);
            
            if ($result) {
                setFlash('success', 'Status pesanan berhasil diupdate');
            } else {
                setFlash('error', 'Gagal mengupdate status pesanan');
            }
            
            redirect(SITE_URL . '/admin/order-detail.php?id=' . $orderId);
        }
    }
    
    /**
     * Show all payments
     */
    public function payments() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
        
        if ($status) {
            $payments = $this->paymentModel->getPaymentsByStatus($status, $limit, $offset);
            $total = $this->paymentModel->countPaymentsByStatus($status);
        } else {
            $payments = $this->paymentModel->getAllPayments($limit, $offset);
            $total = $this->paymentModel->countPayments();
        }
        
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/payments.php';
    }
    
    /**
     * Verify payment
     */
    public function verifyPayment() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentId = (int)$_POST['payment_id'];
            $status = sanitize($_POST['status']);
            $catatan = sanitize($_POST['catatan']);
            
            $payment = $this->paymentModel->getPaymentById($paymentId);
            
            $result = $this->paymentModel->updatePaymentStatus($paymentId, $status, $catatan);
            
            if ($result) {
                // Update order payment status
                if ($status === 'diterima') {
                    $this->orderModel->updatePaymentStatus($payment['pesanan_id'], 'diterima');
                } elseif ($status === 'ditolak') {
                    $this->orderModel->updatePaymentStatus($payment['pesanan_id'], 'ditolak');
                }
                
                setFlash('success', 'Pembayaran berhasil diverifikasi');
            } else {
                setFlash('error', 'Gagal memverifikasi pembayaran');
            }
            
            redirect(SITE_URL . '/admin/payments.php');
        }
    }
    
    /**
     * Show all users
     */
    public function users() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $users = $this->userModel->getAllUsers($limit, $offset);
        $total = $this->userModel->countUsers();
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/users.php';
    }
    
    /**
     * Update user status
     */
    public function updateUserStatus() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int)$_POST['user_id'];
            $status = sanitize($_POST['status']);
            
            $result = $this->userModel->updateUserStatus($userId, $status);
            
            if ($result) {
                setFlash('success', 'Status user berhasil diupdate');
            } else {
                setFlash('error', 'Gagal mengupdate status user');
            }
            
            redirect(SITE_URL . '/admin/users.php');
        }
    }
    
    /**
     * Show all reviews
     */
    public function reviews() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $reviews = $this->reviewModel->getAllReviews($limit, $offset);
        $total = $this->reviewModel->countReviews();
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/reviews.php';
    }
    
    /**
     * Update review status
     */
    public function updateReviewStatus() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reviewId = (int)$_POST['review_id'];
            $status = sanitize($_POST['status']);
            
            $review = $this->reviewModel->getReviewById($reviewId);
            $result = $this->reviewModel->updateReviewStatus($reviewId, $status);
            
            if ($result) {
                // Update product rating
                $this->productModel->updateRating($review['produk_id']);
                setFlash('success', 'Status review berhasil diupdate');
            } else {
                setFlash('error', 'Gagal mengupdate status review');
            }
            
            redirect(SITE_URL . '/admin/reviews.php');
        }
    }
    
    /**
     * Delete review
     */
    public function deleteReview($id) {
        requireAdmin();
        $review = $this->reviewModel->getReviewById($id);
        $result = $this->reviewModel->deleteReview($id);
        
        if ($result) {
            // Update product rating
            $this->productModel->updateRating($review['produk_id']);
            setFlash('success', 'Review berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus review');
        }
        
        redirect(SITE_URL . '/admin/reviews.php');
    }
    
    /**
     * Show all promos
     */
    public function promos() {
        requireAdmin();
        $promos = $this->promoModel->getAllPromos();
        require_once BASE_PATH . '/views/admin/promos.php';
    }
    
    /**
     * Handle add/update promo
     */
    public function savePromo() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'kode_promo' => strtoupper(sanitize($_POST['kode_promo'])),
                'nama_promo' => sanitize($_POST['nama_promo']),
                'deskripsi' => sanitize($_POST['deskripsi']),
                'tipe_diskon' => sanitize($_POST['tipe_diskon']),
                'nilai_diskon' => (float)$_POST['nilai_diskon'],
                'minimal_belanja' => (float)$_POST['minimal_belanja'],
                'maksimal_diskon' => (float)$_POST['maksimal_diskon'],
                'kuota' => (int)$_POST['kuota'],
                'tanggal_mulai' => $_POST['tanggal_mulai'],
                'tanggal_selesai' => $_POST['tanggal_selesai'],
                'status' => sanitize($_POST['status'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $result = $this->promoModel->updatePromo((int)$_POST['id'], $data);
                $message = 'Promo berhasil diupdate';
            } else {
                $result = $this->promoModel->createPromo($data);
                $message = 'Promo berhasil ditambahkan';
            }
            
            if ($result) {
                setFlash('success', $message);
            } else {
                setFlash('error', 'Gagal menyimpan promo');
            }
            
            redirect(SITE_URL . '/admin/promos.php');
        }
    }
    
    /**
     * Delete promo
     */
    public function deletePromo($id) {
        requireAdmin();
        $result = $this->promoModel->deletePromo($id);
        
        if ($result) {
            setFlash('success', 'Promo berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus promo');
        }
        
        redirect(SITE_URL . '/admin/promos.php');
    }
    
    /**
     * Show all banners
     */
    public function banners() {
        requireAdmin();
        $banners = $this->bannerModel->getAllBanners();
        require_once BASE_PATH . '/views/admin/banners.php';
    }
    
    /**
     * Handle add/update banner
     */
    public function saveBanner() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle image upload
            $gambar = '';
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
                $upload = uploadFile($_FILES['gambar'], UPLOAD_PATH);
                if ($upload['success']) {
                    $gambar = $upload['filename'];
                }
            }
            
            $data = [
                'judul' => sanitize($_POST['judul']),
                'deskripsi' => sanitize($_POST['deskripsi']),
                'gambar' => $gambar,
                'link' => sanitize($_POST['link']),
                'urutan' => (int)$_POST['urutan'],
                'tipe' => sanitize($_POST['tipe']),
                'status' => sanitize($_POST['status'])
            ];
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $banner = $this->bannerModel->getBannerById((int)$_POST['id']);
                if ($gambar && $banner['gambar']) {
                    deleteFile(UPLOAD_PATH . $banner['gambar']);
                }
                if (!$gambar) {
                    unset($data['gambar']);
                }
                $result = $this->bannerModel->updateBanner((int)$_POST['id'], $data);
                $message = 'Banner berhasil diupdate';
            } else {
                if (!$gambar) {
                    setFlash('error', 'Gambar wajib diupload');
                    redirect(SITE_URL . '/admin/banners.php');
                }
                $result = $this->bannerModel->createBanner($data);
                $message = 'Banner berhasil ditambahkan';
            }
            
            if ($result) {
                setFlash('success', $message);
            } else {
                setFlash('error', 'Gagal menyimpan banner');
            }
            
            redirect(SITE_URL . '/admin/banners.php');
        }
    }
    
    /**
     * Delete banner
     */
    public function deleteBanner($id) {
        requireAdmin();
        $banner = $this->bannerModel->getBannerById($id);
        
        if ($banner['gambar']) {
            deleteFile(UPLOAD_PATH . $banner['gambar']);
        }
        
        $result = $this->bannerModel->deleteBanner($id);
        
        if ($result) {
            setFlash('success', 'Banner berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus banner');
        }
        
        redirect(SITE_URL . '/admin/banners.php');
    }
    
    /**
     * Show all contacts
     */
    public function contacts() {
        requireAdmin();
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $contacts = $this->contactModel->getAllContacts($limit, $offset);
        $total = $this->contactModel->countContacts();
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/admin/contacts.php';
    }
    
    /**
     * Update contact status
     */
    public function updateContactStatus() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contactId = (int)$_POST['contact_id'];
            $status = sanitize($_POST['status']);
            
            $result = $this->contactModel->updateContactStatus($contactId, $status);
            
            if ($result) {
                setFlash('success', 'Status kontak berhasil diupdate');
            } else {
                setFlash('error', 'Gagal mengupdate status kontak');
            }
            
            redirect(SITE_URL . '/admin/contacts.php');
        }
    }
    
    /**
     * Show payment methods
     */
    public function paymentMethods() {
        requireAdmin();
        $paymentMethods = $this->paymentMethodModel->getAllPaymentMethods();
        require_once BASE_PATH . '/views/admin/payment-methods.php';
    }
    
    /**
     * Handle add/update payment method
     */
    public function savePaymentMethod() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_metode' => sanitize($_POST['nama_metode']),
                'jenis' => sanitize($_POST['jenis']),
                'nomor_rekening' => sanitize($_POST['nomor_rekening']),
                'atas_nama' => sanitize($_POST['atas_nama']),
                'status' => sanitize($_POST['status']),
                'urutan' => (int)$_POST['urutan']
            ];
            
            // Handle logo upload
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === 0) {
                $upload = uploadFile($_FILES['logo'], UPLOAD_PATH);
                if ($upload['success']) {
                    $data['logo'] = $upload['filename'];
                }
            }
            
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $paymentMethod = $this->paymentMethodModel->getPaymentMethodById((int)$_POST['id']);
                if (isset($data['logo']) && $paymentMethod['logo']) {
                    deleteFile(UPLOAD_PATH . $paymentMethod['logo']);
                }
                if (!isset($data['logo'])) {
                    unset($data['logo']);
                }
                $result = $this->paymentMethodModel->updatePaymentMethod((int)$_POST['id'], $data);
                $message = 'Metode pembayaran berhasil diupdate';
            } else {
                $result = $this->paymentMethodModel->createPaymentMethod($data);
                $message = 'Metode pembayaran berhasil ditambahkan';
            }
            
            if ($result) {
                setFlash('success', $message);
            } else {
                setFlash('error', 'Gagal menyimpan metode pembayaran');
            }
            
            redirect(SITE_URL . '/admin/payment-methods.php');
        }
    }
    
    /**
     * Delete payment method
     */
    public function deletePaymentMethod($id) {
        requireAdmin();
        $paymentMethod = $this->paymentMethodModel->getPaymentMethodById($id);
        
        if ($paymentMethod['logo']) {
            deleteFile(UPLOAD_PATH . $paymentMethod['logo']);
        }
        
        $result = $this->paymentMethodModel->deletePaymentMethod($id);
        
        if ($result) {
            setFlash('success', 'Metode pembayaran berhasil dihapus');
        } else {
            setFlash('error', 'Gagal menghapus metode pembayaran');
        }
        
        redirect(SITE_URL . '/admin/payment-methods.php');
    }
}

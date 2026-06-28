<?php
/**
 * Product Controller
 * Mamz Clothing - Fashion Marketplace
 */

require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Wishlist.php';

class ProductController {
    private $productModel;
    private $categoryModel;
    private $wishlistModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
        $this->wishlistModel = new Wishlist();
    }
    
    /**
     * Show all products
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = PRODUCTS_PER_PAGE;
        $offset = ($page - 1) * $limit;
        
        $filters = [];
        if (isset($_GET['kategori'])) {
            $category = $this->categoryModel->getCategoryBySlug($_GET['kategori']);
            if ($category) {
                $filters['kategori_id'] = $category['id'];
            }
        }
        if (isset($_GET['min_price'])) {
            $filters['min_price'] = $_GET['min_price'];
        }
        if (isset($_GET['max_price'])) {
            $filters['max_price'] = $_GET['max_price'];
        }
        if (isset($_GET['ukuran'])) {
            $filters['ukuran'] = $_GET['ukuran'];
        }
        if (isset($_GET['warna'])) {
            $filters['warna'] = $_GET['warna'];
        }
        if (isset($_GET['sort'])) {
            $filters['sort'] = $_GET['sort'];
        }
        
        if (isset($_GET['search'])) {
            $keyword = sanitize($_GET['search']);
            $products = $this->productModel->searchProducts($keyword, $limit, $offset);
            $total = $this->productModel->countSearchResults($keyword);
        } else {
            $products = $this->productModel->filterProducts($filters, $limit, $offset);
            $total = $this->productModel->countProducts();
        }
        
        $categories = $this->categoryModel->getAllCategories();
        $pagination = paginate($total, $limit, $page);
        
        require_once BASE_PATH . '/views/user/products.php';
    }
    
    /**
     * Show product detail
     */
    public function show($slug) {
        $product = $this->productModel->getProductBySlug($slug);
        
        if (!$product) {
            setFlash('error', 'Produk tidak ditemukan');
            redirect(SITE_URL . '/products.php');
        }
        
        $relatedProducts = $this->productModel->getRelatedProducts($product['id'], $product['kategori_id']);
        $inWishlist = isLoggedIn() ? $this->wishlistModel->isInWishlist(getCurrentUserId(), $product['id']) : false;
        
        require_once BASE_PATH . '/views/user/product-detail.php';
    }
    
    /**
     * Add to wishlist (AJAX)
     */
    public function addToWishlist() {
        if (!isLoggedIn()) {
            sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)$_POST['product_id'];
            $userId = getCurrentUserId();
            
            $data = [
                'user_id' => $userId,
                'produk_id' => $productId
            ];
            
            $result = $this->wishlistModel->addToWishlist($data);
            
            if ($result) {
                sendJsonResponse(['success' => true, 'message' => 'Produk ditambahkan ke wishlist']);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Produk sudah ada di wishlist']);
            }
        }
    }
    
    /**
     * Remove from wishlist (AJAX)
     */
    public function removeFromWishlist() {
        if (!isLoggedIn()) {
            sendJsonResponse(['success' => false, 'message' => 'Silakan login terlebih dahulu']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = (int)$_POST['product_id'];
            $userId = getCurrentUserId();
            
            $result = $this->wishlistModel->removeFromWishlist($userId, $productId);
            
            if ($result) {
                sendJsonResponse(['success' => true, 'message' => 'Produk dihapus dari wishlist']);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Gagal menghapus dari wishlist']);
            }
        }
    }
}

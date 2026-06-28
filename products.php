<?php
/**
 * Products Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

// Get filters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $limit;

$filters = [];
if (isset($_GET['kategori'])) {
    $category = $categoryModel->getCategoryBySlug($_GET['kategori']);
    if ($category) {
        $filters['kategori_id'] = $category['id'];
        $categoryName = $category['nama_kategori'];
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

// Get products
if (isset($_GET['search'])) {
    $keyword = sanitize($_GET['search']);
    $products = $productModel->searchProducts($keyword, $limit, $offset);
    $total = $productModel->countSearchResults($keyword);
    $pageTitle = 'Hasil Pencarian: ' . $keyword;
} else {
    $products = $productModel->filterProducts($filters, $limit, $offset);
    $total = $productModel->countProducts();
    $pageTitle = isset($categoryName) ? $categoryName : 'Semua Produk';
}

$categories = $categoryModel->getCategoriesWithProductCount();
$pagination = paginate($total, $limit, $page);

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
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $pageTitle; ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?php echo $pageTitle; ?></h1>
                <p class="text-muted"><?php echo $total; ?> Produk ditemukan</p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Filter</h5>
                        
                        <!-- Category Filter -->
                        <div class="mb-4">
                            <h6 class="mb-2">Kategori</h6>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="all-categories" checked>
                                <label class="form-check-label" for="all-categories">Semua Kategori</label>
                            </div>
                            <?php foreach ($categories as $cat): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           id="cat-<?php echo $cat['id']; ?>"
                                           value="<?php echo $cat['slug']; ?>"
                                           <?php echo isset($filters['kategori_id']) && $filters['kategori_id'] == $cat['id'] ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="cat-<?php echo $cat['id']; ?>">
                                        <?php echo $cat['nama_kategori']; ?> (<?php echo $cat['product_count']; ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Price Filter -->
                        <div class="mb-4">
                            <h6 class="mb-2">Harga</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           placeholder="Min" value="<?php echo $filters['min_price'] ?? ''; ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control form-control-sm" 
                                           placeholder="Max" value="<?php echo $filters['max_price'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Size Filter -->
                        <div class="mb-4">
                            <h6 class="mb-2">Ukuran</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <?php $sizes = ['S', 'M', 'L', 'XL', 'XXL']; ?>
                                <?php foreach ($sizes as $size): ?>
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" value="<?php echo $size; ?>">
                                        <?php echo $size; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary w-100">Terapkan Filter</button>
                    </div>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <!-- Sort Bar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Urutkan:</span>
                        <select class="form-select form-select-sm" style="width: auto;">
                            <option value="">Default</option>
                            <option value="price_asc" <?php echo ($filters['sort'] ?? '') == 'price_asc' ? 'selected' : ''; ?>>Harga Terendah</option>
                            <option value="price_desc" <?php echo ($filters['sort'] ?? '') == 'price_desc' ? 'selected' : ''; ?>>Harga Tertinggi</option>
                            <option value="name_asc" <?php echo ($filters['sort'] ?? '') == 'name_asc' ? 'selected' : ''; ?>>Nama A-Z</option>
                            <option value="name_desc" <?php echo ($filters['sort'] ?? '') == 'name_desc' ? 'selected' : ''; ?>>Nama Z-A</option>
                            <option value="popular" <?php echo ($filters['sort'] ?? '') == 'popular' ? 'selected' : ''; ?>>Terpopuler</option>
                            <option value="rating" <?php echo ($filters['sort'] ?? '') == 'rating' ? 'selected' : ''; ?>>Rating Tertinggi</option>
                        </select>
                    </div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted">Tampilan:</span>
                        <button class="btn btn-sm btn-outline active"><i class="fas fa-th"></i></button>
                        <button class="btn btn-sm btn-outline"><i class="fas fa-list"></i></button>
                    </div>
                </div>
                
                <!-- Products Grid -->
                <?php if (!empty($products)): ?>
                    <div class="row g-4">
                        <?php foreach ($products as $product): ?>
                            <div class="col-6 col-md-4 col-lg-4">
                                <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="pagination">
                        <?php echo getPaginationLinks($pagination, SITE_URL . '/products.php?' . http_build_query($_GET)); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>Tidak ada produk ditemukan</h4>
                        <p class="text-muted">Coba kata kunci atau filter lain</p>
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Reset Filter</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

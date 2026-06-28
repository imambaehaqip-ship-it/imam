<?php
/**
 * Categories Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Product.php';

$categoryModel = new Category();
$productModel = new Product();

$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

$category = null;
$products = [];
$totalProducts = 0;
$totalPages = 1;

if ($categoryId > 0) {
    $category = $categoryModel->getCategoryById($categoryId);
    
    if ($category) {
        $products = $productModel->getProductsByCategory($categoryId, $perPage, $offset);
        $totalProducts = $productModel->countProductsByCategory($categoryId);
        $totalPages = ceil($totalProducts / $perPage);
    } else {
        setFlash('error', 'Kategori tidak ditemukan');
        redirect(SITE_URL . '/index.php');
    }
} else {
    setFlash('error', 'Kategori tidak valid');
    redirect(SITE_URL . '/index.php');
}

$pageTitle = $category ? $category['nama'] : 'Kategori';
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
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/products.php">Produk</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($category['nama']); ?></li>
                    </ol>
                </nav>
                <h1 class="page-title"><?php echo htmlspecialchars($category['nama']); ?></h1>
                <p class="text-muted"><?php echo htmlspecialchars($category['deskripsi']); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Products Section -->
<section class="py-5">
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada produk di kategori ini</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Lihat Semua Produk</a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3 mb-4">
                        <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $categoryId; ?>&page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?id=<?php echo $categoryId; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?id=<?php echo $categoryId; ?>&page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

<?php
/**
 * Home Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';
require_once BASE_PATH . '/models/Banner.php';

$productModel = new Product();
$categoryModel = new Category();
$bannerModel = new Banner();

// Get data
$heroBanners = $bannerModel->getActiveBannersByType('hero');
$promoBanners = $bannerModel->getActiveBannersByType('promo');
$categories = $categoryModel->getCategoriesWithProductCount();
$featuredProducts = $productModel->getFeaturedProducts(8);
$popularProducts = $productModel->getPopularProducts(8);
$latestProducts = $productModel->getLatestProducts(8);

$pageTitle = 'Home';
require_once BASE_PATH . '/views/layouts/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-content">
                    <h1 class="hero-title">Tampil Stylish Bersama Mamz Clothing</h1>
                    <p class="hero-subtitle">Temukan koleksi pakaian premium dengan kualitas terbaik dan harga terjangkau.</p>
                    <div class="hero-buttons">
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-secondary">Belanja Sekarang</a>
                        <a href="<?php echo SITE_URL; ?>/categories.php" class="btn btn-outline">Lihat Koleksi</a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image" data-aos="fade-left" data-aos-delay="200">
                    <img src="<?php echo SITE_URL; ?>/assets/img/hero-image.png" alt="Mamz Clothing" class="img-fluid">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Kategori Produk</h2>
            <p class="text-muted">Pilih kategori favorit Anda</p>
        </div>
        
        <div class="product-grid">
            <?php foreach ($categories as $category): ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $category['urutan'] * 100; ?>">
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=<?php echo $category['slug']; ?>" class="category-card">
                        <div class="card text-center h-100">
                            <div class="card-body">
                                <div class="category-icon mb-3">
                                    <i class="fas <?php echo $category['icon'] ?: 'fa-tshirt'; ?> fa-3x text-primary"></i>
                                </div>
                                <h5 class="card-title"><?php echo $category['nama_kategori']; ?></h5>
                                <p class="card-text text-muted"><?php echo $category['product_count']; ?> Produk</p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Produk Unggulan</h2>
            <p class="text-muted">Koleksi terbaik pilihan kami</p>
        </div>
        
        <div class="product-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div data-aos="fade-up">
                    <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary">Lihat Semua Produk</a>
        </div>
    </div>
</section>

<!-- Popular Products Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Produk Terlaris</h2>
            <p class="text-muted">Produk paling diminati pelanggan</p>
        </div>
        
        <div class="product-grid">
            <?php foreach ($popularProducts as $product): ?>
                <div data-aos="fade-up">
                    <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="section-title">Produk Terbaru</h2>
            <p class="text-muted">Koleksi terbaru dari kami</p>
        </div>
        
        <div class="product-grid">
            <?php foreach ($latestProducts as $product): ?>
                <div data-aos="fade-up">
                    <?php include BASE_PATH . '/views/components/product-card.php'; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-5">
    <div class="container">
        <div class="card bg-primary text-white">
            <div class="card-body text-center py-5">
                <h2 class="card-title mb-3">Berlangganan Newsletter</h2>
                <p class="card-text mb-4">Dapatkan info promo dan produk terbaru langsung ke email Anda</p>
                <form class="row g-3 justify-content-center" style="max-width: 500px; margin: 0 auto;">
                    <div class="col-md-8">
                        <input type="email" class="form-control" placeholder="Masukkan email Anda" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-secondary w-100">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

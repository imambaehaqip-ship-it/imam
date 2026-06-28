<?php
/**
 * Product Detail Page
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

$slug = $_GET['slug'] ?? '';
$product = $productModel->getProductBySlug($slug);

if (!$product) {
    setFlash('error', 'Produk tidak ditemukan');
    redirect(SITE_URL . '/products.php');
}

$relatedProducts = $productModel->getRelatedProducts($product['id'], $product['kategori_id']);
$inWishlist = isLoggedIn() ? isInWishlist($product['id']) : false;
$discountedPrice = calculateDiscountPrice($product['harga'], $product['diskon']);

$pageTitle = $product['nama_produk'];
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
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/products.php?kategori=<?php echo $product['kategori_slug']; ?>"><?php echo $product['nama_kategori']; ?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?php echo $product['nama_produk']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Product Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body p-0">
                        <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo $product['foto_utara'] ?: 'placeholder.jpg'; ?>" 
                             alt="<?php echo $product['nama_produk']; ?>" 
                             class="img-fluid w-100" id="main-product-image">
                    </div>
                </div>
                
                <?php if ($product['foto_galeri']): ?>
                    <?php $galeri = explode(',', $product['foto_galeri']); ?>
                    <div class="row g-2 mt-2">
                        <?php foreach ($galeri as $foto): ?>
                            <div class="col-3">
                                <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo trim($foto); ?>" 
                                     alt="Gallery" 
                                     class="img-fluid rounded cursor-pointer gallery-thumb"
                                     onclick="changeMainImage(this.src)">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Product Info -->
            <div class="col-lg-6">
                <h1 class="h3 mb-3"><?php echo $product['nama_produk']; ?></h1>
                
                <div class="mb-3">
                    <?php echo getRatingStars($product['rating']); ?>
                    <span class="text-muted ms-2"><?php echo $product['total_review']; ?> Review</span>
                </div>
                
                <div class="mb-3">
                    <span class="h3 text-primary"><?php echo formatPrice($discountedPrice); ?></span>
                    <?php if ($product['diskon'] > 0): ?>
                        <span class="text-muted text-decoration-line-through ms-2"><?php echo formatPrice($product['harga']); ?></span>
                        <span class="badge bg-danger ms-2">-<?php echo $product['diskon']; ?>%</span>
                    <?php endif; ?>
                </div>
                
                <p class="text-muted mb-4"><?php echo $product['deskripsi']; ?></p>
                
                <div class="mb-4">
                    <span class="badge bg-light text-dark me-2">Stok: <?php echo $product['stok']; ?></span>
                    <span class="badge bg-light text-dark">Kategori: <?php echo $product['nama_kategori']; ?></span>
                </div>
                
                <?php if ($product['ukuran']): ?>
                    <div class="mb-4">
                        <h6>Ukuran:</h6>
                        <div class="d-flex gap-2">
                            <?php $sizes = explode(',', $product['ukuran']); ?>
                            <?php foreach ($sizes as $size): ?>
                                <label class="form-check-label">
                                    <input type="radio" name="ukuran" value="<?php echo trim($size); ?>" class="form-check-input me-1">
                                    <?php echo trim($size); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($product['warna']): ?>
                    <div class="mb-4">
                        <h6>Warna:</h6>
                        <div class="d-flex gap-2">
                            <?php $colors = explode(',', $product['warna']); ?>
                            <?php foreach ($colors as $color): ?>
                                <label class="form-check-label">
                                    <input type="radio" name="warna" value="<?php echo trim($color); ?>" class="form-check-input me-1">
                                    <?php echo trim($color); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div class="mb-4">
                    <div class="input-group" style="max-width: 150px;">
                        <button class="btn btn-outline-secondary" onclick="decreaseQty()">-</button>
                        <input type="number" class="form-control text-center" id="product-qty" value="1" min="1" max="<?php echo $product['stok']; ?>">
                        <button class="btn btn-outline-secondary" onclick="increaseQty()">+</button>
                    </div>
                </div>
                
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-primary flex-grow-1 add-to-cart" 
                            data-product-id="<?php echo $product['id']; ?>" 
                            data-quantity="1"
                            <?php echo $product['stok'] <= 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-shopping-cart me-2"></i>
                        <?php echo $product['stok'] > 0 ? 'Tambah ke Keranjang' : 'Stok Habis'; ?>
                    </button>
                    
                    <button class="btn btn-outline add-to-wishlist <?php echo $inWishlist ? 'active' : ''; ?>" 
                            data-product-id="<?php echo $product['id']; ?>">
                        <i class="<?php echo $inWishlist ? 'fas' : 'far'; ?> fa-heart"></i>
                    </button>
                </div>
                
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-shield-alt text-primary me-2"></i>
                            <span>Garansi Produk Original</span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-truck text-primary me-2"></i>
                            <span>Pengiriman Cepat & Aman</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-undo text-primary me-2"></i>
                            <span>7 Hari Pengembalian</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Product Reviews Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="mb-4">Ulasan Pelanggan</h3>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="display-4 me-3"><?php echo number_format($product['rating'], 1); ?></div>
                    <div>
                        <div class="mb-1"><?php echo getRatingStars($product['rating']); ?></div>
                        <small class="text-muted">Berdasarkan <?php echo $product['total_review']; ?> ulasan</small>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <button class="btn btn-primary mb-4" data-bs-toggle="modal" data-bs-target="#reviewModal">
                <i class="fas fa-star me-2"></i>Tulis Ulasan
            </button>
        <?php else: ?>
            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary mb-4">
                <i class="fas fa-star me-2"></i>Login untuk Menulis Ulasan
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products Section -->
<section class="py-5">
    <div class="container">
        <h3 class="mb-4">Produk Terkait</h3>
        
        <div class="row g-4">
            <?php foreach ($relatedProducts as $related): ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <?php 
                    $product = $related;
                    include BASE_PATH . '/views/components/product-card.php';
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tulis Ulasan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Rating</label>
                        <div class="star-rating">
                            <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                            <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                            <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                            <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                            <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ulasan</label>
                        <textarea class="form-control" rows="4" placeholder="Tulis ulasan Anda..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary">Kirim Ulasan</button>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('main-product-image').src = src;
}

function increaseQty() {
    const input = document.getElementById('product-qty');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('product-qty');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}
</script>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

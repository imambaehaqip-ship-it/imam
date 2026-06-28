<?php
$discountedPrice = calculateDiscountPrice($product['harga'], $product['diskon']);
$inWishlist = isLoggedIn() ? isInWishlist($product['id']) : false;
?>

<div class="product-card" data-aos="fade-up">
    <div class="product-image">
        <?php if ($product['diskon'] > 0): ?>
            <span class="product-badge badge-sale">-<?php echo $product['diskon']; ?>%</span>
        <?php endif; ?>
        
        <?php if ($product['is_featured']): ?>
            <span class="product-badge badge-new">New</span>
        <?php endif; ?>
        
        <a href="<?php echo SITE_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>">
            <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo $product['foto_utama'] ?: 'placeholder.jpg'; ?>" alt="<?php echo $product['nama_produk']; ?>">
        </a>
        
        <div class="product-actions">
            <button class="action-btn add-to-wishlist <?php echo $inWishlist ? 'active' : ''; ?>" data-product-id="<?php echo $product['id']; ?>">
                <i class="<?php echo $inWishlist ? 'fas' : 'far'; ?> fa-heart"></i>
            </button>
            <a href="<?php echo SITE_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>" class="action-btn">
                <i class="fas fa-eye"></i>
            </a>
        </div>
    </div>
    
    <div class="product-info">
        <p class="product-category"><?php echo $product['nama_kategori']; ?></p>
        <a href="<?php echo SITE_URL; ?>/product.php?slug=<?php echo $product['slug']; ?>" class="product-name">
            <?php echo $product['nama_produk']; ?>
        </a>
        <div class="product-price">
            <span class="price-current"><?php echo formatPrice($discountedPrice); ?></span>
            <?php if ($product['diskon'] > 0): ?>
                <span class="price-old"><?php echo formatPrice($product['harga']); ?></span>
            <?php endif; ?>
        </div>
        <div class="product-rating">
            <?php echo getRatingStars($product['rating']); ?>
            <span class="text-muted ms-1">(<?php echo $product['total_review']; ?>)</span>
        </div>
        <button class="btn btn-primary btn-sm w-100 mt-2 add-to-cart" 
                data-product-id="<?php echo $product['id']; ?>" 
                data-quantity="1"
                <?php echo $product['stok'] <= 0 ? 'disabled' : ''; ?>>
            <?php if ($product['stok'] > 0): ?>
                <i class="fas fa-shopping-cart me-1"></i> Tambah ke Keranjang
            <?php else: ?>
                <i class="fas fa-times me-1"></i> Stok Habis
            <?php endif; ?>
        </button>
    </div>
</div>

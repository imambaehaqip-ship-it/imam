<?php
/**
 * About Us Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';

$pageTitle = 'Tentang Kami';
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
                        <li class="breadcrumb-item active" aria-current="page">Tentang Kami</li>
                    </ol>
                </nav>
                <h1 class="page-title">Tentang Kami</h1>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center mb-5">
            <div class="col-lg-6 mb-4" data-aos="fade-right">
                <img src="<?php echo SITE_URL; ?>/assets/img/about.jpg" alt="About Us" class="img-fluid rounded">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h2 class="mb-4">Simple Style, Premium Quality</h2>
                <p class="text-muted mb-4">
                    Mamz Clothing adalah marketplace fashion yang menyediakan berbagai pilihan pakaian pria dan wanita dengan desain modern, kualitas terbaik, dan harga terjangkau.
                </p>
                <p class="text-muted mb-4">
                    Kami berkomitmen untuk memberikan pengalaman belanja yang terbaik bagi pelanggan kami dengan menyediakan produk-produk berkualitas tinggi dari berbagai brand ternama.
                </p>
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Produk Original</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Kualitas Premium</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Harga Terjangkau</span>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Pengiriman Cepat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mission & Vision -->
        <div class="row mb-5">
            <div class="col-lg-6 mb-4" data-aos="fade-up">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-bullseye fa-3x text-primary"></i>
                        </div>
                        <h3 class="text-center mb-3">Visi Kami</h3>
                        <p class="text-muted text-center">
                            Menjadi marketplace fashion terdepan di Indonesia yang menyediakan produk berkualitas dengan harga terjangkau bagi semua kalangan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-rocket fa-3x text-primary"></i>
                        </div>
                        <h3 class="text-center mb-3">Misi Kami</h3>
                        <p class="text-muted text-center">
                            Memberikan pelayanan terbaik dengan menyediakan koleksi fashion terlengkap, berkualitas, dan terjangkau serta memberikan pengalaman belanja yang menyenangkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Team -->
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="mb-4">Tim Kami</h2>
            <p class="text-muted">Orang-orang di balik Mamz Clothing</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4" data-aos="fade-up">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="<?php echo SITE_URL; ?>/assets/img/team1.jpg" alt="Team Member" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h5>Imam Baehaqi</h5>
                        <p class="text-muted">Founder & CEO</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="<?php echo SITE_URL; ?>/assets/img/team2.jpg" alt="Team Member" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h5>Sarah Wijaya</h5>
                        <p class="text-muted">Creative Director</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center">
                    <div class="card-body">
                        <img src="<?php echo SITE_URL; ?>/assets/img/team3.jpg" alt="Team Member" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                        <h5>Budi Santoso</h5>
                        <p class="text-muted">Operations Manager</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

<?php
/**
 * Contact Us Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__);
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Contact.php';

$contactModel = new Contact();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nama' => sanitize($_POST['nama']),
        'email' => sanitize($_POST['email']),
        'subjek' => sanitize($_POST['subjek']),
        'pesan' => sanitize($_POST['pesan'])
    ];
    
    if (empty($data['nama']) || empty($data['email']) || empty($data['subjek']) || empty($data['pesan'])) {
        setFlash('error', 'Semua field wajib diisi');
        redirect(SITE_URL . '/contact.php');
    }
    
    if (!validateEmail($data['email'])) {
        setFlash('error', 'Email tidak valid');
        redirect(SITE_URL . '/contact.php');
    }
    
    $result = $contactModel->createContact($data);
    
    if ($result) {
        setFlash('success', 'Pesan Anda telah terkirim. Kami akan segera menghubungi Anda.');
        redirect(SITE_URL . '/contact.php');
    } else {
        setFlash('error', 'Gagal mengirim pesan');
        redirect(SITE_URL . '/contact.php');
    }
}

$pageTitle = 'Hubungi Kami';
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
                        <li class="breadcrumb-item active" aria-current="page">Hubungi Kami</li>
                    </ol>
                </nav>
                <h1 class="page-title">Hubungi Kami</h1>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Info -->
            <div class="col-lg-4 mb-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Informasi Kontak</h5>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Alamat</h6>
                                    <p class="text-muted mb-0">Kota Mataram, Nusa Tenggara Barat</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fab fa-whatsapp text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">WhatsApp</h6>
                                    <p class="text-muted mb-0"><?php echo WHATSAPP_NUMBER; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Email</h6>
                                    <p class="text-muted mb-0">info@mamzclothing.com</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Map Placeholder -->
                <div class="card">
                    <div class="card-body p-0">
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                            <p class="text-muted">Google Maps Embed</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Kirim Pesan</h5>
                        
                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="subjek" class="form-label">Subjek</label>
                                <input type="text" class="form-control" id="subjek" name="subjek" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="pesan" class="form-label">Pesan</label>
                                <textarea class="form-control" id="pesan" name="pesan" rows="5" required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once BASE_PATH . '/views/layouts/footer.php'; ?>

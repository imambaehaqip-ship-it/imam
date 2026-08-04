<?php
/**
 * Admin Product Form Page
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

// Check if user is admin
requireAdmin();

require_once BASE_PATH . '/models/Product.php';
require_once BASE_PATH . '/models/Category.php';

$productModel = new Product();
$categoryModel = new Category();

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;
$isEdit = false;

if ($productId > 0) {
    $product = $productModel->getProductById($productId);
    if (!$product) {
        setFlash('error', 'Produk tidak ditemukan');
        redirect(SITE_URL . '/admin/products.php');
    }
    $isEdit = true;
}

$categories = $categoryModel->getAllCategories();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'kategori_id' => (int)$_POST['kategori_id'],
        'nama_produk' => sanitize($_POST['nama']),
        'deskripsi' => sanitize($_POST['deskripsi']),
        'harga' => (float)$_POST['harga'],
        'diskon' => !empty($_POST['harga_diskon']) ? (float)$_POST['harga_diskon'] : 0,
        'stok' => (int)$_POST['stok'],
        'ukuran' => sanitize($_POST['ukuran']),
        'warna' => sanitize($_POST['warna']),
        'status' => sanitize($_POST['status'])
    ];

    // basic validation
    if (empty($data['nama_produk']) || $data['harga'] <= 0 || $data['stok'] < 0) {
        setFlash('error', 'Nama produk, harga, dan stok wajib diisi dengan nilai yang benar');
        redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
    }

    // generate unique slug
    $baseSlug = generateSlug($data['nama_produk']);
    $slug = $baseSlug;
    $counter = 1;

    while ($productModel->slugExists($slug, $isEdit ? $productId : null)) {
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    $data['slug'] = $slug;
    
    // Handle image upload (foto_utama)
    $foto_utama = $isEdit && !empty($product['foto_utama']) ? $product['foto_utama'] : null;
    if (isset($_FILES['gambar']) && !empty($_FILES['gambar']['name'])) {
        $file = $_FILES['gambar'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrorMessage = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Ukuran file terlalu besar',
                UPLOAD_ERR_PARTIAL => 'Upload file terputus',
                UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih',
                default => 'Gagal mengupload gambar'
            };
            setFlash('error', $uploadErrorMessage);
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            setFlash('error', 'File upload tidak valid');
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $maxSize = MAX_FILE_SIZE;

        if (!in_array($ext, ALLOWED_IMAGE_TYPES)) {
            setFlash('error', 'Format file tidak valid');
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }

        if ($file['size'] > $maxSize) {
            setFlash('error', 'Ukuran file terlalu besar');
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }

        if (!is_dir(PRODUCT_UPLOAD_PATH)) {
            if (!mkdir(PRODUCT_UPLOAD_PATH, 0777, true) && !is_dir(PRODUCT_UPLOAD_PATH)) {
                setFlash('error', 'Folder upload tidak dapat dibuat');
                redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
            }
        }

        if (!is_writable(PRODUCT_UPLOAD_PATH)) {
            setFlash('error', 'Folder upload tidak dapat ditulis');
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }

        $filename = 'product_' . time() . '_' . uniqid() . '.' . $ext;
        $uploadPath = PRODUCT_UPLOAD_PATH . $filename;

        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            // delete old file if editing
            if ($isEdit && $foto_utama && file_exists(PRODUCT_UPLOAD_PATH . $foto_utama)) {
                @unlink(PRODUCT_UPLOAD_PATH . $foto_utama);
            }
            $foto_utama = $filename;
        } else {
            setFlash('error', 'Gagal menyimpan gambar ke server');
            redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
        }
    }

    $data['foto_utama'] = $foto_utama;
    
    if ($isEdit) {
        $result = $productModel->updateProduct($productId, $data);
        if ($result) {
            setFlash('success', 'Produk berhasil diperbarui');
            redirect(SITE_URL . '/admin/products.php');
        }
    } else {
        $result = $productModel->createProduct($data);
        if ($result) {
            setFlash('success', 'Produk berhasil ditambahkan');
            redirect(SITE_URL . '/admin/products.php');
        }
    }
    
    setFlash('error', 'Gagal menyimpan produk');
    redirect(SITE_URL . '/admin/product-form.php' . ($isEdit ? '?id=' . $productId : ''));
}

$pageTitle = $isEdit ? 'Edit Produk' : 'Tambah Produk';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><?php echo $pageTitle; ?></h2>
    <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Produk *</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $isEdit ? htmlspecialchars($product['nama_produk']) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="kategori_id" class="form-label">Kategori *</label>
                        <select class="form-select" id="kategori_id" name="kategori_id" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo $isEdit && $product['kategori_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['nama']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="5"><?php echo $isEdit ? htmlspecialchars($product['deskripsi']) : ''; ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="harga" class="form-label">Harga *</label>
                            <input type="number" class="form-control" id="harga" name="harga" value="<?php echo $isEdit ? $product['harga'] : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="harga_diskon" class="form-label">Harga Diskon</label>
                            <input type="number" class="form-control" id="harga_diskon" name="harga_diskon" value="<?php echo $isEdit && isset($product['diskon']) ? $product['diskon'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stok" class="form-label">Stok *</label>
                            <input type="number" class="form-control" id="stok" name="stok" value="<?php echo $isEdit ? $product['stok'] : ''; ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="harga_diskon" class="form-label">Diskon (%)</label>
                            <input type="number" step="0.01" class="form-control" id="harga_diskon" name="harga_diskon" value="<?php echo $isEdit && isset($product['diskon']) ? $product['diskon'] : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ukuran" class="form-label">Ukuran</label>
                            <input type="text" class="form-control" id="ukuran" name="ukuran" value="<?php echo $isEdit ? htmlspecialchars($product['ukuran']) : ''; ?>" placeholder="S, M, L, XL">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="warna" class="form-label">Warna</label>
                            <input type="text" class="form-control" id="warna" name="warna" value="<?php echo $isEdit ? htmlspecialchars($product['warna']) : ''; ?>" placeholder="Hitam, Putih, Merah">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="aktif" <?php echo $isEdit && $product['status'] == 'aktif' ? 'selected' : (!$isEdit ? 'selected' : ''); ?>>Aktif</option>
                            <option value="nonaktif" <?php echo $isEdit && $product['status'] == 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="gambar" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="gambar" name="gambar" accept="image/jpeg,image/jpg,image/png,image/webp">
                        <div class="form-text">Format: JPG, PNG, WebP. Maksimal: <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?>MB.</div>
                        <div class="form-text text-muted">Pilih gambar baru untuk mengganti gambar lama. Jika tidak dipilih, gambar lama akan tetap dipertahankan.</div>
                    </div>
                    
                    <?php if ($isEdit && !empty($product['foto_utama'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Gambar Saat Ini</label>
                            <img src="<?php echo SITE_URL; ?>/uploads/products/<?php echo htmlspecialchars($product['foto_utama']); ?>" alt="Gambar Produk" class="img-fluid rounded">
                        </div>
                    <?php endif; ?>
                    
                    <div class="alert alert-info">
                        <p class="mb-0 small"><i class="fas fa-info-circle me-2"></i>Upload gambar baru untuk mengganti gambar lama saat mengedit produk.</p>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="<?php echo SITE_URL; ?>/admin/products.php" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i><?php echo $isEdit ? 'Simpan Perubahan' : 'Tambah Produk'; ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

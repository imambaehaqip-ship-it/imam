<?php
/**
 * Admin Banners Page
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

require_once BASE_PATH . '/models/Banner.php';

$bannerModel = new Banner();

$banners = $bannerModel->getAllBanners();

$pageTitle = 'Kelola Banner';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Kelola Banner</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bannerModal">
        <i class="fas fa-plus me-2"></i>Tambah Banner
    </button>
</div>

<!-- Banners Grid -->
<div class="row">
    <?php if (empty($banners)): ?>
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada banner</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($banners as $banner): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card">
                    <img src="<?php echo SITE_URL; ?>/uploads/banners/<?php echo htmlspecialchars($banner['gambar']); ?>" alt="<?php echo htmlspecialchars($banner['judul']); ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title"><?php echo htmlspecialchars($banner['judul']); ?></h6>
                        <p class="card-text small text-muted"><?php echo htmlspecialchars(substr($banner['deskripsi'], 0, 50)) . '...'; ?></p>
                        <p class="card-text small">
                            <strong>Urutan:</strong> <?php echo $banner['urutan']; ?><br>
                            <strong>Status:</strong> 
                            <span class="badge bg-<?php echo $banner['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($banner['status']); ?>
                            </span>
                        </p>
                        <div class="btn-group w-100">
                            <button onclick="editBanner(<?php echo $banner['id']; ?>, '<?php echo htmlspecialchars($banner['judul']); ?>', '<?php echo htmlspecialchars($banner['deskripsi']); ?>', '<?php echo htmlspecialchars($banner['link']); ?>', <?php echo $banner['urutan']; ?>, '<?php echo $banner['status']; ?>')" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="confirmDelete(<?php echo $banner['id']; ?>, '<?php echo htmlspecialchars($banner['judul']); ?>')" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Banner Modal -->
<div class="modal fade" id="bannerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bannerModalLabel">Tambah Banner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bannerForm" enctype="multipart/form-data">
                    <input type="hidden" id="bannerId" name="id">
                    <div class="mb-3">
                        <label for="bannerJudul" class="form-label">Judul *</label>
                        <input type="text" class="form-control" id="bannerJudul" name="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="bannerDeskripsi" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="bannerDeskripsi" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="bannerLink" class="form-label">Link</label>
                        <input type="text" class="form-control" id="bannerLink" name="link" placeholder="https://...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bannerUrutan" class="form-label">Urutan</label>
                            <input type="number" class="form-control" id="bannerUrutan" name="urutan" value="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bannerStatus" class="form-label">Status</label>
                            <select class="form-select" id="bannerStatus" name="status">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bannerGambar" class="form-label">Gambar *</label>
                        <input type="file" class="form-control" id="bannerGambar" name="gambar" accept="image/jpeg,image/jpg,image/png,image/webp" required>
                        <div class="form-text">Format: JPG, PNG, WebP. Maksimal: <?php echo (MAX_FILE_SIZE / 1024 / 1024); ?>MB.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveBanner()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
const bannerModal = new bootstrap.Modal(document.getElementById('bannerModal'));

function editBanner(id, judul, deskripsi, link, urutan, status) {
    document.getElementById('bannerId').value = id;
    document.getElementById('bannerJudul').value = judul;
    document.getElementById('bannerDeskripsi').value = deskripsi;
    document.getElementById('bannerLink').value = link;
    document.getElementById('bannerUrutan').value = urutan;
    document.getElementById('bannerStatus').value = status;
    document.getElementById('bannerGambar').removeAttribute('required');
    document.getElementById('bannerModalLabel').textContent = 'Edit Banner';
    bannerModal.show();
}

function saveBanner() {
    const form = document.getElementById('bannerForm');
    const formData = new FormData(form);
    
    $.ajax({
        url: '<?php echo SITE_URL; ?>/ajax/admin/save-banner.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                Swal.fire('Berhasil', response.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Gagal', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
        }
    });
}

function confirmDelete(id, title) {
    Swal.fire({
        title: 'Hapus Banner?',
        text: 'Apakah Anda yakin ingin menghapus banner "' + title + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/ajax/admin/delete-banner.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Banner berhasil dihapus.', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Gagal', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
                }
            });
        }
    });
}

// Reset form when modal is closed
document.getElementById('bannerModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('bannerForm').reset();
    document.getElementById('bannerId').value = '';
    document.getElementById('bannerGambar').setAttribute('required', 'required');
    document.getElementById('bannerModalLabel').textContent = 'Tambah Banner';
});
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

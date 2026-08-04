<?php
/**
 * Admin Promos Page
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

require_once BASE_PATH . '/models/Promo.php';

$promoModel = new Promo();

$promos = $promoModel->getAllPromos();

$pageTitle = 'Kelola Promo';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Kelola Promo</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#promoModal">
        <i class="fas fa-plus me-2"></i>Tambah Promo
    </button>
</div>

<!-- Promos Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($promos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-percent fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada promo</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Diskon</th>
                            <th>Tipe</th>
                            <th>Berlaku Hingga</th>
                            <th>Min. Belanja</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($promos as $promo): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($promo['kode']); ?></strong>
                                </td>
                                <td>
                                    <?php if ($promo['tipe_diskon'] == 'persen'): ?>
                                        <?php echo $promo['nilai_diskon']; ?>%
                                    <?php else: ?>
                                        <?php echo formatRupiah($promo['nilai_diskon']); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo ucfirst($promo['tipe_diskon']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($promo['berlaku_hingga'])); ?></td>
                                <td><?php echo formatRupiah($promo['min_belanja']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $promo['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($promo['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="editPromo(<?php echo $promo['id']; ?>, '<?php echo htmlspecialchars($promo['kode']); ?>', <?php echo $promo['nilai_diskon']; ?>, '<?php echo $promo['tipe_diskon']; ?>', '<?php echo $promo['berlaku_hingga']; ?>', <?php echo $promo['min_belanja']; ?>, '<?php echo $promo['status']; ?>')" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $promo['id']; ?>, '<?php echo htmlspecialchars($promo['kode']); ?>')" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Promo Modal -->
<div class="modal fade" id="promoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="promoModalLabel">Tambah Promo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="promoForm">
                    <input type="hidden" id="promoId" name="id">
                    <div class="mb-3">
                        <label for="promoKode" class="form-label">Kode Promo *</label>
                        <input type="text" class="form-control" id="promoKode" name="kode" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="promoNilai" class="form-label">Nilai Diskon *</label>
                            <input type="number" class="form-control" id="promoNilai" name="nilai_diskon" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="promoTipe" class="form-label">Tipe Diskon</label>
                            <select class="form-select" id="promoTipe" name="tipe_diskon">
                                <option value="persen">Persen (%)</option>
                                <option value="nominal">Nominal (Rp)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="promoBerlaku" class="form-label">Berlaku Hingga *</label>
                            <input type="date" class="form-control" id="promoBerlaku" name="berlaku_hingga" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="promoMin" class="form-label">Min. Belanja</label>
                            <input type="number" class="form-control" id="promoMin" name="min_belanja" value="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="promoStatus" class="form-label">Status</label>
                        <select class="form-select" id="promoStatus" name="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="savePromo()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
const promoModal = new bootstrap.Modal(document.getElementById('promoModal'));

function editPromo(id, kode, nilai, tipe, berlaku, min, status) {
    document.getElementById('promoId').value = id;
    document.getElementById('promoKode').value = kode;
    document.getElementById('promoNilai').value = nilai;
    document.getElementById('promoTipe').value = tipe;
    document.getElementById('promoBerlaku').value = berlaku;
    document.getElementById('promoMin').value = min;
    document.getElementById('promoStatus').value = status;
    document.getElementById('promoModalLabel').textContent = 'Edit Promo';
    promoModal.show();
}

function savePromo() {
    const form = document.getElementById('promoForm');
    const formData = new FormData(form);
    
    $.ajax({
        url: '<?php echo SITE_URL; ?>/ajax/admin/save-promo.php',
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

function confirmDelete(id, kode) {
    Swal.fire({
        title: 'Hapus Promo?',
        text: 'Apakah Anda yakin ingin menghapus promo "' + kode + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/ajax/admin/delete-promo.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Promo berhasil dihapus.', 'success').then(() => {
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
document.getElementById('promoModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('promoForm').reset();
    document.getElementById('promoId').value = '';
    document.getElementById('promoModalLabel').textContent = 'Tambah Promo';
});
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

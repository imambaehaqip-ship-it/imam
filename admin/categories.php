<?php
/**
 * Admin Categories Page
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

require_once BASE_PATH . '/models/Category.php';

$categoryModel = new Category();

$categories = $categoryModel->getAllCategories();

$pageTitle = 'Kelola Kategori';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Kelola Kategori</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="fas fa-plus me-2"></i>Tambah Kategori
    </button>
</div>

<!-- Categories Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-tags fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada kategori</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Jumlah Produk</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($category['nama']); ?></h6>
                                </td>
                                <td><?php echo htmlspecialchars(substr($category['deskripsi'], 0, 50)) . '...'; ?></td>
                                <td><?php echo $category['jumlah_produk']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $category['status'] == 'aktif' ? 'success' : 'danger'; ?>">
                                        <?php echo ucfirst($category['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="editCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['nama']); ?>', '<?php echo htmlspecialchars($category['deskripsi']); ?>', '<?php echo $category['status']; ?>')" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['nama']); ?>')" class="btn btn-sm btn-outline-danger">
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

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Tambah Kategori</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" name="id">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Nama Kategori *</label>
                        <input type="text" class="form-control" id="categoryName" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="categoryDescription" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="categoryStatus" class="form-label">Status</label>
                        <select class="form-select" id="categoryStatus" name="status">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="saveCategory()" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));

function editCategory(id, nama, deskripsi, status) {
    document.getElementById('categoryId').value = id;
    document.getElementById('categoryName').value = nama;
    document.getElementById('categoryDescription').value = deskripsi;
    document.getElementById('categoryStatus').value = status;
    document.getElementById('categoryModalLabel').textContent = 'Edit Kategori';
    categoryModal.show();
}

function saveCategory() {
    const form = document.getElementById('categoryForm');
    const formData = new FormData(form);
    
    $.ajax({
        url: '<?php echo SITE_URL; ?>/ajax/admin/save-category.php',
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

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Kategori?',
        text: 'Apakah Anda yakin ingin menghapus kategori "' + name + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/ajax/admin/delete-category.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Kategori berhasil dihapus.', 'success').then(() => {
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
document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('categoryModalLabel').textContent = 'Tambah Kategori';
});
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

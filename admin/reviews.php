<?php
/**
 * Admin Reviews Page
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

require_once BASE_PATH . '/models/Review.php';

$reviewModel = new Review();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$statusFilter = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$reviews = $reviewModel->getAllReviews($perPage, $offset, $statusFilter);
$totalReviews = $reviewModel->countReviews($statusFilter);
$totalPages = ceil($totalReviews / $perPage);

$pageTitle = 'Kelola Ulasan';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Kelola Ulasan</h2>
    <div class="d-flex gap-2">
        <select class="form-select" onchange="location.href='<?php echo SITE_URL; ?>/admin/reviews.php?status='+this.value" style="width: auto;">
            <option value="">Semua Status</option>
            <option value="pending" <?php echo $statusFilter == 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?php echo $statusFilter == 'approved' ? 'selected' : ''; ?>>Disetujui</option>
            <option value="rejected" <?php echo $statusFilter == 'rejected' ? 'selected' : ''; ?>>Ditolak</option>
        </select>
    </div>
</div>

<!-- Reviews Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($reviews)): ?>
            <div class="text-center py-5">
                <i class="fas fa-star fa-4x text-muted mb-3"></i>
                <p class="text-muted">Tidak ada ulasan ditemukan</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Pengguna</th>
                            <th>Rating</th>
                            <th>Ulasan</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['nama_produk']); ?></td>
                                <td><?php echo htmlspecialchars($review['nama_user']); ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?php echo $i <= $review['rating'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                    <?php endfor; ?>
                                    (<?php echo $review['rating']; ?>/5)
                                </td>
                                <td><?php echo htmlspecialchars(substr($review['ulasan'], 0, 50)) . '...'; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $review['status'] == 'approved' ? 'success' : ($review['status'] == 'rejected' ? 'danger' : 'warning'); ?>">
                                        <?php echo ucfirst($review['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($review['tanggal'])); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <button onclick="updateStatus(<?php echo $review['id']; ?>, 'approved')" class="btn btn-sm btn-outline-success" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="updateStatus(<?php echo $review['id']; ?>, 'rejected')" class="btn btn-sm btn-outline-danger" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $review['id']; ?>)" class="btn btn-sm btn-outline-secondary" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $statusFilter; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $statusFilter; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $statusFilter; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function updateStatus(id, status) {
    $.ajax({
        url: '<?php echo SITE_URL; ?>/ajax/admin/update-review.php',
        type: 'POST',
        data: { id: id, status: status },
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

function confirmDelete(id) {
    Swal.fire({
        title: 'Hapus Ulasan?',
        text: 'Apakah Anda yakin ingin menghapus ulasan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/ajax/admin/delete-review.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Ulasan berhasil dihapus.', 'success').then(() => {
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
</script>

<?php require_once BASE_PATH . '/views/admin/layouts/footer.php'; ?>

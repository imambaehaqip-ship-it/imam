<?php
/**
 * Admin Contacts Page
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

require_once BASE_PATH . '/models/Contact.php';

$contactModel = new Contact();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$contacts = $contactModel->getAllContacts($perPage, $offset);
$totalContacts = $contactModel->countContacts();
$totalPages = ceil($totalContacts / $perPage);

$pageTitle = 'Pesan Kontak';
require_once BASE_PATH . '/views/admin/layouts/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Pesan Kontak</h2>
</div>

<!-- Contacts Table -->
<div class="card">
    <div class="card-body">
        <?php if (empty($contacts)): ?>
            <div class="text-center py-5">
                <i class="fas fa-envelope fa-4x text-muted mb-3"></i>
                <p class="text-muted">Belum ada pesan</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Subjek</th>
                            <th>Pesan</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $contact): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($contact['nama']); ?></td>
                                <td><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td><?php echo htmlspecialchars($contact['subjek']); ?></td>
                                <td><?php echo htmlspecialchars(substr($contact['pesan'], 0, 50)) . '...'; ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($contact['tanggal'])); ?></td>
                                <td>
                                    <button onclick="viewMessage(<?php echo $contact['id']; ?>)" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                    <button onclick="confirmDelete(<?php echo $contact['id']; ?>)" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
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
                                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="messageContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

function viewMessage(id) {
    $.ajax({
        url: '<?php echo SITE_URL; ?>/ajax/admin/get-contact.php',
        type: 'GET',
        data: { id: id },
        success: function(response) {
            if (response.success) {
                const contact = response.data;
                const html = `
                    <p><strong>Nama:</strong> ${contact.nama}</p>
                    <p><strong>Email:</strong> ${contact.email}</p>
                    <p><strong>Subjek:</strong> ${contact.subjek}</p>
                    <p><strong>Tanggal:</strong> ${contact.tanggal}</p>
                    <hr>
                    <p><strong>Pesan:</strong></p>
                    <p>${contact.pesan}</p>
                `;
                document.getElementById('messageContent').innerHTML = html;
                messageModal.show();
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
        title: 'Hapus Pesan?',
        text: 'Apakah Anda yakin ingin menghapus pesan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?php echo SITE_URL; ?>/ajax/admin/delete-contact.php',
                type: 'POST',
                data: { id: id },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Terhapus!', 'Pesan berhasil dihapus.', 'success').then(() => {
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

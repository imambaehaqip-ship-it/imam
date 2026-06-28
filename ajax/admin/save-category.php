<?php
/**
 * AJAX Handler - Save Category
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/../..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

// Check if user is admin
if (!isAdmin()) {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit;
}

require_once BASE_PATH . '/models/Category.php';

$categoryModel = new Category();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$nama = sanitize($_POST['nama']);
$deskripsi = sanitize($_POST['deskripsi']);
$status = sanitize($_POST['status']);

if (empty($nama)) {
    echo json_encode(['success' => false, 'message' => 'Nama kategori wajib diisi']);
    exit;
}

$data = [
    'nama' => $nama,
    'deskripsi' => $deskripsi,
    'status' => $status
];

if ($id > 0) {
    $result = $categoryModel->updateCategory($id, $data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui kategori']);
    }
} else {
    $result = $categoryModel->createCategory($data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Kategori berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan kategori']);
    }
}

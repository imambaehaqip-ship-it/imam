<?php
/**
 * AJAX Handler - Save Banner
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

require_once BASE_PATH . '/models/Banner.php';

$bannerModel = new Banner();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$judul = sanitize($_POST['judul']);
$deskripsi = sanitize($_POST['deskripsi']);
$link = sanitize($_POST['link']);
$urutan = (int)$_POST['urutan'];
$status = sanitize($_POST['status']);

if (empty($judul)) {
    echo json_encode(['success' => false, 'message' => 'Judul banner wajib diisi']);
    exit;
}

// Handle image upload
$gambar = null;
if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['gambar'];
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxSize = MAX_FILE_SIZE;
    
    if (!in_array($file['type'], $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Format file tidak valid']);
        exit;
    }
    
    if ($file['size'] > $maxSize) {
        echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar']);
        exit;
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'banner_' . time() . '_' . uniqid() . '.' . $extension;
    $uploadPath = UPLOAD_PATH . '/banners/' . $filename;
    
    if (!is_dir(UPLOAD_PATH . '/banners')) {
        mkdir(UPLOAD_PATH . '/banners', 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        $gambar = $filename;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        exit;
    }
} else if ($id > 0) {
    // Get existing banner image
    $existingBanner = $bannerModel->getBannerById($id);
    if ($existingBanner) {
        $gambar = $existingBanner['gambar'];
    }
}

if (!$gambar) {
    echo json_encode(['success' => false, 'message' => 'Gambar banner wajib diupload']);
    exit;
}

$data = [
    'judul' => $judul,
    'deskripsi' => $deskripsi,
    'link' => $link,
    'urutan' => $urutan,
    'status' => $status,
    'gambar' => $gambar
];

if ($id > 0) {
    $result = $bannerModel->updateBanner($id, $data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Banner berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui banner']);
    }
} else {
    $result = $bannerModel->createBanner($data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Banner berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan banner']);
    }
}

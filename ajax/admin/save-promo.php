<?php
/**
 * AJAX Handler - Save Promo
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

require_once BASE_PATH . '/models/Promo.php';

$promoModel = new Promo();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$kode = strtoupper(sanitize($_POST['kode']));
$nilai_diskon = (float)$_POST['nilai_diskon'];
$tipe_diskon = sanitize($_POST['tipe_diskon']);
$berlaku_hingga = sanitize($_POST['berlaku_hingga']);
$min_belanja = (float)$_POST['min_belanja'];
$status = sanitize($_POST['status']);

if (empty($kode) || empty($nilai_diskon) || empty($berlaku_hingga)) {
    echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
    exit;
}

// Check if promo code already exists
$existingPromo = $promoModel->getPromoByCode($kode);
if ($existingPromo && (!$id || $existingPromo['id'] != $id)) {
    echo json_encode(['success' => false, 'message' => 'Kode promo sudah digunakan']);
    exit;
}

$data = [
    'kode' => $kode,
    'nilai_diskon' => $nilai_diskon,
    'tipe_diskon' => $tipe_diskon,
    'berlaku_hingga' => $berlaku_hingga,
    'min_belanja' => $min_belanja,
    'status' => $status
];

if ($id > 0) {
    $result = $promoModel->updatePromo($id, $data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Promo berhasil diperbarui']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui promo']);
    }
} else {
    $result = $promoModel->createPromo($data);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Promo berhasil ditambahkan']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan promo']);
    }
}

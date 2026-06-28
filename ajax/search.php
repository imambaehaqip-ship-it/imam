<?php
/**
 * AJAX Search Handler
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . '/..');
}

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/models/Product.php';

header('Content-Type: application/json');

// Check if request is AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request']);
}

$productModel = new Product();
$keyword = sanitize($_GET['keyword'] ?? '');

if (strlen($keyword) < 2) {
    sendJsonResponse(['success' => false, 'message' => 'Keyword minimal 2 karakter']);
}

$products = $productModel->searchProducts($keyword, 10);

if ($products) {
    $results = [];
    foreach ($products as $product) {
        $results[] = [
            'id' => $product['id'],
            'name' => $product['nama_produk'],
            'slug' => $product['slug'],
            'price' => formatPrice(calculateDiscountPrice($product['harga'], $product['diskon'])),
            'image' => $product['foto_utama'],
            'category' => $product['nama_kategori']
        ];
    }
    sendJsonResponse(['success' => true, 'results' => $results]);
} else {
    sendJsonResponse(['success' => false, 'message' => 'Produk tidak ditemukan']);
}

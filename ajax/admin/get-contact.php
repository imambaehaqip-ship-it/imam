<?php
/**
 * AJAX Handler - Get Contact Message
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

require_once BASE_PATH . '/models/Contact.php';

$contactModel = new Contact();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID pesan tidak valid']);
    exit;
}

$contact = $contactModel->getContactById($id);

if ($contact) {
    echo json_encode([
        'success' => true,
        'data' => [
            'nama' => $contact['nama'],
            'email' => $contact['email'],
            'subjek' => $contact['subjek'],
            'pesan' => $contact['pesan'],
            'tanggal' => date('d/m/Y H:i', strtotime($contact['tanggal']))
        ]
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Pesan tidak ditemukan']);
}

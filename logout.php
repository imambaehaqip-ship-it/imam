<?php
/**
 * Logout
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/includes/functions.php';
require_once BASE_PATH . '/includes/auth.php';

logout();
setFlash('success', 'Anda telah logout');
redirect(SITE_URL . '/login.php');

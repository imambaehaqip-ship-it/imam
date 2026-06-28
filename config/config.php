<?php
/**
 * Configuration File
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path if not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'mamz_clothing');
define('DB_USER', 'root');
define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'Mamz Clothing');
define('SITE_TAGLINE', 'Simple Style, Premium Quality');
define('SITE_URL', 'http://localhost/imam');

// Upload Configuration
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('PRODUCT_UPLOAD_PATH', UPLOAD_PATH . 'products/');
define('PAYMENT_UPLOAD_PATH', UPLOAD_PATH . 'payment/');
define('MAX_FILE_SIZE', 5242880); // 5MB in bytes

// Allowed File Types
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Pagination
define('PRODUCTS_PER_PAGE', 12);
define('ADMIN_PRODUCTS_PER_PAGE', 20);

// Session Configuration
define('SESSION_NAME', 'mamz_session');
define('SESSION_LIFETIME', 86400); // 24 hours

// Email Configuration (for future use)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('FROM_EMAIL', 'noreply@mamzclothing.com');
define('FROM_NAME', 'Mamz Clothing');

// WhatsApp Configuration
define('WHATSAPP_NUMBER', '6281234567890');

// Google Maps API Key (for future use)
define('GOOGLE_MAPS_API_KEY', '');

// Timezone
date_default_timezone_set('Asia/Makassar');

// Error Reporting (Set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

<?php
/**
 * Bootstrap File - Routing and Initialization
 * Mamz Clothing - Fashion Marketplace
 */

// Define base path
define('BASE_PATH', __DIR__);

// Load configuration
require_once BASE_PATH . '/config/config.php';

// Load helper functions
require_once BASE_PATH . '/includes/functions.php';

// Start session
session_name(SESSION_NAME);
session_start([
    'cookie_lifetime' => SESSION_LIFETIME,
    'cookie_httponly' => true,
    'cookie_secure' => false, // Set to true if using HTTPS
    'use_strict_mode' => true,
    'use_cookies' => true,
    'use_only_cookies' => true
]);

// Set timezone
date_default_timezone_set(DEFAULT_TIMEZONE);

// Error reporting (disable in production)
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Simple routing for admin panel
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Remove base path from request path
$basePath = dirname($_SERVER['SCRIPT_NAME']);
if ($basePath !== '/') {
    $requestPath = str_replace($basePath, '', $requestPath);
}

// Remove leading slash
$requestPath = ltrim($requestPath, '/');

// Handle admin routes
if (strpos($requestPath, 'admin/') === 0) {
    $adminFile = BASE_PATH . '/' . $requestPath;
    
    // Check if file exists
    if (file_exists($adminFile)) {
        require_once $adminFile;
    } else {
        // Default to admin dashboard
        require_once BASE_PATH . '/admin/dashboard.php';
    }
    exit;
}

// For user pages, the routing is handled by direct file access
// This bootstrap file is included in each page for initialization

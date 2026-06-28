<?php
/**
 * Admin Header Layout
 * Mamz Clothing - Fashion Marketplace
 */

if (!isset($pageTitle)) {
    $pageTitle = 'Admin Panel';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>/assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="sidebar-brand"><?php echo SITE_NAME; ?></h4>
            <small class="sidebar-subtitle">Admin Panel</small>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/dashboard.php">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/products.php">
                        <i class="fas fa-box me-2"></i>Produk
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/categories.php">
                        <i class="fas fa-tags me-2"></i>Kategori
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/orders.php">
                        <i class="fas fa-shopping-cart me-2"></i>Pesanan
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/users.php">
                        <i class="fas fa-users me-2"></i>Pengguna
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'banners.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/banners.php">
                        <i class="fas fa-images me-2"></i>Banner
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'promos.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/promos.php">
                        <i class="fas fa-percent me-2"></i>Promo
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reviews.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/reviews.php">
                        <i class="fas fa-star me-2"></i>Ulasan
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contacts.php' ? 'active' : ''; ?>" href="<?php echo SITE_URL; ?>/admin/contacts.php">
                        <i class="fas fa-envelope me-2"></i>Kontak
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="sidebar-footer">
            <a href="<?php echo SITE_URL; ?>/index.php" class="btn btn-outline-light btn-sm">
                <i class="fas fa-home me-2"></i>Ke Website
            </a>
            <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-outline-danger btn-sm">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-secondary d-lg-none" id="sidebarToggle">
                            <i class="fas fa-bars"></i>
                        </button>
                        <h1 class="admin-page-title d-none d-lg-block"><?php echo $pageTitle; ?></h1>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    <?php echo strtoupper(substr($_SESSION['user_nama'] ?? 'A', 0, 1)); ?>
                                </div>
                                <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['user_nama'] ?? 'Admin'); ?></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/profile.php"><i class="fas fa-user me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Flash Messages -->
        <?php if (hasFlash()): ?>
            <div class="container-fluid mt-3">
                <?php $flash = getFlash(); ?>
                <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($flash['message']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Page Content -->
        <div class="container-fluid py-4">

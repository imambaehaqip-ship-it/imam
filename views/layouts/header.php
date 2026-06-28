<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo SITE_NAME . ' - ' . SITE_TAGLINE; ?>">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Google Fonts - Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>/assets/css/style.css" rel="stylesheet">
    
    <?php if (isset($extraCss)): ?>
        <?php echo $extraCss; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Loading Screen -->
    <div id="loading-screen">
        <div class="loader"></div>
    </div>
    
    <!-- Scroll Progress -->
    <div id="scroll-progress"></div>
    
    <!-- Back to Top -->
    <button id="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <!-- Navbar -->
    <nav class="navbar" id="main-navbar">
        <div class="container">
            <a href="<?php echo SITE_URL; ?>/index.php" class="navbar-brand">
                <i class="fas fa-tshirt"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <ul class="navbar-nav">
                <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">Produk</a></li>
                <li><a href="<?php echo SITE_URL; ?>/categories.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>">Kategori</a></li>
                <li><a href="<?php echo SITE_URL; ?>/about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">Tentang Kami</a></li>
                <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>">Hubungi Kami</a></li>
            </ul>
            
            <div class="navbar-actions">
                <a href="<?php echo SITE_URL; ?>/cart.php" class="nav-icon-btn cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge cart-count"><?php echo getCartCount(); ?></span>
                </a>
                
                <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/profile.php" class="nav-icon-btn">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="<?php echo SITE_URL; ?>/logout.php" class="nav-icon-btn">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-sm">Login</a>
                    <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-outline btn-sm">Register</a>
                <?php endif; ?>
                
                <button class="nav-icon-btn dark-mode-toggle">
                    <i class="fas fa-moon"></i>
                </button>
                
                <button class="nav-icon-btn mobile-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu-overlay"></div>
    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <a href="<?php echo SITE_URL; ?>/index.php" class="navbar-brand">
                <i class="fas fa-tshirt"></i>
                <?php echo SITE_NAME; ?>
            </a>
            <button class="mobile-menu-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mobile-nav">
            <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link">Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>/products.php" class="nav-link">Produk</a></li>
            <li><a href="<?php echo SITE_URL; ?>/categories.php" class="nav-link">Kategori</a></li>
            <li><a href="<?php echo SITE_URL; ?>/about.php" class="nav-link">Tentang Kami</a></li>
            <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link">Hubungi Kami</a></li>
        </ul>
        <div class="mobile-menu-actions">
            <a href="<?php echo SITE_URL; ?>/cart.php" class="nav-icon-btn cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-badge cart-count"><?php echo getCartCount(); ?></span>
            </a>
            
            <?php if (isLoggedIn()): ?>
                <a href="<?php echo SITE_URL; ?>/profile.php" class="nav-icon-btn">
                    <i class="fas fa-user"></i>
                </a>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="nav-icon-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-sm w-100">Login</a>
                <a href="<?php echo SITE_URL; ?>/register.php" class="btn btn-outline btn-sm w-100">Register</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Flash Messages -->
    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="container mt-3">
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo $flash['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main>

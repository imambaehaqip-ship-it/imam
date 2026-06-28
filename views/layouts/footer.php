    </main>
    
    <!-- Footer - Premium Design -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up">
                    <div class="footer-brand">
                        <i class="fas fa-tshirt me-2"></i>
                        <?php echo SITE_NAME; ?>
                    </div>
                    <p class="footer-tagline"><?php echo SITE_TAGLINE; ?></p>
                    <p class="footer-description">
                        Mamz Clothing adalah marketplace fashion yang menyediakan berbagai pilihan pakaian pria dan wanita dengan desain modern, kualitas terbaik, dan harga terjangkau.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                    <h4 class="footer-title">Menu</h4>
                    <a href="<?php echo SITE_URL; ?>/index.php" class="footer-link">Home</a>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="footer-link">Produk</a>
                    <a href="<?php echo SITE_URL; ?>/categories.php" class="footer-link">Kategori</a>
                    <a href="<?php echo SITE_URL; ?>/about.php" class="footer-link">Tentang Kami</a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="footer-link">Hubungi Kami</a>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                    <h4 class="footer-title">Kategori</h4>
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=kaos" class="footer-link">Kaos</a>
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=kemeja" class="footer-link">Kemeja</a>
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=hoodie" class="footer-link">Hoodie</a>
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=jaket" class="footer-link">Jaket</a>
                    <a href="<?php echo SITE_URL; ?>/products.php?kategori=celana" class="footer-link">Celana</a>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                    <h4 class="footer-title">Kontak</h4>
                    <div class="footer-contact">
                        <p class="mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Kota Mataram, Nusa Tenggara Barat
                        </p>
                        <p class="mb-2">
                            <i class="fab fa-whatsapp me-2"></i>
                            <?php echo WHATSAPP_NUMBER; ?>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            info@mamzclothing.com
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom" data-aos="fade-up" data-aos-delay="400">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/dist/sweetalert2.all.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/cart.js"></script>
    <script src="<?php echo SITE_URL; ?>/assets/js/wishlist.js"></script>
    
    <?php if (isset($extraJs)): ?>
        <?php echo $extraJs; ?>
    <?php endif; ?>
</body>
</html>

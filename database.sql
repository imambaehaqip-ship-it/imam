-- ============================================
-- DATABASE: mamz_clothing
-- ============================================
-- Mamz Clothing - Fashion Marketplace
-- Simple Style, Premium Quality
-- ============================================

CREATE DATABASE IF NOT EXISTS mamz_clothing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mamz_clothing;

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nomor_hp VARCHAR(20),
    alamat TEXT,
    kota VARCHAR(50),
    provinsi VARCHAR(50),
    kode_pos VARCHAR(10),
    role ENUM('admin', 'user') DEFAULT 'user',
    foto_profil VARCHAR(255) DEFAULT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: kategori
-- ============================================
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    deskripsi TEXT,
    icon VARCHAR(50),
    urutan INT DEFAULT 0,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: produk
-- ============================================
CREATE TABLE IF NOT EXISTS produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kategori_id INT NOT NULL,
    nama_produk VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL UNIQUE,
    deskripsi TEXT,
    harga DECIMAL(15,2) NOT NULL,
    diskon DECIMAL(5,2) DEFAULT 0.00,
    stok INT DEFAULT 0,
    ukuran VARCHAR(100),
    warna VARCHAR(100),
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_review INT DEFAULT 0,
    total_terjual INT DEFAULT 0,
    foto_utama VARCHAR(255),
    foto_galeri TEXT,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    is_featured BOOLEAN DEFAULT FALSE,
    is_popular BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_kategori (kategori_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_popular (is_popular)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: wishlist
-- ============================================
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_wishlist (user_id, produk_id),
    INDEX idx_user (user_id),
    INDEX idx_produk (produk_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: keranjang
-- ============================================
CREATE TABLE IF NOT EXISTS keranjang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    quantity INT DEFAULT 1,
    ukuran VARCHAR(50),
    warna VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_keranjang (user_id, produk_id),
    INDEX idx_user (user_id),
    INDEX idx_produk (produk_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: pesanan
-- ============================================
CREATE TABLE IF NOT EXISTS pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nomor_pesanan VARCHAR(50) NOT NULL UNIQUE,
    total_harga DECIMAL(15,2) NOT NULL,
    status_pesanan ENUM('pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'pending',
    status_pembayaran ENUM('menunggu', 'diterima', 'ditolak') DEFAULT 'menunggu',
    
    -- Data Penerima
    nama_penerima VARCHAR(100) NOT NULL,
    email_penerima VARCHAR(100) NOT NULL,
    nomor_hp VARCHAR(20) NOT NULL,
    
    -- Alamat Pengiriman
    provinsi VARCHAR(50) NOT NULL,
    kota VARCHAR(50) NOT NULL,
    kecamatan VARCHAR(50),
    kelurahan VARCHAR(50),
    kode_pos VARCHAR(10),
    detail_alamat TEXT NOT NULL,
    
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_nomor_pesanan (nomor_pesanan),
    INDEX idx_status_pesanan (status_pesanan),
    INDEX idx_status_pembayaran (status_pembayaran)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: detail_pesanan
-- ============================================
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    produk_id INT NOT NULL,
    nama_produk VARCHAR(200) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    quantity INT NOT NULL,
    ukuran VARCHAR(50),
    warna VARCHAR(50),
    subtotal DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_pesanan (pesanan_id),
    INDEX idx_produk (produk_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: pembayaran
-- ============================================
CREATE TABLE IF NOT EXISTS pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pesanan_id INT NOT NULL,
    metode_pembayaran VARCHAR(100) NOT NULL,
    nama_pengirim VARCHAR(100) NOT NULL,
    nominal_transfer DECIMAL(15,2) NOT NULL,
    tanggal_transfer DATE NOT NULL,
    bukti_pembayaran VARCHAR(255) NOT NULL,
    status ENUM('menunggu_verifikasi', 'diterima', 'ditolak') DEFAULT 'menunggu_verifikasi',
    catatan_admin TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_pesanan (pesanan_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: review
-- ============================================
CREATE TABLE IF NOT EXISTS review (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    produk_id INT NOT NULL,
    pesanan_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    komentar TEXT,
    foto_review VARCHAR(255),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (pesanan_id) REFERENCES pesanan(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_produk (produk_id),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: promo
-- ============================================
CREATE TABLE IF NOT EXISTS promo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode_promo VARCHAR(50) NOT NULL UNIQUE,
    nama_promo VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    tipe_diskon ENUM('persentase', 'nominal') DEFAULT 'persentase',
    nilai_diskon DECIMAL(15,2) NOT NULL,
    minimal_belanja DECIMAL(15,2) DEFAULT 0,
    maksimal_diskon DECIMAL(15,2) DEFAULT 0,
    kuota INT DEFAULT 0,
    kuota_terpakai INT DEFAULT 0,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_kode_promo (kode_promo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: banner
-- ============================================
CREATE TABLE IF NOT EXISTS banner (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(200) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255) NOT NULL,
    link VARCHAR(255),
    urutan INT DEFAULT 0,
    tipe ENUM('hero', 'promo', 'sidebar') DEFAULT 'hero',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipe (tipe),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: metode_pembayaran
-- ============================================
CREATE TABLE IF NOT EXISTS metode_pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_metode VARCHAR(100) NOT NULL UNIQUE,
    jenis ENUM('e-wallet', 'bank_transfer', 'qris') DEFAULT 'bank_transfer',
    nomor_rekening VARCHAR(100),
    atas_nama VARCHAR(100),
    logo VARCHAR(255),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    urutan INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE: kontak
-- ============================================
CREATE TABLE IF NOT EXISTS kontak (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subjek VARCHAR(200) NOT NULL,
    pesan TEXT NOT NULL,
    status ENUM('belum_dibaca', 'dibaca', 'dibalas') DEFAULT 'belum_dibaca',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INSERT DEFAULT DATA
-- ============================================

-- Insert Admin User
INSERT INTO users (nama_lengkap, email, password, nomor_hp, role, status) VALUES
('Admin Mamz Clothing', 'admin@mamzclothing.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081234567890', 'admin', 'aktif');

-- Insert Demo User
INSERT INTO users (nama_lengkap, email, password, nomor_hp, alamat, kota, provinsi, kode_pos, role, status) VALUES
('User Demo', 'user@demo.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '081987654321', 'Jl. Demo No. 123', 'Mataram', 'Nusa Tenggara Barat', '83111', 'user', 'aktif');

-- Insert Kategori
INSERT INTO kategori (nama_kategori, slug, deskripsi, icon, urutan, status) VALUES
('Kaos', 'kaos', 'Kaos casual dengan berbagai pilihan desain', 'fa-tshirt', 1, 'aktif'),
('Kemeja', 'kemeja', 'Kemeja formal dan casual', 'fa-shirt', 2, 'aktif'),
('Hoodie', 'hoodie', 'Hoodie nyaman untuk sehari-hari', 'fa-hoodie', 3, 'aktif'),
('Jaket', 'jaket', 'Jaket untuk berbagai aktivitas', 'fa-vest', 4, 'aktif'),
('Sweater', 'sweater', 'Sweater hangat dan stylish', 'fa-mitten', 5, 'aktif'),
('Polo Shirt', 'polo-shirt', 'Polo shirt elegan', 'fa-shirt', 6, 'aktif'),
('Celana', 'celana', 'Celana berbagai model', 'fa-socks', 7, 'aktif'),
('Fashion Pria', 'fashion-pria', 'Koleksi fashion khusus pria', 'fa-person', 8, 'aktif'),
('Fashion Wanita', 'fashion-wanita', 'Koleksi fashion khusus wanita', 'fa-person-dress', 9, 'aktif');

-- Insert Metode Pembayaran
INSERT INTO metode_pembayaran (nama_metode, jenis, nomor_rekening, atas_nama, status, urutan) VALUES
('DANA', 'e-wallet', '081234567890', 'Imam Baehaqi', 'aktif', 1),
('OVO', 'e-wallet', '081234567890', 'Imam Baehaqi', 'aktif', 2),
('GoPay', 'e-wallet', '081234567890', 'Imam Baehaqi', 'aktif', 3),
('ShopeePay', 'e-wallet', '081234567890', 'Imam Baehaqi', 'aktif', 4),
('BCA', 'bank_transfer', '1234567890', 'Imam Baehaqi', 'aktif', 5),
('BRI', 'bank_transfer', '0987654321', 'Imam Baehaqi', 'aktif', 6),
('BNI', 'bank_transfer', '1122334455', 'Imam Baehaqi', 'aktif', 7),
('Mandiri', 'bank_transfer', '5566778899', 'Imam Baehaqi', 'aktif', 8);

-- Insert Sample Products
INSERT INTO produk (kategori_id, nama_produk, slug, deskripsi, harga, diskon, stok, ukuran, warna, rating, total_review, total_terjual, foto_utama, foto_galeri, status, is_featured, is_popular) VALUES
(1, 'Kaos Polos Premium', 'kaos-polos-premium', 'Kaos polos dengan bahan katun premium yang nyaman dipakai sehari-hari', 150000, 0, 50, 'S,M,L,XL,XXL', 'Hitam,Putih,Biru,Merah', 4.5, 10, 25, 'kaos-polos-hitam.jpg', 'kaos-polos-putih.jpg,kaos-polos-biru.jpg', 'aktif', TRUE, TRUE),
(2, 'Kemeja Formal Slim Fit', 'kemeja-formal-slim-fit', 'Kemeja formal dengan potongan slim fit yang elegan', 350000, 10, 30, 'S,M,L,XL', 'Putih,Biru Tua,Hitam', 4.7, 15, 40, 'kemeja-putih.jpg', 'kemeja-biru.jpg,kemeja-hitam.jpg', 'aktif', TRUE, TRUE),
(3, 'Hoodie Oversized', 'hoodie-oversized', 'Hoodie oversized dengan bahan fleece yang hangat', 450000, 0, 25, 'L,XL,XXL', 'Hitam,Abu-abu,Navy', 4.8, 20, 35, 'hoodie-hitam.jpg', 'hoodie-abu.jpg,hoodie-navy.jpg', 'aktif', TRUE, TRUE),
(4, 'Jaket Bomber', 'jaket-bomber', 'Jaket bomber dengan desain modern dan stylish', 550000, 15, 20, 'M,L,XL', 'Hitam,Hijau Army,Krem', 4.6, 12, 18, 'jaket-bomber-hitam.jpg', 'jaket-bomber-army.jpg', 'aktif', FALSE, TRUE),
(5, 'Sweater Rajut', 'sweater-rajut', 'Sweater rajut dengan motif yang menarik', 380000, 0, 35, 'M,L,XL', 'Merah,Kuning,Biru', 4.4, 8, 22, 'sweater-merah.jpg', 'sweater-kuning.jpg', 'aktif', FALSE, FALSE),
(6, 'Polo Shirt Classic', 'polo-shirt-classic', 'Polo shirt classic dengan kerah yang elegan', 280000, 5, 40, 'S,M,L,XL', 'Putih,Biru,Hitam', 4.5, 14, 30, 'polo-putih.jpg', 'polo-biru.jpg', 'aktif', TRUE, FALSE),
(7, 'Celana Chino', 'celana-chino', 'Celana chino dengan bahan yang nyaman dan stylish', 400000, 0, 45, '28,30,32,34,36', 'Hitam,Coklat,Krem', 4.7, 18, 45, 'celana-chino-hitam.jpg', 'celana-chino-coklat.jpg', 'aktif', TRUE, TRUE),
(8, 'Celana Jeans', 'celana-jeans', 'Celana jeans dengan berbagai model', 420000, 10, 50, '28,30,32,34,36', 'Biru Tua,Hitam', 4.6, 16, 50, 'celana-jeans-biru.jpg', 'celana-jeans-hitam.jpg', 'aktif', FALSE, TRUE),
(9, 'Dress Wanita Elegant', 'dress-wanita-elegant', 'Dress wanita dengan desain elegant dan modern', 550000, 0, 20, 'S,M,L', 'Merah,Hitam,Biru', 4.8, 22, 28, 'dress-merah.jpg', 'dress-hitam.jpg', 'aktif', TRUE, TRUE),
(10, 'Blouse Wanita', 'blouse-wanita', 'Blouse wanita dengan berbagai pilihan motif', 320000, 5, 30, 'S,M,L,XL', 'Putih,Pink,Biru Muda', 4.5, 11, 25, 'blouse-putih.jpg', 'blouse-pink.jpg', 'aktif', FALSE, FALSE);

-- Insert Sample Banner
INSERT INTO banner (judul, deskripsi, gambar, link, urutan, tipe, status) VALUES
('Summer Sale 2024', 'Diskon hingga 50% untuk semua produk', 'banner-summer.jpg', '', 1, 'hero', 'aktif'),
('New Collection', 'Koleksi terbaru telah hadir', 'banner-new.jpg', '', 2, 'hero', 'aktif'),
('Flash Sale', 'Diskon spesial hari ini', 'banner-flash.jpg', '', 3, 'promo', 'aktif');

-- Insert Sample Promo
INSERT INTO promo (kode_promo, nama_promo, deskripsi, tipe_diskon, nilai_diskon, minimal_belanja, maksimal_diskon, tanggal_mulai, tanggal_selesai, status) VALUES
('MAMZ10', 'Diskon 10%', 'Diskon 10% untuk semua pembelian', 'persentase', 10.00, 100000, 50000, '2024-01-01', '2024-12-31', 'aktif'),
('MAMZ50K', 'Diskon 50K', 'Diskon 50.000 untuk pembelian minimal 300K', 'nominal', 50000, 300000, 0, '2024-01-01', '2024-12-31', 'aktif');

-- Insert Sample Wishlist
INSERT INTO wishlist (user_id, produk_id) VALUES
(2, 1),
(2, 3),
(2, 5),
(2, 7);

-- Insert Sample Cart
INSERT INTO keranjang (user_id, produk_id, quantity, ukuran, warna) VALUES
(2, 1, 2, 'L', 'Hitam'),
(2, 3, 1, 'XL', 'Navy');

-- Insert Sample Orders
INSERT INTO pesanan (user_id, nomor_pesanan, total_harga, status_pesanan, status_pembayaran, nama_penerima, email_penerima, nomor_hp, provinsi, kota, kecamatan, kelurahan, kode_pos, detail_alamat, catatan) VALUES
(2, 'ORD-20240623-001', 330000, 'selesai', 'diterima', 'User Demo', 'user@demo.com', '081987654321', 'Nusa Tenggara Barat', 'Mataram', 'Selong', 'Selong', '83111', 'Jl. Demo No. 123', 'Tolong dikirim secepatnya'),
(2, 'ORD-20240624-002', 450000, 'dikirim', 'diterima', 'User Demo', 'user@demo.com', '081987654321', 'Nusa Tenggara Barat', 'Mataram', 'Selong', 'Selong', '83111', 'Jl. Demo No. 123', NULL);

-- Insert Sample Order Details
INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, harga, quantity, ukuran, warna, subtotal) VALUES
(1, 1, 'Kaos Polos Premium', 150000, 2, 'L', 'Hitam', 300000),
(1, 3, 'Hoodie Oversized', 450000, 1, 'XL', 'Navy', 450000),
(2, 3, 'Hoodie Oversized', 450000, 1, 'XL', 'Navy', 450000);

-- Insert Sample Payments
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, nama_pengirim, nominal_transfer, tanggal_transfer, bukti_pembayaran, status, catatan_admin) VALUES
(1, 'Transfer Bank BCA', 'User Demo', 330000, '2024-06-23', 'bukti-001.jpg', 'diterima', 'Pembayaran telah diverifikasi'),
(2, 'DANA', 'User Demo', 450000, '2024-06-24', 'bukti-002.jpg', 'diterima', NULL);

-- Insert Sample Reviews
INSERT INTO review (user_id, produk_id, pesanan_id, rating, komentar, status) VALUES
(2, 1, 1, 5, 'Kualitas sangat bagus, bahan nyaman dipakai', 'aktif'),
(2, 3, 1, 4, 'Hoodie nya hangat dan ukurannya pas', 'aktif'),
(2, 5, NULL, 5, 'Sweater rajut dengan motif yang menarik', 'aktif');

-- ============================================
-- END OF DATABASE SCHEMA
-- ============================================

<?php
$pdo = new PDO('mysql:host=localhost;dbname=mamz_clothing;charset=utf8mb4', 'root', '');
$email = 'admin@example.com';
$password = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, email, password, role, status) VALUES (:nama, :email, :password, :role, :status) ON DUPLICATE KEY UPDATE nama_lengkap=VALUES(nama_lengkap), password=VALUES(password), role=VALUES(role), status=VALUES(status)");
$stmt->execute([':nama' => 'Administrator', ':email' => $email, ':password' => $password, ':role' => 'admin', ':status' => 'aktif']);
echo "Admin account ready\n";

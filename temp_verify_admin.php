<?php
$pdo = new PDO('mysql:host=localhost;dbname=mamz_clothing;charset=utf8mb4', 'root', '');
$stmt = $pdo->query('SELECT email, role, status FROM users WHERE email = "admin@example.com"');
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    echo json_encode($row, JSON_UNESCAPED_SLASHES) . PHP_EOL;
} else {
    echo "not_found\n";
}

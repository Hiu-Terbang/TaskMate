<?php
$host = '127.0.0.1';
$db = 'taskmate';
$user = 'root';
$pass = '';
$port = 3307; // Sesuaikan dengan port MySQL XAMPP NEW

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database belum terhubung. Periksa MySQL XAMPP dan port pada config/database.php.');
}

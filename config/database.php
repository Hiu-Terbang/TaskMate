<?php
// Pengaturan database.
// - Lokal (XAMPP): nilai bawaan di bawah sudah cocok, tidak perlu diubah.
// - Hosting: salin config/config.example.php jadi config/config.local.php,
//   lalu isi dengan data MySQL dari panel hosting. File itu tidak ikut ke Git.
$config = [
    'host' => '127.0.0.1',
    'port' => 3307, // Port MySQL XAMPP NEW
    'name' => 'taskmate',
    'user' => 'root',
    'pass' => '',
];
if (is_file(__DIR__ . '/config.local.php')) {
    $config = array_merge($config, require __DIR__ . '/config.local.php');
}
// Variabel environment (kalau hosting mendukung) mengalahkan semua nilai di atas.
foreach (['host' => 'DB_HOST', 'port' => 'DB_PORT', 'name' => 'DB_NAME', 'user' => 'DB_USER', 'pass' => 'DB_PASS'] as $key => $env) {
    $value = getenv($env);
    if ($value !== false && $value !== '') $config[$key] = $value;
}

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4",
        $config['user'],
        $config['pass'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database belum terhubung. Periksa pengaturan di config/database.php atau config/config.local.php.');
}

<?php
// Konfigurasi Database
$db_host = 'localhost';
$db_name = 'SewaAlatOutdoor';
$db_user = 'root'; // Default Laragon
$db_pass = '';     // Default Laragon (kosong)

// Opsi untuk PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, $options);
} catch (\PDOException $e) {
    // Menampilkan pesan error
    throw new \PDOException("Koneksi database gagal. Periksa kembali konfigurasi Anda.", (int)$e->getCode());
}

// Definisikan konstanta direktori upload
define('UPLOAD_DIR', '../public/images/uploads/');
?>
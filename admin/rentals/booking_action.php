<?php
session_start();
// Path require: naik 2 level ke config
require '../../config/config.php';

// Keamanan: Hanya user yang login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    $_SESSION['message'] = 'Anda harus login untuk booking.';
    $_SESSION['message_type'] = 'error';
    header("Location: ../../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../../user/catalog.php");
    exit();
}

$item_id = $_POST['item_id'] ?? null;
$user_id = $_SESSION['user_id'];
$tanggal_sewa = $_POST['tanggal_sewa'] ?? null;
$tanggal_kembali = $_POST['tanggal_kembali'] ?? null;

// Validasi
if (empty($item_id) || empty($tanggal_sewa) || empty($tanggal_kembali)) {
    $_SESSION['message'] = 'Data tidak valid. Pastikan semua tanggal terisi dengan benar.';
    $_SESSION['message_type'] = 'error';
    header("Location: ../../user/booking/booking.php?item_id=" . $item_id);
    exit();
}

if ($tanggal_kembali <= $tanggal_sewa) {
    $_SESSION['message'] = 'Tanggal kembali harus lebih besar dari tanggal sewa.';
    $_SESSION['message_type'] = 'error';
    header("Location: ../../user/booking/booking.php?item_id=" . $item_id);
    exit();
}

try {
    // Validasi item exist
    $check_stmt = $pdo->prepare("SELECT id FROM items WHERE id = ?");
    $check_stmt->execute([$item_id]);
    if (!$check_stmt->fetch()) {
        throw new Exception("Item tidak ditemukan.");
    }

    // Insert booking
    $sql = "INSERT INTO rentals (user_id, item_id, tanggal_sewa, tanggal_kembali, status) 
            VALUES (?, ?, ?, ?, 'dipinjam')";
    $pdo->prepare($sql)->execute([$user_id, $item_id, $tanggal_sewa, $tanggal_kembali]);
    
    $_SESSION['message'] = 'Peminjaman berhasil! Pesanan Anda sedang diproses.';
    $_SESSION['message_type'] = 'success';
    header("Location: ../../user/catalog.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['message'] = 'Terjadi kesalahan database: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: ../../user/booking/booking.php?item_id=" . $item_id);
    exit();
} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: ../../user/booking/booking.php?item_id=" . $item_id);
    exit();
}
?>
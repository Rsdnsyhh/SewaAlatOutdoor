<?php
session_start();
// Path require: naik 2 level ke config
require '../../config/config.php';

// Keamanan: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak. Hanya admin yang bisa melakukan aksi ini.");
}

// Hanya terima POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Metode request tidak valid.");
}

$action = $_GET['action'] ?? '';
$id = $_POST['id'] ?? null;

// Validasi ID
if (empty($id) || !filter_var($id, FILTER_VALIDATE_INT)) {
    $_SESSION['message'] = 'ID pesanan tidak valid.';
    $_SESSION['message_type'] = 'error';
    header("Location: ../dashboard.php?page=rentals_list");
    exit();
}

try {
    switch ($action) {
        case 'update_status':
            // Update status menjadi dikembalikan
            $sql = "UPDATE rentals SET status = 'dikembalikan' WHERE id = ?";
            $pdo->prepare($sql)->execute([$id]);
            $_SESSION['message'] = 'Pesanan ditandai selesai!';
            $_SESSION['message_type'] = 'success';
            break;

        case 'delete':
            // Delete pesanan
            $sql = "DELETE FROM rentals WHERE id = ?";
            $pdo->prepare($sql)->execute([$id]);
            $_SESSION['message'] = 'Pesanan telah dihapus.';
            $_SESSION['message_type'] = 'success';
            break;

        default:
            $_SESSION['message'] = 'Aksi tidak dikenali.';
            $_SESSION['message_type'] = 'error';
    }
} catch (PDOException $e) {
    $_SESSION['message'] = 'Terjadi kesalahan database: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
}

// Redirect kembali ke halaman rentals_list
header("Location: ../dashboard.php?page=rentals_list");
exit();
?>
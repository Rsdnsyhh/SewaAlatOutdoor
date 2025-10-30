<?php
// File: admin/items/items_action.php
session_start();
require '../../config/config.php';

// Keamanan: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak.");
}

// Konfigurasi Upload
define('UPLOAD_DIR', '../../public/images/products/');
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 5 * 1024 * 1024; // 5MB

// Fungsi helper upload
function handleUpload($file) {
    global $allowed_types, $max_size;
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error upload: ' . $file['error']];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file maksimal 5MB.'];
    }
    
    // Cek MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan. Hanya JPG, PNG, GIF, WebP.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = uniqid('item_') . '.' . $extension;
    $target_file = UPLOAD_DIR . $filename;
    
    // Pastikan folder ada
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal memindahkan file.'];
    }
}

// Fungsi helper delete file
function deleteImageFile($filename) {
    if (empty($filename)) return;
    $filepath = UPLOAD_DIR . $filename;
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
}

// Ambil action dari GET atau POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    switch ($action) {
        // ========================================
        // CREATE - Tambah Alat Baru
        // ========================================
        case 'create':
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                die("Metode request tidak valid.");
            }
            
            $title = trim($_POST['title'] ?? '');
            $id_kategori = $_POST['id_kategori'] ?? null;
            $description = trim($_POST['description'] ?? '');
            
            // Validasi input
            if (empty($title)) {
                $_SESSION['message'] = 'Judul alat harus diisi!';
                $_SESSION['message_type'] = 'error';
                header("Location: ../dashboard.php?page=items_create");
                exit();
            }
            
            $image_filename = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $upload = handleUpload($_FILES['image']);
                if ($upload['success']) {
                    $image_filename = $upload['filename'];
                } else {
                    $_SESSION['message'] = $upload['message'];
                    $_SESSION['message_type'] = 'error';
                    header("Location: ../dashboard.php?page=items_create");
                    exit();
                }
            }
            
            $sql = "INSERT INTO items (title, id_kategori, description, image) VALUES (?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$title, $id_kategori, $description, $image_filename]);

            $_SESSION['message'] = 'Alat baru berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

        // ========================================
        // UPDATE - Edit Alat
        // ========================================
        case 'update':
            if ($_SERVER["REQUEST_METHOD"] != "POST") {
                die("Metode request tidak valid.");
            }
            
            $id = intval($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $id_kategori = $_POST['id_kategori'] ?? null;
            $description = trim($_POST['description'] ?? '');
            $existing_image = $_POST['existing_image'] ?? '';
            $image_filename = $existing_image;
            
            // Validasi input
            if (empty($title)) {
                $_SESSION['message'] = 'Judul alat harus diisi!';
                $_SESSION['message_type'] = 'error';
                header("Location: ../dashboard.php?page=items_edit&id=" . $id);
                exit();
            }

            // Handle upload gambar baru (opsional)
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $upload = handleUpload($_FILES['image']);
                if ($upload['success']) {
                    deleteImageFile($existing_image); // Hapus gambar lama
                    $image_filename = $upload['filename'];
                } else {
                    $_SESSION['message'] = $upload['message'];
                    $_SESSION['message_type'] = 'error';
                    header("Location: ../dashboard.php?page=items_edit&id=" . $id);
                    exit();
                }
            }

            $sql = "UPDATE items SET title=?, id_kategori=?, description=?, image=? WHERE id=?";
            $pdo->prepare($sql)->execute([$title, $id_kategori, $description, $image_filename, $id]);

            $_SESSION['message'] = 'Alat berhasil diperbarui!';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

        // ========================================
        // DELETE - Hapus Alat (Support GET & POST)
        // ========================================
        case 'delete':
            // Support both GET and POST
            $id = 0;
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $id = intval($_POST['id'] ?? 0);
            } else {
                $id = intval($_GET['id'] ?? 0);
            }
            
            if ($id <= 0) {
                $_SESSION['message'] = 'ID tidak valid!';
                $_SESSION['message_type'] = 'error';
                header("Location: ../dashboard.php?page=items_list");
                exit();
            }
            
            // Ambil nama file gambar sebelum dihapus
            $stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch(PDO::FETCH_ASSOC);
            $image_to_delete = $item['image'] ?? null;

            // Hapus data dari database
            $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$id]);
            
            // Hapus file gambar jika ada
            if ($image_to_delete) {
                deleteImageFile($image_to_delete);
            }

            $_SESSION['message'] = 'Alat berhasil dihapus.';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

        // ========================================
        // DEFAULT - Action tidak dikenali
        // ========================================
        default:
            $_SESSION['message'] = 'Aksi tidak dikenali.';
            $_SESSION['message_type'] = 'error';
            header("Location: ../dashboard.php?page=items_list");
            exit();
    }
} catch (PDOException $e) {
    $_SESSION['message'] = 'Error Database: ' . $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: ../dashboard.php?page=items_list");
    exit();
}
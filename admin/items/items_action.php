<?php
session_start();
require '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak.");
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    die("Metode request tidak valid.");
}

$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_size = 2 * 1024 * 1024; // 2MB
$action = $_GET['action'] ?? '';

// Fungsi helper upload
function handleUpload($file) {
    global $allowed_types, $max_size;
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error upload: ' . $file['error']];
    }
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file maks 2MB.'];
    }
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan.'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('item_') . '.' . $extension;
    
    // Target path: naik 3 level (dari admin/items/) ke root, lalu ke public/images/uploads/
    $target_file = '../../../' . UPLOAD_DIR . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Gagal memindahkan file.'];
    }
}

// Fungsi helper delete
function deleteImageFile($filename) {
    if (empty($filename)) return;
    // Path unlink: naik 3 level (dari admin/items/) ke root, lalu ke public/images/uploads/
    $filepath = '../../../' . UPLOAD_DIR . $filename;
    if (file_exists($filepath)) {
        @unlink($filepath);
    }
}

try {
    switch ($action) {
        case 'create':
            $title = $_POST['title'];
            $id_kategori = $_POST['id_kategori'];
            $description = $_POST['description'] ?? null;
            $price = $_POST['price_per_day'];
            $stock = $_POST['stock'];
            
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
            
            $sql = "INSERT INTO items (title, id_kategori, description, price_per_day, stock, image) VALUES (?, ?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$title, $id_kategori, $description, $price, $stock, $image_filename]);

            $_SESSION['message'] = 'Alat baru berhasil ditambahkan!';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

        case 'update':
            $id = $_POST['id'];
            $title = $_POST['title'];
            $id_kategori = $_POST['id_kategori'];
            $description = $_POST['description'] ?? null;
            $price = $_POST['price_per_day'];
            $stock = $_POST['stock'];
            $existing_image = $_POST['existing_image'];
            $image_filename = $existing_image;

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

            $sql = "UPDATE items SET title=?, id_kategori=?, description=?, price_per_day=?, stock=?, image=? WHERE id=?";
            $pdo->prepare($sql)->execute([$title, $id_kategori, $description, $price, $stock, $image_filename, $id]);

            $_SESSION['message'] = 'Alat berhasil diperbarui!';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

        case 'delete':
            $id = $_POST['id'];
            
            $stmt = $pdo->prepare("SELECT image FROM items WHERE id = ?");
            $stmt->execute([$id]);
            $item = $stmt->fetch();
            $image_to_delete = $item['image'] ?? null;

            $pdo->prepare("DELETE FROM items WHERE id = ?")->execute([$id]);
            
            deleteImageFile($image_to_delete);

            $_SESSION['message'] = 'Alat berhasil dihapus.';
            $_SESSION['message_type'] = 'success';
            header("Location: ../dashboard.php?page=items_list");
            exit();

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
?>
<?php
session_start();
require '../config/config.php';

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: catalog.php");
    exit();
}

try {
    $sql = "SELECT 
                items.*, 
                categories.nama_kategori 
            FROM items
            LEFT JOIN categories ON items.id_kategori = categories.id_kategori
            WHERE items.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        header("Location: catalog.php");
        exit();
    }
} catch (PDOException $e) {
    die("Gagal mengambil data item: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?php echo htmlspecialchars($item['title']); ?> - Sistem Sewa</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="header-controls">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="welcome-user">Hai, <b><?php echo htmlspecialchars($_SESSION['username']); ?>!</b></span>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="../admin/dashboard.php" class="login-link-header">Ke Dashboard</a>
                    <?php endif; ?>
                    <a href="../auth/logout.php" class="login-link-header logout">Logout</a>
                <?php else: ?>
                    <a href="../auth/register.php" class="login-link-header"><b>Register</b></a>
                    <a href="../auth/login.php" class="login-link-header"><b>Login</b></a>
                <?php endif; ?>
                <button class="dark-mode-toggle" id="darkModeToggle">Dark Mode</button>
            </div>
            
            <h1 class="logo"><a href="catalog.php" style="color:inherit; text-decoration:none;">Sistem Sewa Alat Outdoor</a></h1>
            <nav class="navigation">
                <ul class="nav-list">
                    <li class="nav-item"><a href="catalog.php#about" class="nav-link">Tentang</a></li>
                    <li class="nav-item"><a href="catalog.php#services" class="nav-link">Pilihan Alat</a></li>
                    <li class="nav-item"><a href="catalog.php#how" class="nav-link">Cara Sewa</a></li>
                    <li class="nav-item"><a href="catalog.php#contact" class="nav-link">Kontak</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <div class="item-detail-container">
                    <div class="item-detail-image-container">
                        <?php 
                        $image_path = UPLOAD_DIR . htmlspecialchars($item['image'] ?? '');
                        if (!empty($item['image']) && file_exists($image_path)): 
                        ?>
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-detail-image">
                        <?php else: ?>
                            <div class="item-detail-image-placeholder">
                                <span>Tidak Ada Gambar</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="item-detail-info">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p><strong>Kategori:</strong> <?php echo htmlspecialchars($item['nama_kategori'] ?? 'Tidak ada kategori'); ?></p>
                        
                        <p><strong>Deskripsi:</strong></p>
                        <div class="item-description">
                            <?php echo nl2br(htmlspecialchars($item['description'] ?: 'Tidak ada deskripsi.')); ?>
                        </div>
                        <hr class="detail-divider">
                        
                        <div class="booking-action-container">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <?php if ($_SESSION['role'] == 'user'): ?>
                                    <a href="booking/booking.php?item_id=<?php echo $item['id']; ?>" class="btn-submit booking-btn">Sewa Sekarang</a>
                                <?php else: ?>
                                    <p class_="booking-info">Anda login sebagai Admin. Hanya user yang bisa menyewa.</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <a href="../auth/login.php" class="btn-submit booking-btn">Login untuk Menyewa</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Sistem Sewa Alat Outdoor & Camping</p>
        </div>
    </footer>
    
    <script src="../public/js/script.js"></script>
</body>
</html>
<?php
session_start();
require '../config/config.php'; // Memuat koneksi dan UPLOAD_DIR

// Ambil data alat terbaru dari database
try {
    $stmt = $pdo->query("
        SELECT items.*, categories.nama_kategori 
        FROM items 
        LEFT JOIN categories ON items.id_kategori = categories.id_kategori
        ORDER BY items.created_at DESC 
        LIMIT 6
    ");
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $items = [];
    $error_db = "Gagal memuat data alat.";
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sewa Alat Outdoor & Camping</title>
    <link rel="stylesheet" href="../public/css/style.css">
    </head>
    <body>
    <header class="header">
        <div class="container header-container">
        
        <div class="header-controls">
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="welcome-user">
                    Hai, <b><?php echo htmlspecialchars($_SESSION['username']); ?>!</b>
                </span>
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
        
        <h1 class="logo">Sewa Alat Outdoor</h1>
        <p class="tagline">Perlengkapan lengkap untuk petualangan Anda. Mudah & Terpercaya.</p>
        <nav class="navigation">
            <ul class="nav-list">
            <li class="nav-item"><a href="#about" class="nav-link">Tentang</a></li>
            <li class="nav-item"><a href="#services" class="nav-link">Pilihan Alat</a></li>
            <li class="nav-item"><a href="#how" class="nav-link">Cara Sewa</a></li>
            <li class="nav-item"><a href="#contact" class="nav-link">Kontak</a></li>
            </ul>
        </nav>
        </div>
    </header>

    <main>
        <?php if (isset($_SESSION['message'])): ?>
        <section class="section" style="padding-top: 20px; padding-bottom: 0;">
        <div class="container">
            <div class="message-alert <?php echo $_SESSION['message_type'] ?? 'success'; ?>">
            <?php echo htmlspecialchars($_SESSION['message']); ?>
            </div>
        </div>
        </section>
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
        <?php endif; ?>

        <section id="about" class="section">
        <div class="container">
            <h2 class="section-title">Tentang Kami</h2>
            <p style="font-size: 1.1em; max-width: 800px; margin: 0 auto 20px auto;">
                Selamat datang! Kami adalah solusi satu atap Anda untuk semua kebutuhan perlengkapan petualangan. 
                Kami hanya menyediakan alat berkualitas yang terawat baik untuk memastikan keamanan dan kenyamanan Anda.
            </p>
            <p style="max-width: 800px; margin: 0 auto;">
                Misi kami adalah membuat petualangan Anda lebih mudah dan terjangkau. 
                Fokus saja pada pendakian Anda, biarkan kami yang siapkan peralatannya.
            </p>
        </div>
        </section>

        <section id="services" class="section">
        <div class="container">
            <h2 class="section-title">Alat Terbaru Kami</h2>
            
            <?php if (isset($error_db)): ?>
                <p style="text-align: center; color: red;"><?php echo $error_db; ?></p>
            <?php elseif (empty($items)): ?>
                <p style="text-align: center;">Belum ada alat yang tersedia untuk disewa.</p>
            <?php else: ?>
                <div class="product-card-grid">
                    <?php foreach ($items as $item): ?>
                    <a href="detail.php?id=<?php echo $item['id']; ?>" class="card product-card service-card">
                        <?php 
                        $image_path = UPLOAD_DIR . htmlspecialchars($item['image'] ?? '');
                        if (!empty($item['image']) && file_exists($image_path)): 
                        ?>
                            <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="product-card-img">
                        <?php else: ?>
                            <div class="product-card-img-placeholder">
                                <span>Tidak Ada Gambar</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="product-card-body">
                            <span class="product-card-category"><?php echo htmlspecialchars($item['nama_kategori'] ?? 'N/A'); ?></span>
                            <h3 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="card-description">
                                <?php 
                                // Batas karakter diperpanjang menjadi 110
                                $desc = htmlspecialchars($item['description'] ?? '');
                                if (strlen($desc) > 110) {
                                    echo substr($desc, 0, 110) . '...';
                                } else {
                                    echo $desc ?: 'Klik untuk detail...';
                                }
                                ?>
                            </p>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        </section>

        <section id="how" class="section">
        <div class="container">
            <h2 class="section-title">Cara Sewa</h2>
            <ol class="how-list">
            <li class="how-item"><b>1. Cari & Pilih Alat:</b> Telusuri katalog dan pilih alat yang Anda butuhkan.</li>
            <li class="how-item"><b>2. Tentukan Tanggal:</b> Masuk ke akun Anda, lalu tentukan tanggal sewa.</li>
            <li class="how-item"><b>3. Konfirmasi & Ambil:</b> Tunggu konfirmasi admin, lalu ambil barang di basecamp.</li>
            </ol>
        </div>
        </section>

        <section id="contact" class="section">
        <div class="container">
            <h2 class="section-title">Kontak Kami</h2>
            <p style="font-size: 1.1em; margin-bottom: 25px;">
                Ada pertanyaan? Tim kami siap membantu merencanakan kebutuhan petualangan Anda.
            </p>
            <address class="contact-address" style="font-style: normal; line-height: 1.8;">
                <p><b>Email:</b> sewaalatoutdoorcamping@gmail.com</p>
                <p><b>Telp/WA:</b> 0821-4869-9022</p>
                <p id="contact-status"></p>
            </address>
        </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Sewa Alat Outdoor. Semua Hak Dilindungi.</p>
        </div>
    </footer>

    <script src="../public/js/script.js"></script>
</body>
</html>
<?php
session_start();

// Jika belum login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Sewa</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <h1 class="logo">Dashboard Admin</h1>
            <nav class="navigation">
                <ul class="nav-list">
                    <li class="nav-item"><a href="logout.php" class="nav-link"><b>Logout</b></a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2 class="section-title">Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                <p style="text-align: center;">
                    Anda berada di panel administrator. Dari sini, Anda dapat mengelola inventaris alat, memverifikasi pesanan sewa, dan melihat riwayat transaksi.
                </p>
                
                <p style="text-align: center; margin-top: 15px;">
                    <a href="dashboard.php?page=products">Lihat Produk</a>
                </p>
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Sistem Sewa Alat Outdoor & Camping</p>
        </div>
    </footer>
</body>
</html>
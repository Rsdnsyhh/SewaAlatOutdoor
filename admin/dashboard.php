<?php
session_start();
require '../config/config.php';

// Keamanan: Hanya admin yang boleh akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Menentukan halaman yang akan dimuat
$page = $_GET['page'] ?? 'dashboard_home';

// Mapping halaman ke file yang sebenarnya (dengan path lengkap)
$page_files = [
    'dashboard_home' => 'dashboard_home.php',
    'items_list' => 'items/items_list.php',
    'items_create' => 'items/items_create.php',
    'items_edit' => 'items/items_edit.php',
    'items_detail' => 'items/items_detail.php',
    'rentals_list' => 'rentals/rentals_list.php'
];

// Validasi halaman
if (!isset($page_files[$page])) {
    $page = 'dashboard_home';
}

$page_file = $page_files[$page];

// DEBUG: Cek apakah file ada
$debug_msg = '';
if (!file_exists($page_file)) {
    $debug_msg = "File tidak ditemukan: " . $page_file . " (Lokasi: " . __DIR__ . ")";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Sistem Sewa</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <h1 class="logo">Dashboard Admin</h1>

            <div class="header-controls">

                <div class="user-menu" id="userMenu">
                    <button class="user-menu-button" id="userMenuButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="arrow" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/>
                        </svg>
                    </button>
                    <div class="user-menu-dropdown" id="userMenuDropdown">
                        <a href="../user/catalog.php" class="dropdown-link">Lihat Situs</a>
                        <a href="../auth/logout.php" class="dropdown-link logout">Logout</a>
                    </div>
                </div>

                <button class="dark-mode-toggle" id="darkModeToggle">Dark Mode</button>
            </div>
            <nav class="navigation">
                <ul class="nav-list">
                    <li class="nav-item"><a href="dashboard.php?page=dashboard_home" class="nav-link">Dashboard</a></li>
                    <li class="nav-item"><a href="dashboard.php?page=rentals_list" class="nav-link">Kelola Pesanan</a></li>
                    <li class="nav-item"><a href="dashboard.php?page=items_list" class="nav-link">Kelola Alat</a></li>
                    <li class="nav-item"><a href="dashboard.php?page=items_create" class="nav-link">Tambah Alat</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">

                <?php
                // Tampilkan pesan sukses/gagal
                if (isset($_SESSION['message'])) {
                    echo '<div class="message-alert ' . ($_SESSION['message_type'] ?? 'success') . '">';
                    echo htmlspecialchars($_SESSION['message']);
                    echo '</div>';
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                }

                // DEBUG: Tampilkan info jika file tidak ditemukan
                if ($debug_msg) {
                    echo '<div class="message-alert error"><strong>DEBUG INFO:</strong> ' . $debug_msg . '</div>';
                }

                // Muat konten halaman
                if (file_exists($page_file)) {
                    include $page_file;
                } else {
                    echo '<p style="text-align: center; color: red;"><strong>Error: File konten tidak ditemukan.</strong></p>';
                    echo '<p style="text-align: center; color: red;">File yang dicari: <code>' . htmlspecialchars($page_file) . '</code></p>';
                    echo '<p style="text-align: center; color: red;">Lokasi: <code>' . htmlspecialchars(__DIR__) . '</code></p>';
                }
                ?>

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
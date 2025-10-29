<?php
session_start();
require '../../config/config.php';

// Keamanan: Hanya user yang login yang bisa booking
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    $_SESSION['message'] = 'Anda harus login sebagai user untuk memesan.';
    $_SESSION['message_type'] = 'error';
    header("Location: ../../auth/login.php");
    exit();
}

$item_id = $_GET['item_id'] ?? null;
if (!$item_id || !filter_var($item_id, FILTER_VALIDATE_INT)) {
    header("Location: ../catalog.php");
    exit();
}

// Ambil data item
try {
    $stmt = $pdo->prepare("SELECT title FROM items WHERE id = ?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    if (!$item) {
        header("Location: ../catalog.php");
        exit();
    }
} catch (PDOException $e) {
    die("Gagal mengambil data item: " . $e->getMessage());
}

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking: <?php echo htmlspecialchars($item['title']); ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body>
    <header class="header">
        <div class="container header-container">
            <div class="header-controls">
                <span class="welcome-user">Hai, <b><?php echo htmlspecialchars($_SESSION['username']); ?>!</b></span>
                <a href="../../auth/logout.php" class="login-link-header logout">Logout</a>
                <button class="dark-mode-toggle" id="darkModeToggle">Dark Mode</button>
            </div>
            <h1 class="logo"><a href="../catalog.php" style="color:inherit; text-decoration:none;">Sistem Sewa Alat Outdoor</a></h1>
        </div>
    </header>

    <main>
        <section class="section">
            <div class="container">
                <h2 class="section-title">Formulir Peminjaman</h2>
                
                <form action="../../admin/rentals/booking_action.php" method="POST" class="form-admin" style="max-width: 500px;">
                    <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($item_id); ?>">
                    
                    <div class="form-group-admin">
                        <label>Alat yang disewa:</label>
                        <input type="text" value="<?php echo htmlspecialchars($item['title']); ?>" readonly disabled style="background:#eee;">
                    </div>
                    
                    <div class="form-group-admin">
                        <label for="tanggal_sewa">Tanggal Mulai Sewa</label>
                        <input type="date" id="tanggal_sewa" name="tanggal_sewa" required min="<?php echo $today; ?>">
                    </div>

                    <div class="form-group-admin">
                        <label for="tanggal_kembali">Tanggal Pengembalian</label>
                        <input type="date" id="tanggal_kembali" name="tanggal_kembali" required min="<?php echo $today; ?>">
                    </div>

                    <button type="submit" class="btn-submit">Konfirmasi Peminjaman</button>
                    <a href="../detail.php?id=<?php echo $item_id; ?>" class="btn-submit" style="display:block; text-align:center; text-decoration:none; margin-top:10px; background-color: var(--tertiary-color);">Batal</a>
                </form>
                
            </div>
        </section>
    </main>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 Sistem Sewa Alat Outdoor & Camping</p>
        </div>
    </footer>
    
    <script src="../../public/js/script.js"></script>
    <script>
        // Validasi tanggal sederhana
        const tglSewa = document.getElementById('tanggal_sewa');
        const tglKembali = document.getElementById('tanggal_kembali');

        tglSewa.addEventListener('change', function() {
            tglKembali.min = tglSewa.value;
            if (tglKembali.value < tglSewa.value) {
                tglKembali.value = tglSewa.value;
            }
        });
    </script>
</body>
</html>
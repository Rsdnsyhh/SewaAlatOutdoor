<?php
// File ini di-include dari dashboard.php, jadi $pdo sudah tersedia
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit();
}

try {
    // 1. Total Alat
    $total_items = $pdo->query("SELECT COUNT(*) FROM items")->fetchColumn();
    
    // 2. Total Pesanan (sedang dipinjam)
    $total_rentals = $pdo->query("SELECT COUNT(*) FROM rentals WHERE status = 'dipinjam'")->fetchColumn();
    
    // 3. Total User Terdaftar (role 'user')
    $total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
    
    // 4. Total Kategori
    $total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();

} catch (PDOException $e) {
    echo '<div class="message-alert error">Gagal memuat statistik: ' . $e->getMessage() . '</div>';
    $total_items = $total_rentals = $total_users = $total_categories = 'N/A';
}
?>

<h2 class="section-title">Dashboard</h2>
<p style="text-align: center; margin-top: -20px; margin-bottom: 30px;">Ringkasan status sistem Anda saat ini.</p>

<div class="stat-grid">
    <div class="stat-card">
        <h3><?php echo $total_items; ?></h3>
        <p>Total Alat</p>
        <a href="dashboard.php?page=items_list">Lihat Detail &rarr;</a>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_rentals; ?></h3>
        <p>Pesanan Aktif</p>
        <a href="dashboard.php?page=rentals_list">Lihat Detail &rarr;</a>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_users; ?></h3>
        <p>Total User</p>
        <a href="#">Lihat Detail &rarr;</a>
    </div>
    <div class="stat-card">
        <h3><?php echo $total_categories; ?></h3>
        <p>Total Kategori</p>
        <a href="#">Lihat Detail &rarr;</a>
    </div>
</div>
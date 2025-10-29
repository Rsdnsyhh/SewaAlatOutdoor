<?php
// File ini di-include dari dashboard.php, jadi $pdo sudah tersedia
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit();
}

try {
    $sql = "SELECT rentals.*, users.username, items.title
            FROM rentals 
            LEFT JOIN users ON rentals.user_id = users.id
            LEFT JOIN items ON rentals.item_id = items.id
            ORDER BY rentals.status ASC, rentals.tanggal_sewa ASC";
    $stmt = $pdo->query($sql);
    $rentals = $stmt->fetchAll();
} catch (PDOException $e) {
    $rentals = [];
    echo '<div class="message-alert error">Gagal memuat data pesanan.</div>';
}
?>

<h2 class="section-title">Kelola Pesanan</h2>

<?php if (empty($rentals)): ?>
    <p style="text-align: center; color: #888;">Belum ada pesanan.</p>
<?php else: ?>

<table class="admin-table rentals-table">
    <thead>
        <tr>
            <th>Peminjam</th>
            <th>Alat</th>
            <th>Tgl Sewa</th>
            <th>Tgl Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rentals as $rental): ?>
        <tr>
            <td><?php echo htmlspecialchars($rental['username'] ?? 'User Dihapus'); ?></td>
            <td><?php echo htmlspecialchars($rental['title'] ?? 'Item Dihapus'); ?></td>
            <td><?php echo htmlspecialchars(date('d M Y', strtotime($rental['tanggal_sewa']))); ?></td>
            <td><?php echo htmlspecialchars(date('d M Y', strtotime($rental['tanggal_kembali']))); ?></td>
            <td><span class="status-badge status-<?php echo htmlspecialchars($rental['status']); ?>"><?php echo htmlspecialchars(ucfirst($rental['status'])); ?></span></td>
            <td>
                <div class="action-links">
                    <?php if ($rental['status'] == 'dipinjam'): ?>
                    <form action="rentals/rentals_action.php?action=update_status" method="POST" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                        <button type="submit" class="action-link detail">Selesai</button>
                    </form>
                    <?php endif; ?>
                    
                    <form action="rentals/rentals_action.php?action=delete" method="POST" style="display: inline;" onsubmit="return confirm('Hapus permanen pesanan ini?');">
                        <input type="hidden" name="id" value="<?php echo $rental['id']; ?>">
                        <button type="submit" class="action-link delete">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>
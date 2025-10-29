<?php
// File ini di-include dari dashboard.php, jadi $pdo sudah tersedia
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit();
}

$search = $_GET['search'] ?? '';
$params = [];
$search_query = "";

if (!empty($search)) {
    $search_query = " WHERE items.title LIKE ?";
    $params[] = "%$search%";
}

try {
    $sql = "SELECT 
                items.id, items.title, items.created_at, items.image, 
                categories.nama_kategori 
            FROM items
            LEFT JOIN categories ON items.id_kategori = categories.id_kategori
            " . $search_query . " 
            ORDER BY items.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();
} catch (PDOException $e) {
    $items = [];
    echo '<div class="message-alert error">Gagal memuat data alat.</div>';
}
?>

<h2 class="section-title">Kelola Alat</h2>

<?php if (empty($items)): ?>
    <p style="text-align: center; color: #888;">Belum ada alat yang ditambahkan.</p>
<?php else: ?>

<table class="admin-table items-table">
    <thead>
        <tr>
            <th>Gambar</th>
            <th>Judul Alat</th>
            <th>Kategori</th>
            <th>Tanggal Dibuat</th>
            <th style="text-align: center;">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td>
                <?php if (!empty($item['image'])): ?>
                    <img src="../<?php echo UPLOAD_DIR . htmlspecialchars($item['image']); ?>" alt="Gambar" class="admin-table-img">
                <?php else: ?>
                    <span class="no-image-placeholder">N/A</span>
                <?php endif; ?>
            </td>
            <td><?php echo htmlspecialchars($item['title']); ?></td>
            <td><?php echo htmlspecialchars($item['nama_kategori'] ?? '-'); ?></td>
            <td><?php echo date('d M Y', strtotime($item['created_at'])); ?></td>
            <td style="text-align: center; padding: 0;">
                <div class="action-links" style="justify-content: center; padding: 12px 15px;">
                    <a href="dashboard.php?page=items_detail&id=<?php echo $item['id']; ?>" class="action-link detail">Detail</a>
                    <a href="dashboard.php?page=items_edit&id=<?php echo $item['id']; ?>" class="action-link edit">Edit</a>
                    
                    <form action="items/items_action.php?action=delete" method="POST" onsubmit="return confirm('Hapus item ini?');" style="display: inline;">
                        <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="action-link delete">Hapus</button>
                    </form>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>
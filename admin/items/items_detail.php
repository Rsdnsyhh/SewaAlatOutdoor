<?php
// File ini di-include dari dashboard.php, jadi $pdo sudah tersedia
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: dashboard.php?page=items_list");
    exit();
}

try {
    $sql = "SELECT items.*, categories.nama_kategori 
            FROM items
            LEFT JOIN categories ON items.id_kategori = categories.id_kategori
            WHERE items.id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        header("Location: dashboard.php?page=items_list");
        exit();
    }
} catch (PDOException $e) {
    die("Gagal mengambil data item: " . $e->getMessage());
}
?>

<h2 class="section-title">Detail Alat</h2>
<div class="item-detail-container">
    <div class="item-detail-image-container">
        <?php 
        $image_path_relative = UPLOAD_DIR . htmlspecialchars($item['image'] ?? '');
        if (!empty($item['image']) && file_exists('../' . $image_path_relative)): 
        ?>
            <img src="../<?php echo $image_path_relative; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-detail-image">
        <?php else: ?>
            <div class="item-detail-image-placeholder"><span>Tidak Ada Gambar</span></div>
        <?php endif; ?>
    </div>
    
    <div class="item-detail-info">
        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
        <p><strong>Kategori:</strong> <?php echo htmlspecialchars($item['nama_kategori'] ?? '-'); ?></p>
        <p><strong>Harga per Hari:</strong> Rp <?php echo number_format($item['price_per_day'] ?? 0, 0, ',', '.'); ?></p>
        <p><strong>Stok:</strong> <?php echo $item['stock'] ?? 0; ?> unit</p>
        
        <hr class="detail-divider">
        
        <p><strong>Deskripsi:</strong></p>
        <div class="item-description">
            <?php echo nl2br(htmlspecialchars($item['description'] ?: 'Tidak ada deskripsi.')); ?>
        </div>
    </div>
</div>

<a href="dashboard.php?page=items_list" class="btn-submit" style="display:inline-block; margin-top: 20px;">&larr; Kembali ke Daftar</a>
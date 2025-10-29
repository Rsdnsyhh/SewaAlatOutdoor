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
    $stmt = $pdo->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->execute([$id]);
    $item = $stmt->fetch();

    if (!$item) {
        header("Location: dashboard.php?page=items_list");
        exit();
    }
    
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori ASC");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    die("Gagal mengambil data item: " . $e->getMessage());
}
?>

<h2 class="section-title">Edit Alat: <?php echo htmlspecialchars($item['title']); ?></h2>

<form action="items/items_action.php?action=update" method="POST" class="form-admin" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($item['image'] ?? ''); ?>">

    <div class="form-group-admin">
        <label for="title">Judul Alat (Wajib)</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>
    </div>

    <div class="form-group-admin">
        <label for="id_kategori">Kategori (Wajib)</label>
        <select id="id_kategori" name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories as $kategori): ?>
                <option value="<?php echo htmlspecialchars($kategori['id_kategori']); ?>" 
                    <?php echo ($item['id_kategori'] == $kategori['id_kategori']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group-admin">
        <label>Gambar Saat Ini:</label>
        <?php 
        $image_path_relative = UPLOAD_DIR . htmlspecialchars($item['image'] ?? '');
        if (!empty($item['image']) && file_exists('../' . $image_path_relative)): 
        ?>
            <img src="../<?php echo $image_path_relative; ?>" alt="Gambar saat ini" class="admin-form-preview">
        <?php else: ?>
            <p>Tidak ada gambar.</p>
        <?php endif; ?>
    </div>
    
    <div class="form-group-admin">
        <label for="image">Ganti Gambar Alat</label>
        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif, image/webp">
        <small>Biarkan kosong jika tidak ingin mengganti gambar.</small>
    </div>

    <div class="form-group-admin">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars($item['description'] ?? ''); ?></textarea>
    </div>

    <div class="form-group-admin">
        <label for="price_per_day">Harga per Hari (Wajib)</label>
        <input type="number" id="price_per_day" name="price_per_day" value="<?php echo $item['price_per_day']; ?>" required min="0">
    </div>

    <div class="form-group-admin">
        <label for="stock">Stok (Wajib)</label>
        <input type="number" id="stock" name="stock" value="<?php echo $item['stock']; ?>" required min="0">
    </div>

    <button type="submit" class="btn-submit">Update Alat</button>
    <a href="dashboard.php?page=items_list" class="btn-submit" style="display:inline-block; text-align:center; text-decoration:none; margin-top:10px; background-color: var(--tertiary-color);">Batal</a>
</form>
<?php
// File ini di-include dari dashboard.php, jadi $pdo sudah tersedia
if (!isset($pdo)) {
    header("Location: dashboard.php");
    exit();
}

try {
    $cat_stmt = $pdo->query("SELECT * FROM categories ORDER BY nama_kategori ASC");
    $categories = $cat_stmt->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    echo '<div class="message-alert error">Gagal memuat kategori.</div>';
}
?>

<h2 class="section-title">Tambah Alat Baru</h2>

<form action="items/items_action.php?action=create" method="POST" class="form-admin" enctype="multipart/form-data">
    <div class="form-group-admin">
        <label for="title">Judul Alat (Wajib)</label>
        <input type="text" id="title" name="title" required>
    </div>
    
    <div class="form-group-admin">
        <label for="id_kategori">Kategori (Wajib)</label>
        <select id="id_kategori" name="id_kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($categories as $kategori): ?>
                <option value="<?php echo htmlspecialchars($kategori['id_kategori']); ?>">
                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group-admin">
        <label for="image">Gambar Alat</label>
        <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/gif, image/webp">
        <small>Ukuran file maks 2MB.</small>
    </div>

    <div class="form-group-admin">
        <label for="description">Deskripsi</label>
        <textarea id="description" name="description" rows="5"></textarea>
    </div>

    <div class="form-group-admin">
        <label for="price_per_day">Harga per Hari (Wajib)</label>
        <input type="number" id="price_per_day" name="price_per_day" required min="0">
    </div>

    <div class="form-group-admin">
        <label for="stock">Stok (Wajib)</label>
        <input type="number" id="stock" name="stock" required min="0">
    </div>

    <button type="submit" class="btn-submit">Simpan Alat</button>
</form>
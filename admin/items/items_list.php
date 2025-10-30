<?php
// File: admin/items/items_list.php
// File ini di-include dari dashboard.php

if (!isset($pdo)) {
    header("Location: ../dashboard.php");
    exit();
}

// Ambil data items dengan JOIN ke categories (sesuai struktur database Anda)
try {
    $stmt = $pdo->query("
        SELECT 
            i.id,
            i.title,
            i.description,
            i.image,
            i.id_kategori,
            i.created_at,
            COALESCE(c.nama_kategori, 'Tidak Ada Kategori') as category_name
        FROM items i 
        LEFT JOIN categories c ON i.id_kategori = c.id_kategori 
        ORDER BY i.created_at DESC
    ");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="message-alert error">Gagal memuat data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $items = [];
}
?>

<h2 class="section-title">Kelola Alat</h2>

<div class="table-wrapper">
    <table class="admin-table items-table">
        <thead>
            <tr>
                <th>Gambar</th>
                <th>Judul Alat</th>
                <th>Kategori</th>
                <th>Tanggal Dibuat</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($items)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 40px;">
                        <p style="margin-bottom: 15px; font-size: 1.1em;">Belum ada alat yang ditambahkan.</p>
                        <a href="dashboard.php?page=items_create" 
                            class="action-link detail" 
                            style="display: inline-block; text-decoration: none;">
                            Tambah Alat Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php 
                                // Perbaikan: Cek NULL sebelum htmlspecialchars
                                $image_name = $item['image'] ?? '';
                                $image_path = '../public/images/products/' . $image_name;
                                
                                if (!empty($image_name) && file_exists($image_path)): 
                                ?>
                                    <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                        alt="<?php echo htmlspecialchars($item['title'] ?? 'Produk'); ?>" 
                                        class="admin-table-img">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/80x80/e0e0e0/999999?text=No+Image" 
                                        alt="No Image" 
                                        class="admin-table-img">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($item['title'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($item['category_name'] ?? 'Tidak Ada'); ?></td>
                            <td><?php echo !empty($item['created_at']) ? date('d M Y', strtotime($item['created_at'])) : '-'; ?></td>
                            <td>
                                <div class="action-links">
                                    <a href="dashboard.php?page=items_detail&id=<?php echo intval($item['id']); ?>" 
                                    class="action-link detail">Detail</a>
                                    <a href="dashboard.php?page=items_edit&id=<?php echo intval($item['id']); ?>" 
                                    class="action-link edit">Edit</a>
                                    <a href="items/items_action.php?action=delete&id=<?php echo intval($item['id']); ?>" 
                                    class="action-link delete" 
                                    onclick="return confirm('Yakin ingin menghapus alat ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php

if (!isset($pdo)) {
    header("Location: ../dashboard.php");
    exit();
}

try {
    // Ambil semua kategori
    $stmt = $pdo->query("
        SELECT 
            id_kategori, 
            nama_kategori 
        FROM categories 
        ORDER BY nama_kategori ASC
    ");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    echo '<div class="message-alert error">Gagal memuat data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $categories = [];
}
?>

<h2 class="section-title">Kelola Kategori</h2>
<p style="text-align: center; margin-top: -10px; margin-bottom: 20px; color: #666;">
    Total: <strong><?php echo count($categories); ?></strong> kategori tersedia
</p>

<div class="table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 10%;">No</th>
                <th style="width: 40%;">Nama Kategori</th>
                <th style="width: 25%;">Jumlah Alat</th>
                <th style="width: 25%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 40px;">
                        <p style="font-size: 1.1em; color: #999;">Belum ada kategori.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($categories as $cat): ?>
                    <?php
                    // Hitung jumlah alat per kategori
                    try {
                        $count = $pdo->prepare("SELECT COUNT(*) FROM items WHERE id_kategori = ?");
                        $count->execute([$cat['id_kategori']]);
                        $total = $count->fetchColumn();
                    } catch (PDOException $e) {
                        $total = 0;
                    }
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td>
                            <strong style="color: var(--secondary-color);">
                                <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                            </strong>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($total > 0): ?>
                                <a href="dashboard.php?page=items_list&kategori=<?php echo $cat['id_kategori']; ?>" 
                                    style="color: var(--primary-color); text-decoration: none; font-weight: 600;">
                                    <?php echo $total; ?> alat
                                </a>
                            <?php else: ?>
                                <span style="color: #999;">0 alat</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if ($total > 0): ?>
                                <span class="status-badge status-dikembalikan">Aktif</span>
                            <?php else: ?>
                                <span class="status-badge status-pending">Kosong</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div style="margin-top: 30px; padding: 20px; background-color: #f9f9f9; border-radius: 8px; border-left: 4px solid var(--secondary-color);">
    <h3 style="margin-top: 0; color: var(--primary-color);">📊 Ringkasan Kategori</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 15px;">
        <?php foreach ($categories as $cat): ?>
            <?php
            $count = $pdo->prepare("SELECT COUNT(*) FROM items WHERE id_kategori = ?");
            $count->execute([$cat['id_kategori']]);
            $total = $count->fetchColumn();
            ?>
            <div style="padding: 15px; background: white; border-radius: 8px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                <div style="font-size: 2em; font-weight: bold; color: var(--secondary-color);">
                    <?php echo $total; ?>
                </div>
                <div style="font-size: 0.9em; color: #666; margin-top: 5px;">
                    <?php echo htmlspecialchars($cat['nama_kategori']); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.dark-mode div[style*="background-color: #f9f9f9"] {
    background-color: var(--dm-card-bg) !important;
}
.dark-mode div[style*="background: white"] {
    background: var(--dm-card-bg) !important;
    border: 1px solid #4a5568;
}
</style>
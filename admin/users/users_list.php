<?php
// File: admin/users/users_list.php
// File ini di-include dari dashboard.php

if (!isset($pdo)) {
    header("Location: ../dashboard.php");
    exit();
}

try {
    // Ambil semua user dengan role 'user'
    $stmt = $pdo->query("
        SELECT id, username, role, created_at 
        FROM users 
        WHERE role = 'user'
        ORDER BY created_at DESC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hitung statistik
    $total_users = count($users);
    $stmt_rentals = $pdo->query("
        SELECT user_id, COUNT(*) as total_rentals 
        FROM rentals 
        GROUP BY user_id
    ");
    $rentals_count = $stmt_rentals->fetchAll(PDO::FETCH_KEY_PAIR);
    
} catch (PDOException $e) {
    echo '<div class="message-alert error">Gagal memuat data: ' . htmlspecialchars($e->getMessage()) . '</div>';
    $users = [];
    $rentals_count = [];
}
?>

<h2 class="section-title">Kelola User</h2>
<p style="text-align: center; margin-top: -10px; margin-bottom: 20px; color: #666;">
    Total: <strong><?php echo $total_users; ?></strong> user terdaftar
</p>

<div class="table-wrapper">
    <table class="admin-table">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 25%;">Username</th>
                <th style="width: 15%;">Role</th>
                <th style="width: 18%;">Tanggal Daftar</th>
                <th style="width: 17%;">Total Pesanan</th>
                <th style="width: 17%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <p style="font-size: 1.1em; color: #999;">Belum ada user yang terdaftar.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($users as $user): ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $user['role']; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                        <td style="text-align: center;">
                            <?php 
                            $rental_count = $rentals_count[$user['id']] ?? 0;
                            echo $rental_count . ' pesanan';
                            ?>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="dashboard.php?page=user_detail&id=<?php echo $user['id']; ?>" 
                                    class="action-link detail">Detail</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<style>
.status-badge.status-user {
    background-color: #3498db;
    color: white;
}
.status-badge.status-admin {
    background-color: #e74c3c;
    color: white;
}
</style>
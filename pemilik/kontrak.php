<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Kelola Kontrak';
$userId = Auth::id();

$kontrakData = Database::fetchAll("
    SELECT kt.*, u.nama_lengkap, k.nomor_kamar, ks.nama_kost
    FROM kontrak kt
    JOIN penyewa p ON kt.penyewa_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN kamar k ON kt.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId
    ORDER BY kt.created_at DESC
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-contract"></i> Daftar Kontrak
    </div>
    <div class="card-body">
        <?php if (empty($kontrakData)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada kontrak.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Penyewa</th>
                            <th>Kost</th>
                            <th>Kamar</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th>Durasi</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kontrakData as $k): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($k['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($k['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($k['nomor_kamar']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($k['tanggal_mulai'])); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($k['tanggal_berakhir'])); ?></td>
                                <td><?php echo $k['durasi_bulan']; ?> bulan</td>
                                <td>Rp <?php echo number_format($k['total_harga'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge badge-status badge-<?php echo strtolower($k['status']); ?>">
                                        <?php echo $k['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

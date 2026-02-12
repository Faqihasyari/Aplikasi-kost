<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Riwayat Penyewa';
$userId = Auth::id();

$riwayatPenyewa = Database::fetchAll("
    SELECT p.*, u.nama_lengkap, u.no_hp, k.nomor_kamar, ks.nama_kost
    FROM penyewa p
    JOIN users u ON p.user_id = u.id
    JOIN kamar k ON p.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId AND p.status = 'Selesai'
    ORDER BY p.tanggal_keluar DESC
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Riwayat Penyewa (Selesai)
    </div>
    <div class="card-body">
        <?php if (empty($riwayatPenyewa)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada riwayat penyewa.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No. HP</th>
                            <th>Kost</th>
                            <th>Kamar</th>
                            <th>Tanggal Masuk</th>
                            <th>Tanggal Keluar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayatPenyewa as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($p['nomor_kamar']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['tanggal_masuk'])); ?></td>
                                <td><?php echo $p['tanggal_keluar'] ? date('d/m/Y', strtotime($p['tanggal_keluar'])) : '-'; ?></td>
                                <td><span class="badge badge-selesai">Selesai</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

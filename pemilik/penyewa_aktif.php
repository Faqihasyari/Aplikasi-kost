<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Penyewa Aktif';
$userId = Auth::id();

$penyewaAktif = Database::fetchAll("
    SELECT p.*, u.nama_lengkap, u.no_hp, k.nomor_kamar, ks.nama_kost,
           kt.tanggal_mulai, kt.tanggal_berakhir
    FROM penyewa p
    JOIN users u ON p.user_id = u.id
    JOIN kamar k ON p.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    LEFT JOIN kontrak kt ON kt.penyewa_id = p.id AND kt.status = 'Aktif'
    WHERE ks.pemilik_id = $userId AND p.status = 'Aktif'
    ORDER BY p.tanggal_masuk DESC
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-user-check"></i> Daftar Penyewa Aktif
    </div>
    <div class="card-body">
        <?php if (empty($penyewaAktif)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada penyewa aktif.
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
                            <th>Kontrak Berakhir</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($penyewaAktif as $p): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($p['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($p['no_hp']); ?></td>
                                <td><?php echo htmlspecialchars($p['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($p['nomor_kamar']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['tanggal_masuk'])); ?></td>
                                <td><?php echo $p['tanggal_berakhir'] ? date('d/m/Y', strtotime($p['tanggal_berakhir'])) : '-'; ?></td>
                                <td><span class="badge badge-aktif">Aktif</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

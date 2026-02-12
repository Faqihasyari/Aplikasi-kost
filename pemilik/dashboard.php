<?php
/**
 * Pemilik Dashboard
 * File: pemilik/dashboard.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');

$pageTitle = 'Dashboard Pemilik';
$userId = Auth::id();

// Statistik Pemilik
$totalKost = Database::fetchOne("SELECT COUNT(*) as total FROM kost WHERE pemilik_id = $userId")['total'];

$totalKamar = Database::fetchOne("
    SELECT COUNT(*) as total FROM kamar k
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId
")['total'];

$kamarKosong = Database::fetchOne("
    SELECT COUNT(*) as total FROM kamar k
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId AND k.status = 'Kosong'
")['total'];

$penyewaAktif = Database::fetchOne("
    SELECT COUNT(*) as total FROM penyewa p
    JOIN kamar km ON p.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE ks.pemilik_id = $userId AND p.status = 'Aktif'
")['total'];

$tunggakan = Database::fetchOne("
    SELECT COUNT(*) as total FROM pembayaran pb
    JOIN kontrak k ON pb.kontrak_id = k.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE ks.pemilik_id = $userId AND pb.status IN ('Belum Bayar', 'Terlambat')
")['total'];

$pendapatan = Database::fetchOne("
    SELECT SUM(pb.jumlah) as total FROM pembayaran pb
    JOIN kontrak k ON pb.kontrak_id = k.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE ks.pemilik_id = $userId AND pb.status = 'Lunas'
")['total'] ?? 0;

// Data Kost
$dataKost = Database::fetchAll("
    SELECT k.*,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id) as total_kamar,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Kosong') as kamar_kosong,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Terisi') as kamar_terisi
    FROM kost k
    WHERE k.pemilik_id = $userId
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<!-- Statistik Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Kost Saya</div>
                        <div class="stats-value"><?php echo $totalKost; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building stats-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Kamar Kosong</div>
                        <div class="stats-value"><?php echo $kamarKosong; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-door-open stats-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Penyewa Aktif</div>
                        <div class="stats-value"><?php echo $penyewaAktif; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check stats-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card danger">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Tunggakan</div>
                        <div class="stats-value"><?php echo $tunggakan; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle stats-icon text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pendapatan -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave"></i> Pendapatan Saya
            </div>
            <div class="card-body text-center">
                <h2 class="text-success">Rp <?php echo number_format($pendapatan, 0, ',', '.'); ?></h2>
                <p class="text-muted">Total pendapatan dari seluruh pembayaran lunas</p>
            </div>
        </div>
    </div>
</div>

<!-- Data Kost -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-building"></i> Daftar Kost Saya
    </div>
    <div class="card-body">
        <?php if (empty($dataKost)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada kost. 
                <a href="/coba_kost/pemilik/kost.php" class="alert-link">Tambah kost sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kost</th>
                            <th>Alamat</th>
                            <th>Total Kamar</th>
                            <th>Terisi</th>
                            <th>Kosong</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKost as $kost): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kost['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($kost['alamat']); ?></td>
                                <td><?php echo $kost['total_kamar']; ?></td>
                                <td><span class="badge bg-warning"><?php echo $kost['kamar_terisi']; ?></span></td>
                                <td><span class="badge bg-info"><?php echo $kost['kamar_kosong']; ?></span></td>
                                <td>
                                    <a href="/coba_kost/pemilik/kamar.php?kost_id=<?php echo $kost['id']; ?>" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-door-open"></i> Kelola Kamar
                                    </a>
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

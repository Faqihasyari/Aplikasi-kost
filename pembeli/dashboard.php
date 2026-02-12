<?php
/**
 * Pembeli Dashboard
 * File: pembeli/dashboard.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pembeli');

$pageTitle = 'Dashboard Pembeli';
$userId = Auth::id();

// Cek penyewa aktif
$penyewaAktif = Database::fetchOne("
    SELECT p.*, k.nomor_kamar, ks.nama_kost
    FROM penyewa p
    JOIN kamar k ON p.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE p.user_id = $userId AND p.status = 'Aktif'
");

// Jika ada penyewa aktif, ambil data kontrak
$kontrakAktif = null;
$sisaHari = null;
$pembayaranTerdekat = null;

if ($penyewaAktif) {
    $kontrakAktif = Database::fetchOne("
        SELECT * FROM kontrak 
        WHERE penyewa_id = {$penyewaAktif['id']} AND status = 'Aktif'
    ");
    
    if ($kontrakAktif) {
        $tanggalBerakhir = new DateTime($kontrakAktif['tanggal_berakhir']);
        $sekarang = new DateTime();
        $interval = $sekarang->diff($tanggalBerakhir);
        $sisaHari = $interval->days;
        
        // Cek pembayaran yang belum lunas
        $pembayaranTerdekat = Database::fetchOne("
            SELECT * FROM pembayaran 
            WHERE kontrak_id = {$kontrakAktif['id']} 
            AND status IN ('Belum Bayar', 'Terlambat')
            ORDER BY bulan_ke ASC
            LIMIT 1
        ");
    }
}

// Statistik
$totalKontrak = Database::fetchOne("
    SELECT COUNT(*) as total FROM kontrak kt
    JOIN penyewa p ON kt.penyewa_id = p.id
    WHERE p.user_id = $userId
")['total'];

$totalPembayaran = Database::fetchOne("
    SELECT COUNT(*) as total FROM pembayaran pb
    JOIN kontrak kt ON pb.kontrak_id = kt.id
    JOIN penyewa p ON kt.penyewa_id = p.id
    WHERE p.user_id = $userId AND pb.status = 'Lunas'
")['total'];

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<!-- Status Kontrak Aktif -->
<?php if ($penyewaAktif && $kontrakAktif): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card" style="border-left: 4px solid #1cc88a;">
                <div class="card-body">
                    <h5 class="card-title text-success">
                        <i class="fas fa-check-circle"></i> Kontrak Aktif
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Kost:</strong> <?php echo htmlspecialchars($penyewaAktif['nama_kost']); ?></p>
                            <p class="mb-1"><strong>Kamar:</strong> <?php echo htmlspecialchars($penyewaAktif['nomor_kamar']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Tanggal Masuk:</strong> <?php echo date('d/m/Y', strtotime($penyewaAktif['tanggal_masuk'])); ?></p>
                            <p class="mb-1"><strong>Berakhir:</strong> <?php echo date('d/m/Y', strtotime($kontrakAktif['tanggal_berakhir'])); ?></p>
                            <p class="mb-1"><strong>Sisa Masa Sewa:</strong> <span class="badge bg-warning"><?php echo $sisaHari; ?> hari</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($pembayaranTerdekat): ?>
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Perhatian!</strong> Anda memiliki pembayaran yang belum lunas untuk periode <strong><?php echo $pembayaranTerdekat['periode_bulan']; ?></strong>.
            <a href="/coba_kost/pembeli/pembayaran.php" class="alert-link">Bayar Sekarang</a>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> Anda belum memiliki kontrak aktif. 
        <a href="/coba_kost/pembeli/kost.php" class="alert-link">Cari kost sekarang</a>
    </div>
<?php endif; ?>

<!-- Statistik -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stats-card primary">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Total Kontrak</div>
                        <div class="stats-value"><?php echo $totalKontrak; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-contract stats-icon text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Pembayaran Lunas</div>
                        <div class="stats-value"><?php echo $totalPembayaran; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle stats-icon text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card stats-card info">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Status</div>
                        <div class="stats-value" style="font-size: 1.2rem;">
                            <?php echo $penyewaAktif ? 'Penyewa Aktif' : 'Belum Ngekost'; ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check stats-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-bolt"></i> Aksi Cepat
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="/coba_kost/pembeli/kost.php" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Cari Kost
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="/coba_kost/pembeli/kontrak.php" class="btn btn-info w-100">
                    <i class="fas fa-file-contract"></i> Lihat Kontrak
                </a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="/coba_kost/pembeli/pembayaran.php" class="btn btn-success w-100">
                    <i class="fas fa-money-bill-wave"></i> Pembayaran
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

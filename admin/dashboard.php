<?php
/**
 * Admin Dashboard
 * File: admin/dashboard.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('admin');

$pageTitle = 'Dashboard Admin';

// Statistik
$totalKost = Database::fetchOne("SELECT COUNT(*) as total FROM kost")['total'];
$totalPemilik = Database::fetchOne("SELECT COUNT(*) as total FROM users WHERE role = 'pemilik'")['total'];
$totalPembeli = Database::fetchOne("SELECT COUNT(*) as total FROM users WHERE role = 'pembeli'")['total'];
$totalKontrakAktif = Database::fetchOne("SELECT COUNT(*) as total FROM kontrak WHERE status = 'Aktif'")['total'];
$totalPendapatan = Database::fetchOne("SELECT SUM(jumlah) as total FROM pembayaran WHERE status = 'Lunas'")['total'] ?? 0;

// Data recent
$recentKontrak = Database::fetchAll("
    SELECT k.*, u.nama_lengkap, km.nomor_kamar, ks.nama_kost
    FROM kontrak k
    JOIN penyewa p ON k.penyewa_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    ORDER BY k.created_at DESC
    LIMIT 5
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
                        <div class="stats-title">Total Kost</div>
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
        <div class="card stats-card success">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Total Pemilik</div>
                        <div class="stats-value"><?php echo $totalPemilik; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie stats-icon text-success"></i>
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
                        <div class="stats-title">Total Pembeli</div>
                        <div class="stats-value"><?php echo $totalPembeli; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users stats-icon text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stats-card warning">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="stats-title">Kontrak Aktif</div>
                        <div class="stats-value"><?php echo $totalKontrakAktif; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-contract stats-icon text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave"></i> Total Pendapatan
            </div>
            <div class="card-body text-center">
                <h2 class="text-success">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Statistik Sistem
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Kamar:</span>
                    <strong><?php echo Database::fetchOne("SELECT COUNT(*) as total FROM kamar")['total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Kamar Terisi:</span>
                    <strong><?php echo Database::fetchOne("SELECT COUNT(*) as total FROM kamar WHERE status = 'Terisi'")['total']; ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Kamar Kosong:</span>
                    <strong><?php echo Database::fetchOne("SELECT COUNT(*) as total FROM kamar WHERE status = 'Kosong'")['total']; ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Contracts -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list"></i> Kontrak Terbaru
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Penyewa</th>
                                <th>Kost</th>
                                <th>Kamar</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Berakhir</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recentKontrak)): ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada kontrak</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recentKontrak as $kontrak): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($kontrak['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($kontrak['nama_kost']); ?></td>
                                        <td><?php echo htmlspecialchars($kontrak['nomor_kamar']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($kontrak['tanggal_mulai'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($kontrak['tanggal_berakhir'])); ?></td>
                                        <td>
                                            <span class="badge badge-status badge-<?php echo strtolower($kontrak['status']); ?>">
                                                <?php echo $kontrak['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

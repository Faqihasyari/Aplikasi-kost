<?php
/**
 * Admin - Laporan
 * File: admin/laporan.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('admin');

$pageTitle = 'Laporan';

// Laporan Kost
$laporanKost = Database::fetchAll("
    SELECT k.*, u.nama_lengkap as pemilik,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id) as total_kamar,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Terisi') as kamar_terisi,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Kosong') as kamar_kosong
    FROM kost k
    JOIN users u ON k.pemilik_id = u.id
    ORDER BY k.created_at DESC
");

// Laporan Pembayaran
// Pembayaran LUNAS
$laporanLunas = Database::fetchAll("
    SELECT p.*, k.tanggal_mulai, k.tanggal_berakhir,
           u.nama_lengkap as penyewa,
           km.nomor_kamar,
           ks.nama_kost
    FROM pembayaran p
    JOIN kontrak k ON p.kontrak_id = k.id
    JOIN penyewa py ON k.penyewa_id = py.id
    JOIN users u ON py.user_id = u.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE p.status = 'Lunas'
    ORDER BY p.created_at DESC
    LIMIT 50
");

// Pembayaran BELUM LUNAS / TUNGGAKAN
$laporanBelumLunas = Database::fetchAll("
    SELECT p.*, k.tanggal_mulai, k.tanggal_berakhir,
           u.nama_lengkap as penyewa,
           km.nomor_kamar,
           ks.nama_kost
    FROM pembayaran p
    JOIN kontrak k ON p.kontrak_id = k.id
    JOIN penyewa py ON k.penyewa_id = py.id
    JOIN users u ON py.user_id = u.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE p.status IN ('Belum Bayar','Terlambat','Menunggu')
    ORDER BY p.created_at DESC
    LIMIT 50
");


// Summary
$totalPendapatan = Database::fetchOne("SELECT SUM(jumlah) as total FROM pembayaran WHERE status = 'Lunas'")['total'] ?? 0;
$totalTunggakan = Database::fetchOne("SELECT SUM(jumlah) as total FROM pembayaran WHERE status IN ('Belum Bayar', 'Terlambat')")['total'] ?? 0;

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card stats-card success">
            <div class="card-body">
                <h6 class="stats-title">Total Pendapatan (Lunas)</h6>
                <h3 class="stats-value text-success">Rp <?php echo number_format($totalPendapatan, 0, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card stats-card danger">
            <div class="card-body">
                <h6 class="stats-title">Total Tunggakan</h6>
                <h3 class="stats-value text-danger">Rp <?php echo number_format($totalTunggakan, 0, ',', '.'); ?></h3>
            </div>
        </div>
    </div>
</div>

<a href="/coba_kost/admin/export_laporan_pdf.php"
   class="btn btn-danger mb-3">
    <i class="fas fa-file-pdf"></i> Export PDF
</a>


<!-- Laporan Kost -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-building"></i> Laporan Data Kost
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kost</th>
                        <th>Pemilik</th>
                        <th>Alamat</th>
                        <th>Total Kamar</th>
                        <th>Terisi</th>
                        <th>Kosong</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporanKost as $kost): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($kost['nama_kost']); ?></td>
                            <td><?php echo htmlspecialchars($kost['pemilik']); ?></td>
                            <td><?php echo htmlspecialchars($kost['alamat']); ?></td>
                            <td><?php echo $kost['total_kamar']; ?></td>
                            <td><span class="badge bg-warning"><?php echo $kost['kamar_terisi']; ?></span></td>
                            <td><span class="badge bg-info"><?php echo $kost['kamar_kosong']; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Laporan Pembayaran -->
<div class="card mb-4">
    <div class="card-header bg-danger text-white">
        <i class="fas fa-exclamation-circle"></i> Pembayaran Belum Lunas / Tunggakan
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kost</th>
                    <th>Kamar</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporanBelumLunas as $bayar): ?>
                <tr>
                    <td><?= htmlspecialchars($bayar['penyewa']) ?></td>
                    <td><?= htmlspecialchars($bayar['nama_kost']) ?></td>
                    <td><?= htmlspecialchars($bayar['nomor_kamar']) ?></td>
                    <td><?= htmlspecialchars($bayar['periode_bulan']) ?></td>
                    <td class="text-danger">
                        Rp <?= number_format($bayar['jumlah'],0,',','.') ?>
                    </td>
                    <td>
                        <?= $bayar['tanggal_bayar']
                            ? date('d/m/Y', strtotime($bayar['tanggal_bayar']))
                            : '-' ?>
                    </td>
                    <td>
                        <?php
                        $statusClass = match ($bayar['status']) {
                            'Belum Bayar' => 'bg-danger',
                            'Terlambat'   => 'bg-warning',
                            'Menunggu'    => 'bg-info',
                            default       => 'bg-secondary'
                        };
                        ?>
                        <span class="badge <?= $statusClass ?>">
                            <?= $bayar['status'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header bg-success text-white">
        <i class="fas fa-check-circle"></i> Pembayaran Lunas
    </div>
    <div class="card-body table-responsive">
        <table class="table table-hover table-sm">
            <thead>
                <tr>
                    <th>Penyewa</th>
                    <th>Kost</th>
                    <th>Kamar</th>
                    <th>Periode</th>
                    <th>Jumlah</th>
                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($laporanLunas as $bayar): ?>
                <tr>
                    <td><?= htmlspecialchars($bayar['penyewa']) ?></td>
                    <td><?= htmlspecialchars($bayar['nama_kost']) ?></td>
                    <td><?= htmlspecialchars($bayar['nomor_kamar']) ?></td>
                    <td><?= htmlspecialchars($bayar['periode_bulan']) ?></td>
                    <td class="text-success">
                        Rp <?= number_format($bayar['jumlah'],0,',','.') ?>
                    </td>
                    <td>
                        <?= date('d/m/Y', strtotime($bayar['tanggal_bayar'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>

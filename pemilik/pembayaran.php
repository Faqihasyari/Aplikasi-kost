<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
Auth::requireRole('pemilik');
$pageTitle = 'Pembayaran & Tunggakan';

$userId = Auth::id();

// Handle Validasi Pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $pembayaran_id = (int)$_POST['pembayaran_id'];

    if ($_POST['action'] === 'validasi') {
        // Update status menjadi Lunas dan set tanggal bayar
        Database::query("
            UPDATE pembayaran 
            SET status='Lunas', 
                tanggal_bayar=CURDATE()
            WHERE id=$pembayaran_id
        ");
        header('Location: pembayaran.php?success=valid');
        exit;
    } elseif ($_POST['action'] === 'tolak') {
        // Ambil nama file bukti
        $bayar = Database::fetchOne("SELECT bukti_pembayaran FROM pembayaran WHERE id=$pembayaran_id");
        $buktiFile = $bayar['bukti_pembayaran'];

        // Hapus file bukti dari server
        $filePath = __DIR__ . '/../uploads/bukti/' . $buktiFile;
        if ($buktiFile && file_exists($filePath)) {
            unlink($filePath);
        }

        // Update status menjadi Belum Bayar dan hapus bukti
        Database::query("
            UPDATE pembayaran 
            SET status='Belum Bayar', 
                bukti_pembayaran=NULL,
                tanggal_bayar=NULL
            WHERE id=$pembayaran_id
        ");
        header('Location: pembayaran.php?success=tolak');
        exit;
    }
}

// PEMBAYARAN BELUM LUNAS / TUNGGAKAN
$pembayaranBelumLunas = Database::fetchAll("
    SELECT pb.*, kt.tanggal_mulai, kt.tanggal_berakhir,
           u.nama_lengkap, k.nomor_kamar, ks.nama_kost
    FROM pembayaran pb
    JOIN kontrak kt ON pb.kontrak_id = kt.id
    JOIN penyewa p ON kt.penyewa_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN kamar k ON kt.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId
      AND pb.status IN ('Belum Bayar','Terlambat','Menunggu')
    ORDER BY pb.created_at DESC
");

// PEMBAYARAN LUNAS
$pembayaranLunas = Database::fetchAll("
    SELECT pb.*, kt.tanggal_mulai, kt.tanggal_berakhir,
           u.nama_lengkap, k.nomor_kamar, ks.nama_kost
    FROM pembayaran pb
    JOIN kontrak kt ON pb.kontrak_id = kt.id
    JOIN penyewa p ON kt.penyewa_id = p.id
    JOIN users u ON p.user_id = u.id
    JOIN kamar k ON kt.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId
      AND pb.status = 'Lunas'
    ORDER BY pb.created_at DESC
");


$totalTunggakan = count($pembayaranBelumLunas);


include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?php
        if ($_GET['success'] === 'valid') echo 'Pembayaran berhasil divalidasi!';
        if ($_GET['success'] === 'tolak') echo 'Bukti pembayaran ditolak dan dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-12">
        <div class="card stats-card danger">
            <div class="card-body">
                <h5 class="stats-title">Total Tunggakan</h5>
                <h3 class="stats-value text-danger">
                    <?php echo count($pembayaranBelumLunas); ?> pembayaran
                </h3>

            </div>
        </div>
    </div>
</div>

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
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaranBelumLunas as $p): ?>
                    <?php
                    $statusClass = match ($p['status']) {
                        'Lunas' => 'bg-success',
                        'Belum Bayar' => 'bg-danger',
                        'Terlambat' => 'bg-warning',
                        'Menunggu' => 'bg-info',
                        default => 'bg-secondary'
                    };
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($p['nama_kost']) ?></td>
                        <td><?= htmlspecialchars($p['nomor_kamar']) ?></td>
                        <td><?= htmlspecialchars($p['periode_bulan']) ?></td>
                        <td class="text-danger">Rp<?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                        <td><?= $p['tanggal_bayar'] ? date('d/m/Y', strtotime($p['tanggal_bayar'])) : '-' ?></td>
                        <td>
                            <span class="badge <?= $statusClass ?>">
                                <?= $p['status'] ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($p['bukti_pembayaran']): ?>
                                <a href="/coba_kost/uploads/bukti/<?= $p['bukti_pembayaran'] ?>"
                                    target="_blank" class="btn btn-sm btn-info">
                                    <i class="fas fa-file-image"></i> Lihat
                                </a>
                            <?php else: ?> - <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($p['status'] === 'Menunggu' && $p['bukti_pembayaran']): ?>
                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Validasi pembayaran ini?')">
                                    <input type="hidden" name="action" value="validasi">
                                    <input type="hidden" name="pembayaran_id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-success btn-sm"><i class="fas fa-check"></i></button>
                                </form>

                                <form method="POST" class="d-inline"
                                    onsubmit="return confirm('Tolak pembayaran ini?')">
                                    <input type="hidden" name="action" value="tolak">
                                    <input type="hidden" name="pembayaran_id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-danger btn-sm"><i class="fas fa-times"></i></button>
                                </form>
                            <?php else: ?> <?php endif; ?>
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
                    <th>Bukti</th>

                    <th>Tanggal Bayar</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pembayaranLunas as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($p['nama_kost']) ?></td>
                        <td><?= htmlspecialchars($p['nomor_kamar']) ?></td>
                        <td><?= htmlspecialchars($p['periode_bulan']) ?></td>
                        <td class="text-success">Rp<?= number_format($p['jumlah'], 0, ',', '.') ?></td>
                        <td>
                            <?php if ($p['bukti_pembayaran']): ?>
                                <a href="/coba_kost/uploads/bukti/<?php echo htmlspecialchars($p['bukti_pembayaran']); ?>"
                                    target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-file-image"></i> Lihat
                                </a>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>

                        <td><?= date('d/m/Y', strtotime($p['tanggal_bayar'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include __DIR__ . '/../layout/footer.php'; ?>
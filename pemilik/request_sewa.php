<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
require_once __DIR__ . '/../lib/request.php';

Auth::requireRole('pemilik');
$pageTitle = 'Kelola Request Penyewaan';

$userId = Auth::id();

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = (int)$_POST['request_id'];
    
    if ($_POST['action'] === 'terima') {
        $result = RequestSewa::terimaRequest($request_id, $userId);
        header('Location: request_sewa.php?success=diterima');
        exit;
    }
    
    if ($_POST['action'] === 'tolak') {
        $result = RequestSewa::tolakRequest($request_id, $userId);
        header('Location: request_sewa.php?success=ditolak');
        exit;
    }
    
    if ($_POST['action'] === 'konfirmasi_pembayaran') {
        $approve = $_POST['approve'] === 'ya';
        $result = RequestSewa::konfirmasiPembayaran($request_id, $userId, $approve);
        header('Location: request_sewa.php?success=' . ($approve ? 'disetujui' : 'ditolak'));
        exit;
    }
}

$requests = RequestSewa::getRequestByPemilik($userId);

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle"></i>
    <?php 
    if ($_GET['success'] === 'diterima') echo 'Request berhasil diterima! QR Code telah dikirim ke pembeli.';
    if ($_GET['success'] === 'ditolak') echo 'Request berhasil ditolak!';
    if ($_GET['success'] === 'disetujui') echo 'Pembayaran disetujui! Penyewa aktif.';
    if ($_GET['success'] === 'ditolak_pembayaran') echo 'Pembayaran ditolak!';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-file-signature"></i> Request Penyewaan
    </div>
    <div class="card-body">
        <?php if (empty($requests)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada request penyewaan
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Penyewa</th>
                        <th>Kontak</th>
                        <th>Kost</th>
                        <th>Kamar</th>
                        <th>Harga</th>
                        <th>Tanggal Request</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($req['nama_lengkap']); ?></strong><br>
                            <small><?php echo htmlspecialchars($req['username']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($req['no_hp']); ?></td>
                        <td><?php echo htmlspecialchars($req['nama_kost']); ?></td>
                        <td><?php echo htmlspecialchars($req['nomor_kamar']); ?></td>
                        <td>Rp<?php echo number_format($req['harga_sewa'], 0, ',', '.'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($req['created_at'])); ?></td>
                        <td>
                            <?php if ($req['status'] === 'Menunggu'): ?>
                                <span class="badge bg-warning">Menunggu</span>
                            <?php elseif ($req['status'] === 'Diterima'): ?>
                                <span class="badge bg-success">Diterima - QR Terkirim</span>
                            <?php elseif ($req['status'] === 'Menunggu Konfirmasi'): ?>
                                <span class="badge bg-info">Menunggu Konfirmasi Bukti</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($req['status'] === 'Menunggu'): ?>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Terima request ini?')">
                                <input type="hidden" name="action" value="terima">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fas fa-check"></i> Terima
                                </button>
                            </form>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Tolak request ini?')">
                                <input type="hidden" name="action" value="tolak">
                                <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-times"></i> Tolak
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($req['status'] === 'Menunggu Konfirmasi'): ?>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" 
                                    data-bs-target="#konfirmModal<?php echo $req['id']; ?>">
                                <i class="fas fa-eye"></i> Konfirmasi
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- Modal Konfirmasi Bukti -->
                    <?php if ($req['status'] === 'Menunggu Konfirmasi'): ?>
                    <div class="modal fade" id="konfirmModal<?php echo $req['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Konfirmasi Pembayaran - <?php echo htmlspecialchars($req['nama_lengkap']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <h6>Data Penyewa</h6>
                                            <p><strong>Nama:</strong> <?php echo htmlspecialchars($req['nama_lengkap']); ?></p>
                                            <p><strong>No. HP:</strong> <?php echo htmlspecialchars($req['no_hp']); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Data Kamar</h6>
                                            <p><strong>Kamar:</strong> <?php echo htmlspecialchars($req['nomor_kamar']); ?></p>
                                            <p><strong>Harga:</strong> Rp<?php echo number_format($req['harga_sewa'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <h6 class="text-center mb-3">Bukti Pembayaran</h6>
                                    <?php if ($req['bukti_pembayaran']): ?>
                                    <div class="text-center">
                                        <img src="/coba_kost/uploads/bukti/<?php echo $req['bukti_pembayaran']; ?>" 
                                             class="img-fluid mb-3" style="max-height:400px;">
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-warning text-center">
                                        Bukti pembayaran tidak ditemukan
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="modal-footer">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="konfirmasi_pembayaran">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="approve" value="ya">
                                        <button type="submit" class="btn btn-success" 
                                                onclick="return confirm('Setujui pembayaran ini?')">
                                            <i class="fas fa-check"></i> Setujui & Aktifkan
                                        </button>
                                    </form>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="konfirmasi_pembayaran">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <input type="hidden" name="approve" value="tidak">
                                        <button type="submit" class="btn btn-danger" 
                                                onclick="return confirm('Tolak pembayaran ini?')">
                                            <i class="fas fa-times"></i> Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<?php
require_once __DIR__.'/../lib/auth.php';
require_once __DIR__.'/../lib/database.php';
require_once __DIR__.'/../lib/request.php';

Auth::requireRole('pembeli');
$pageTitle = 'Request Penyewaan';

// Handle upload bukti
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'upload_bukti'){
    $request_id = (int)$_POST['request_id'];
    $result = RequestSewa::uploadBukti($request_id, $_FILES['bukti_pembayaran']);
    
    if($result['success']){
        header('Location: request.php?success=uploaded');
    } else {
        $error = $result['message'];
    }
    exit;
}

$userId = Auth::id();
$requests = RequestSewa::getRequestByUser($userId);

include __DIR__.'/../layout/header.php';
include __DIR__.'/../layout/sidebar.php';
?>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle"></i> 
    <?php 
    if($_GET['success'] === 'requested') echo 'Request berhasil dikirim! Menunggu konfirmasi pemilik.';
    if($_GET['success'] === 'uploaded') echo 'Bukti pembayaran berhasil diupload! Menunggu konfirmasi pemilik.';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($error)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-clock"></i> Request Penyewaan Saya
    </div>
    <div class="card-body">
        <?php if(empty($requests)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada request. 
                <a href="/coba_kost/pembeli/kost.php" class="alert-link">Cari kost sekarang</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kost</th>
                            <th>Kamar</th>
                            <th>Harga</th>
                            <th>Tanggal Request</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($requests as $req): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($req['nama_kost']); ?></td>
                            <td><?php echo htmlspecialchars($req['nomor_kamar']); ?></td>
                            <td>Rp<?php echo number_format($req['harga_sewa'], 0, ',', '.'); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($req['created_at'])); ?></td>
                            <td>
                                <?php if($req['status'] === 'Menunggu'): ?>
                                    <span class="badge bg-warning">Menunggu Konfirmasi</span>
                                <?php elseif($req['status'] === 'Diterima'): ?>
                                    <span class="badge bg-success">
                                        Diterima - Bayar dalam 
                                        <?php 
                                        if($req['jam_tersisa'] > 0) 
                                            echo $req['jam_tersisa'].' jam'; 
                                        else 
                                            echo $req['menit_tersisa'].' menit'; 
                                        ?>
                                    </span>
                                <?php elseif($req['status'] === 'Menunggu Konfirmasi'): ?>
                                    <span class="badge bg-info">Menunggu Konfirmasi Bukti</span>
                                <?php elseif($req['status'] === 'Ditolak'): ?>
                                    <span class="badge bg-danger">Ditolak</span>
                                <?php elseif($req['status'] === 'Selesai'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($req['status'] === 'Diterima' && strtotime($req['qr_expired']) > time()): ?>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal<?php echo $req['id']; ?>">
                                        <i class="fas fa-upload"></i> Upload Bukti
                                    </button>
                                <?php endif; ?>
                                
                                <?php if($req['status'] === 'Ditolak'): ?>
                                    <a href="/coba_kost/pembeli/kost.php" class="btn btn-info btn-sm">
                                        <i class="fas fa-search"></i> Cari Lain
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        
                        <!-- Modal Upload Bukti -->
                        <?php if($req['status'] === 'Diterima' && strtotime($req['qr_expired']) > time()): ?>
                        <div class="modal fade" id="uploadModal<?php echo $req['id']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="action" value="upload_bukti">
                                        <input type="hidden" name="request_id" value="<?php echo $req['id']; ?>">
                                        <div class="modal-body">
                                            <div class="mb-3 text-center">
                                                <div class="alert alert-warning">
                                                    <strong>QR Code Pembayaran</strong><br>
                                                    <small>Scan QR Code ini menggunakan aplikasi pembayaran Anda</small>
                                                </div>
                                                
                                                <?php
                                                // Generate QR Code data dengan informasi pembayaran
                                                $qrData = "REQUEST_ID:".$req['id']."|NAMA:".$req['nama_kost']."|KAMAR:".$req['nomor_kamar']."|JUMLAH:".$req['harga_sewa'];
                                                $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
                                                ?>
                                                
                                                <div class="qris-container bg-light p-3 rounded">
                                                    <div class="text-center mb-2">
                                                        <strong>REQ#<?php echo $req['id']; ?></strong><br>
                                                        <small>Rp<?php echo number_format($req['harga_sewa'], 0, ',', '.'); ?></small>
                                                    </div>
                                                    <img src="<?php echo $qrCodeUrl; ?>" 
                                                         alt="QR Code Pembayaran" 
                                                         class="img-fluid rounded shadow"
                                                         style="border: 1px solid #ddd; padding: 10px; background: white; max-width: 300px;">
                                                </div>
                                                
                                                <div class="alert alert-info mt-3">
                                                    <strong>Gunakan QRIS:</strong> Scan QR Code ini menggunakan aplikasi pembayaran Anda
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Upload Bukti Pembayaran</label>
                                                <input type="file" class="form-control" name="bukti_pembayaran" accept="image/*" required>
                                                <small class="text-muted">Format: JPG/PNG/WebP (max 5MB)</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Kirim Bukti</button>
                                        </div>
                                    </form>
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

<?php include __DIR__.'/../layout/footer.php'; ?>
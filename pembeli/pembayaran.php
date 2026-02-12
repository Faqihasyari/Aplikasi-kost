<?php
require_once __DIR__.'/../lib/auth.php';
require_once __DIR__.'/../lib/database.php';
Auth::requireRole('pembeli');
$pageTitle = 'Pembayaran';

$userId = Auth::id();

// Handle Upload Bukti - Status "Menunggu" dulu
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bayar'){
    $pembayaran_ids = $_POST['pembayaran_id'] ?? [];
    
    if(empty($pembayaran_ids)){
        header('Location: /coba_kost/pembeli/pembayaran.php');
        exit;
    }

    if(!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== 0){
        die('Bukti pembayaran wajib diupload.');
    }

    $ext = strtolower(pathinfo($_FILES['bukti_pembayaran']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp'];
    
    if(!in_array($ext, $allowed)){
        die('Format file tidak valid. Gunakan JPG, JPEG, PNG, atau WebP.');
    }

    $buktiName = 'bukti_'.time().'_'.$userId.'.'.$ext;
    $uploadDir = $_SERVER['DOCUMENT_ROOT'].'/coba_kost/uploads/bukti/';
    
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0777, true);
    }

    if(move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $uploadDir.$buktiName)) {
        // Status berubah menjadi "Menunggu" bukan "Lunas"
        foreach($pembayaran_ids as $id){
            Database::query("
                UPDATE pembayaran 
                SET status='Menunggu', 
                    bukti_pembayaran='$buktiName',
                    tanggal_bayar=NULL
                WHERE id=$id
            ");
        }
        header('Location: /coba_kost/pembeli/pembayaran.php?success=uploaded');
        exit;
    } else {
        die('Gagal upload bukti pembayaran.');
    }
}

$pembayaranData = Database::fetchAll("
    SELECT pb.*, kt.tanggal_mulai, kt.tanggal_berakhir, k.nomor_kamar, ks.nama_kost
    FROM pembayaran pb
    JOIN kontrak kt ON pb.kontrak_id = kt.id
    JOIN penyewa p ON kt.penyewa_id = p.id
    JOIN kamar k ON kt.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE p.user_id = $userId
    ORDER BY pb.bulan_ke ASC
");

include __DIR__.'/../layout/header.php';
include __DIR__.'/../layout/sidebar.php';
?>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle"></i> 
    <?php 
    if($_GET['success'] === 'paid') echo 'Pembayaran berhasil! Terima kasih.';
    if($_GET['success'] === 'uploaded') echo 'Bukti pembayaran berhasil diupload! Menunggu validasi pemilik.';
    ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['kontrak_id'])): ?>
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i> Kontrak berhasil dibuat! Silakan lakukan pembayaran di bawah ini.
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-money-bill-wave"></i> Daftar Pembayaran
    </div>
    <div class="card-body">
        <?php if(empty($pembayaranData)): ?>
            <div class="alert alert-info">Belum ada pembayaran.</div>
        <?php else: ?>
            <form method="POST" id="formBayar" enctype="multipart/form-data">
                <input type="hidden" name="action" value="bayar">
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="checkAll"></th>
                                <th>Kost</th>
                                <th>Kamar</th>
                                <th>Periode</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pembayaranData as $p): ?>
                            <tr>
                                <td>
                                    <?php if($p['status'] !== 'Lunas' && $p['status'] !== 'Menunggu'): ?>
                                        <input type="checkbox" name="pembayaran_id[]" value="<?php echo $p['id']; ?>" class="cbBayar">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($p['nomor_kamar']); ?></td>
                                <td><?php echo htmlspecialchars($p['periode_bulan']); ?></td>
                                <td>Rp<?php echo number_format($p['jumlah'], 0, ',', '.'); ?></td>
                                <td>
                                    <span class="badge badge-status badge-<?php echo strtolower(str_replace(' ', '-', $p['status'])); ?>">
                                        <?php echo $p['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning">
                    <strong>Total yang dipilih:</strong> <span id="totalBayar">Rp 0</span>
                </div>

                <button type="button" class="btn btn-primary" onclick="showQRIS()" id="btnBayar" disabled>
                    <i class="fas fa-qrcode"></i> Bayar dengan QRIS
                </button>

                <!-- Modal QRIS -->
                <div class="modal fade" id="qrisModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Pembayaran QRIS</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <h5 class="text-center">Scan QRIS untuk Pembayaran</h5>
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=QRIS_PAYMENT" 
                                     class="img-fluid d-block mx-auto mb-3">
                                
                                <div class="alert alert-info text-center">
                                    <strong>Total:</strong> <span id="totalQRIS">Rp 0</span>
                                </div>

                                <!-- INPUT FILE -->
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*" required>
                                    <small class="text-muted">Format: JPG/JPEG/PNG/WebP</small>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success">Konfirmasi Pembayaran</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('checkAll')?.addEventListener('change', function(){
    const checkboxes = document.querySelectorAll('.cbBayar');
    checkboxes.forEach(cb => cb.checked = this.checked);
    hitungTotal();
});

document.querySelectorAll('.cbBayar').forEach(cb => {
    cb.addEventListener('change', hitungTotal);
});

function hitungTotal(){
    const checked = document.querySelectorAll('.cbBayar:checked');
    let total = 0;

    checked.forEach(cb => {
        const row = cb.closest('tr');
        const hargaText = row.cells[4].textContent.replace(/[^0-9]/g, '');
        total += parseInt(hargaText);
    });

    document.getElementById('totalBayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('totalQRIS').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('btnBayar').disabled = checked.length === 0;
}

function showQRIS(){
    new bootstrap.Modal(document.getElementById('qrisModal')).show();
}
</script>

<?php include __DIR__.'/../layout/footer.php'; ?>
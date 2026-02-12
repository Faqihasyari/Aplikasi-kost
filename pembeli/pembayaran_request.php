<?php
// File: coba_kost/pembeli/pembayaran_request.php

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pembeli');
$pageTitle = 'Pembayaran Request';
$userId = Auth::id();

// Ambil request yang menunggu pembayaran
$request = Database::fetchOne("
    SELECT r.*, k.nama_kost, km.nomor_kamar, km.harga_sewa
    FROM request_penyewaan r
    JOIN kost k ON r.kost_id = k.id
    JOIN kamar km ON r.kamar_id = km.id
    WHERE r.user_id = $userId 
    AND r.status = 'menunggu_pembayaran'
    ORDER BY r.tanggal_request DESC
    LIMIT 1
");

if (!$request) {
    header('Location: /coba_kost/pembeli/dashboard.php');
    exit;
}

// Handle upload bukti pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bukti_pembayaran'])) {
    $file = $_FILES['bukti_pembayaran'];
    if ($file['error'] === 0) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
        if (in_array($ext, $allowed)) {
            $fileName = 'bukti_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../uploads/bukti/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                Database::query("
                    UPDATE request_penyewaan 
                    SET bukti_pembayaran = '$fileName',
                        status = 'menunggu_verifikasi'
                    WHERE id = {$request['id']}
                ");
                $success = 'Bukti pembayaran berhasil diupload! Tunggu verifikasi pemilik.';
            }
        }
    }
}

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-qrcode"></i> Pembayaran Request Sewa
    </div>
    <div class="card-body text-center">
        <h4>Scan QRIS untuk Pembayaran</h4>
        
        <!-- QR Code -->
        <img src="<?php echo $request['qr_code']; ?>" class="img-fluid mb-3" style="max-width: 300px;">
        
        <div class="alert alert-info">
            <p><strong>Kost:</strong> <?php echo htmlspecialchars($request['nama_kost']); ?></p>
            <p><strong>Kamar:</strong> <?php echo htmlspecialchars($request['nomor_kamar']); ?></p>
            <p><strong>Harga:</strong> Rp <?php echo number_format($request['harga_sewa'], 0, ',', '.'); ?></p>
            <p><strong>Batas Pembayaran:</strong> <?php echo date('d/m/Y H:i', strtotime($request['batas_bayar'])); ?></p>
        </div>
        
        <!-- Form Upload Bukti -->
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Upload Bukti Pembayaran</label>
                <input type="file" class="form-control" name="bukti_pembayaran" required accept="image/*,.pdf">
            </div>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-upload"></i> Kirim Bukti Pembayaran
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
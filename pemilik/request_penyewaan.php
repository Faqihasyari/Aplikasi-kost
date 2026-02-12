<?php
// File: coba_kost/pemilik/request_penyewaan.php

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Request Penyewaan';
$userId = Auth::id();

// Handle Aksi Terima/Tolak
if (isset($_GET['action']) && isset($_GET['id'])) {
    $requestId = (int)$_GET['id'];
    $action = $_GET['action'];

    if ($action === 'terima') {
        // Generate QR Code (simpan URL atau data QR)
        $qrData = "PAYMENT_" . time() . "_" . $requestId;
        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrData);
        
        // Set batas waktu 1 hari
        $batasBayar = date('Y-m-d H:i:s', strtotime('+1 day'));
        
        Database::query("
            UPDATE request_penyewaan 
            SET status = 'menunggu_pembayaran', 
                qr_code = '$qrCodeUrl',
                batas_bayar = '$batasBayar'
            WHERE id = $requestId
        ");
        
        // TODO: Kirim notifikasi/email ke pembeli dengan QR Code
    } elseif ($action === 'tolak') {
        Database::query("UPDATE request_penyewaan SET status = 'ditolak' WHERE id = $requestId");
    }
}

// Ambil data request
$requests = Database::fetchAll("
    SELECT r.*, u.nama_lengkap, u.email, u.no_hp, 
           k.nama_kost, km.nomor_kamar, km.harga_sewa
    FROM request_penyewaan r
    JOIN users u ON r.user_id = u.id
    JOIN kost k ON r.kost_id = k.id
    JOIN kamar km ON r.kamar_id = km.id
    WHERE k.pemilik_id = $userId
    ORDER BY r.tanggal_request DESC
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-clock"></i> Daftar Request Penyewaan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pembeli</th>
                        <th>Kost</th>
                        <th>Kamar</th>
                        <th>Tanggal Request</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($req['nama_kost']); ?></td>
                        <td><?php echo htmlspecialchars($req['nomor_kamar']); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($req['tanggal_request'])); ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $req['status'] === 'menunggu' ? 'warning' :
                                ($req['status'] === 'menunggu_pembayaran' ? 'info' :
                                ($req['status'] === 'menunggu_verifikasi' ? 'primary' :
                                ($req['status'] === 'diterima' ? 'success' : 'danger'))); 
                            ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $req['status'])); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($req['status'] === 'menunggu'): ?>
                                <a href="?action=terima&id=<?php echo $req['id']; ?>" 
                                   class="btn btn-success btn-sm" 
                                   onclick="return confirm('Terima request ini?')">
                                    <i class="fas fa-check"></i> Terima
                                </a>
                                <a href="?action=tolak&id=<?php echo $req['id']; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Tolak request ini?')">
                                    <i class="fas fa-times"></i> Tolak
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
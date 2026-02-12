<?php
// File: coba_kost/pemilik/verifikasi_pembayaran.php

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Verifikasi Pembayaran';
$userId = Auth::id();

// Ambil request yang menunggu verifikasi
$requests = Database::fetchAll("
    SELECT r.*, u.nama_lengkap, k.nama_kost, km.nomor_kamar, km.harga_sewa
    FROM request_penyewaan r
    JOIN users u ON r.user_id = u.id
    JOIN kost k ON r.kost_id = k.id
    JOIN kamar km ON r.kamar_id = km.id
    WHERE k.pemilik_id = $userId 
    AND r.status = 'menunggu_verifikasi'
    ORDER BY r.tanggal_request DESC
");

// Handle verifikasi
if (isset($_GET['action']) && isset($_GET['id'])) {
    $requestId = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'terima') {
        // Dapatkan data request
        $requestData = Database::fetchOne("SELECT * FROM request_penyewaan WHERE id = $requestId");
        
        // Buat kontrak aktif
        Database::query("
            INSERT INTO penyewa (user_id, kamar_id, tanggal_masuk, status) 
            VALUES ({$requestData['user_id']}, {$requestData['kamar_id']}, NOW(), 'Aktif')
        ");
        $penyewaId = Database::getLastID();
        
        // Update status request
        Database::query("UPDATE request_penyewaan SET status = 'diterima' WHERE id = $requestId");
        
        // Update status kamar
        Database::query("UPDATE kamar SET status = 'Terisi' WHERE id = {$requestData['kamar_id']}");
        
        $success = "Pembayaran diterima! Penyewa sekarang aktif.";
        
    } elseif ($action === 'tolak') {
        Database::query("DELETE FROM request_penyewaan WHERE id = $requestId");
        $success = "Pembayaran ditolak! Request dihapus.";
    }
}

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header">
        <i class="fas fa-check-circle"></i> Verifikasi Pembayaran
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Pembeli</th>
                        <th>Kost</th>
                        <th>Kamar</th>
                        <th>Harga</th>
                        <th>Bukti Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($req['nama_kost']); ?></td>
                        <td><?php echo htmlspecialchars($req['nomor_kamar']); ?></td>
                        <td>Rp <?php echo number_format($req['harga_sewa'], 0, ',', '.'); ?></td>
                        <td>
                            <?php if ($req['bukti_pembayaran']): ?>
                            <a href="/coba_kost/uploads/bukti/<?php echo $req['bukti_pembayaran']; ?>" 
                               target="_blank" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> Lihat
                            </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="?action=terima&id=<?php echo $req['id']; ?>" 
                               class="btn btn-success btn-sm"
                               onclick="return confirm('Terima pembayaran ini? Penyewa akan aktif.')">
                                <i class="fas fa-check"></i> Terima
                            </a>
                            <a href="?action=tolak&id=<?php echo $req['id']; ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Tolak pembayaran ini? Request akan dihapus.')">
                                <i class="fas fa-times"></i> Tolak
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pembeli');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pembayaran_id'])) {
    $userId = Auth::id();
    $pembayaran_id = (int)$_POST['pembayaran_id'];
    
    // Validasi kepemilikan pembayaran
    $pembayaran = Database::fetchOne("
        SELECT pb.*, k.kost_id, ks.pemilik_id
        FROM pembayaran pb
        JOIN kontrak ktr ON pb.kontrak_id = ktr.id
        JOIN penyewa p ON ktr.penyewa_id = p.id
        JOIN kamar k ON p.kamar_id = k.id
        JOIN kost ks ON k.kost_id = ks.id
        WHERE pb.id = $pembayaran_id 
        AND p.user_id = $userId 
        AND pb.status = 'Belum Bayar'
    ");
    
    if (!$pembayaran) {
        header('Location: /coba_kost/pembeli/pembayaran.php?error=invalid');
        exit;
    }
    
    // Validasi file
    if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
        header('Location: /coba_kost/pembeli/pembayaran.php?error=file');
        exit;
    }
    
    $file = $_FILES['bukti_pembayaran'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($ext, $allowed) || $file['size'] > 5000000) {
        header('Location: /coba_kost/pembeli/pembayaran.php?error=format');
        exit;
    }
    
    $buktiName = 'bayar_' . time() . '_' . $userId . '_' . $pembayaran_id . '.' . $ext;
    $uploadDir = __DIR__ . '/../uploads/bukti/';
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $buktiName)) {
        // Update pembayaran - HANYA update kolom yang ada di struktur tabel
        Database::query("
            UPDATE pembayaran 
            SET bukti_pembayaran = '$buktiName',
                status = 'Menunggu',
                tanggal_upload = NOW()
            WHERE id = $pembayaran_id
        ");
        
        header('Location: /coba_kost/pembeli/pembayaran.php?success=bayar');
    } else {
        header('Location: /coba_kost/pembeli/pembayaran.php?error=upload');
    }
    
    exit;
}

header('Location: /coba_kost/pembeli/pembayaran.php');
exit;
?>
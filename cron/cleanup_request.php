<?php
require_once __DIR__ . '/../lib/database.php';

// Hapus request yang batas bayarnya sudah lewat
$sql = "DELETE FROM request_penyewaan 
        WHERE batas_bayar < NOW() 
        AND status = 'menunggu_pembayaran'";
Database::query($sql);

echo "Cleanup completed: " . date('Y-m-d H:i:s');
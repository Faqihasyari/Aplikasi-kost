<?php
/**
 * Request Sewa Library
 * File: lib/request.php
 */
require_once __DIR__.'/database.php';
require_once __DIR__.'/auth.php';

class RequestSewa{
    
    public static function buatRequest($kamar_id){
        $user_id = Auth::id();
        
        // Cek apakah sudah punya kontrak aktif
        $cekKontrak = Database::fetchOne("
            SELECT p.id 
            FROM penyewa p 
            WHERE p.user_id=$user_id AND p.status='Aktif'
        ");
        if($cekKontrak){
            return ['success'=>false,'message'=>'Anda sudah memiliki kontrak aktif!'];
        }
        
        // Cek apakah sudah ada request aktif untuk kamar ini
        $cekRequest = Database::fetchOne("
            SELECT id 
            FROM request_sewa 
            WHERE user_id=$user_id AND kamar_id=$kamar_id 
            AND status IN('Menunggu','Diterima','Menunggu Konfirmasi')
        ");
        if($cekRequest){
            return ['success'=>false,'message'=>'Anda sudah mengajukan request untuk kamar ini!'];
        }
        
        // Cek ketersediaan kamar
        $kamar = Database::fetchOne("SELECT status FROM kamar WHERE id=$kamar_id");
        if(!$kamar || $kamar['status']!=='Kosong'){
            return ['success'=>false,'message'=>'Kamar tidak tersedia!'];
        }
        
        // Insert request - created_at otomatis oleh database
        Database::query("
            INSERT INTO request_sewa(user_id, kamar_id, status) 
            VALUES($user_id,$kamar_id,'Menunggu')
        ");
        
        return ['success'=>true,'message'=>'Request berhasil dikirim! Menunggu konfirmasi pemilik.'];
    }
    
    public static function terimaRequest($request_id,$pemilik_id){
        // Validasi kepemilikan
        $request = Database::fetchOne("
            SELECT rs.*, k.kost_id, ks.pemilik_id 
            FROM request_sewa rs
            JOIN kamar k ON rs.kamar_id = k.id 
            JOIN kost ks ON k.kost_id = ks.id 
            WHERE rs.id=$request_id AND rs.status='Menunggu'
        ");
        
        if(!$request || $request['pemilik_id']!=$pemilik_id){
            return ['success'=>false,'message'=>'Request tidak valid atau bukan milik Anda!'];
        }
        
        // Generate QR Code unique
        $qr_code = 'REQ_'.$request_id.'_'.time().'.png';
        
        // ⭐ PERBAIKAN KRITIS: Hitung qr_expired LANGSUNG di SQL dengan DATE_ADD
        // Ini menghindari masalah timezone dan double calculation
        Database::query("
            UPDATE request_sewa 
            SET status='Diterima', 
                qr_code='$qr_code', 
                qr_expired=DATE_ADD(created_at, INTERVAL 24 HOUR)
            WHERE id=$request_id
        ");
        
        // Generate QR Code (simulasi)
        self::generateQRCode($request_id,$qr_code);
        
        return ['success'=>true,'message'=>'Request diterima! QR Code telah dikirim ke pembeli.'];
    }
    
    public static function tolakRequest($request_id,$pemilik_id){
        $request = Database::fetchOne("
            SELECT rs.*, k.kost_id, ks.pemilik_id 
            FROM request_sewa rs
            JOIN kamar k ON rs.kamar_id = k.id 
            JOIN kost ks ON k.kost_id = ks.id 
            WHERE rs.id=$request_id AND rs.status='Menunggu'
        ");
        
        if(!$request || $request['pemilik_id']!=$pemilik_id){
            return ['success'=>false,'message'=>'Request tidak valid!'];
        }
        
        Database::query("
            UPDATE request_sewa 
            SET status='Ditolak' 
            WHERE id=$request_id
        ");
        
        return ['success'=>true,'message'=>'Request ditolak!'];
    }
    
    public static function uploadBukti($request_id,$file){
        $user_id = Auth::id();
        $request = Database::fetchOne("
            SELECT * 
            FROM request_sewa 
            WHERE id=$request_id AND user_id=$user_id AND status='Diterima'
        ");
        
        if(!$request){
            return ['success'=>false,'message'=>'Request tidak valid!'];
        }
        
        // Cek expired - gunakan SQL untuk konsistensi
        $isExpired = Database::fetchOne("
            SELECT CASE 
                WHEN qr_expired <= NOW() THEN 1 
                ELSE 0 
            END as expired 
            FROM request_sewa 
            WHERE id=$request_id
        ");
        
        if($isExpired['expired']){
            Database::query("UPDATE request_sewa SET status='Ditolak' WHERE id=$request_id");
            return ['success'=>false,'message'=>'QR Code sudah expired! Request ditolak.'];
        }
        
        // Validasi file
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        
        if(!in_array($ext,$allowed) || $file['size'] > 5000000){ // max 5MB
            return ['success'=>false,'message'=>'File tidak valid (max 5MB, format: jpg/jpeg/png/webp)!'];
        }
        
        $buktiName = 'bukti_'.time().'_'.$user_id.'.'.$ext;
        
        $uploadDir = __DIR__.'/../uploads/bukti/';
        if(!is_dir($uploadDir)){
            mkdir($uploadDir, 0777, true);
        }
        
        if(move_uploaded_file($file['tmp_name'],$uploadDir.$buktiName)) {
            Database::query("
                UPDATE request_sewa 
                SET bukti_pembayaran='$buktiName', 
                    status='Menunggu Konfirmasi' 
                WHERE id=$request_id
            ");
            
            return ['success'=>true,'message'=>'Bukti pembayaran berhasil diupload! Menunggu konfirmasi pemilik.'];
        }
        
        return ['success'=>false,'message'=>'Gagal upload bukti!'];
    }
    
    public static function konfirmasiPembayaran($request_id,$pemilik_id, $approve=true){
        $request = Database::fetchOne("
            SELECT rs.*, k.harga_sewa, k.kost_id, ks.pemilik_id, u.nama_lengkap 
            FROM request_sewa rs
            JOIN kamar k ON rs.kamar_id = k.id 
            JOIN kost ks ON k.kost_id = ks.id 
            JOIN users u ON rs.user_id = u.id 
            WHERE rs.id=$request_id AND rs.status='Menunggu Konfirmasi'
        ");
        
        if(!$request || $request['pemilik_id']!=$pemilik_id){
            return ['success'=>false,'message'=>'Request tidak valid!'];
        }
        
        if($approve){
            // Buat penyewa
            Database::query("
                INSERT INTO penyewa(user_id, kamar_id, tanggal_masuk, status) 
                VALUES({$request['user_id']},{$request['kamar_id']},NOW(),'Aktif')
            ");
            $penyewa_id = Database::getLastId();
            
            // Buat kontrak 1 bulan
            $tanggal_mulai = date('Y-m-d');
            $tanggal_berakhir = date('Y-m-d', strtotime('+1 month'));
            $total_harga = $request['harga_sewa'];
            
            Database::query("
                INSERT INTO kontrak 
                (penyewa_id, kamar_id, tanggal_mulai, tanggal_berakhir, durasi_bulan, total_harga, status) 
                VALUES 
                ($penyewa_id,{$request['kamar_id']},'$tanggal_mulai', '$tanggal_berakhir', 1,$total_harga,'Aktif')
            ");
            $kontrak_id = Database::getLastId();
            
            // Buat pembayaran
            Database::query("
                INSERT INTO pembayaran 
                (kontrak_id, bulan_ke, periode_bulan, jumlah, status, tanggal_bayar, bukti_pembayaran) 
                VALUES 
                ($kontrak_id, 1, '".date('F Y')."', {$request['harga_sewa']},'Lunas', CURDATE(), '{$request['bukti_pembayaran']}')
            ");
            
            // Update status kamar
            Database::query("
                UPDATE kamar 
                SET status='Terisi' 
                WHERE id={$request['kamar_id']}
            ");
            
            // Update request
            Database::query("
                UPDATE request_sewa 
                SET status='Selesai' 
                WHERE id=$request_id
            ");
            
            return ['success'=>true,'message'=>'Pembayaran disetujui! Penyewa berhasil diaktifkan.'];
        } else {
            // Tolak pembayaran - hapus request
            Database::query("
                UPDATE request_sewa 
                SET status='Ditolak' 
                WHERE id=$request_id
            ");
            
            return ['success'=>true,'message'=>'Pembayaran ditolak!'];
        }
    }
    
    private static function generateQRCode($request_id,$filename){
        // Simulasi QR Code - di production gunakan library seperti BaconQrCode
        
        $uploadDir = __DIR__.'/../uploads/qr/';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        
        // Buat placeholder QR
        $text = "REQUEST:$request_id|EXPIRES:".date('Y-m-d H:i:s', strtotime('+1 day'));
        file_put_contents($uploadDir.$filename,"QR_PLACEHOLDER:$text");
    }
    
    public static function getRequestByUser($user_id){
        // ⭐ PERBAIKAN: Hitung sisa waktu dengan SQL agar akurat
        return Database::fetchAll("
            SELECT rs.*, 
                   k.nomor_kamar, 
                   ks.nama_kost, 
                   k.harga_sewa,
                   TIMESTAMPDIFF(HOUR, NOW(), DATE_ADD(rs.created_at, INTERVAL 24 HOUR)) as jam_tersisa,
                   TIMESTAMPDIFF(MINUTE, NOW(), DATE_ADD(rs.created_at, INTERVAL 24 HOUR)) as menit_tersisa
            FROM request_sewa rs
            JOIN kamar k ON rs.kamar_id = k.id 
            JOIN kost ks ON k.kost_id = ks.id 
            WHERE rs.user_id=$user_id
            ORDER BY rs.created_at DESC
        ");
    }
    
    public static function getRequestByPemilik($pemilik_id){
        return Database::fetchAll("
            SELECT rs.*, 
                   u.nama_lengkap, 
                   u.username, 
                   u.no_hp,
                   k.nomor_kamar, 
                   ks.nama_kost, 
                   k.harga_sewa, 
                   k.foto as foto_kamar,
                   DATE_ADD(rs.created_at, INTERVAL 24 HOUR) as batas_waktu
            FROM request_sewa rs
            JOIN kamar k ON rs.kamar_id = k.id 
            JOIN kost ks ON k.kost_id = ks.id 
            JOIN users u ON rs.user_id = u.id 
            WHERE ks.pemilik_id=$pemilik_id 
            AND rs.status IN('Menunggu','Diterima','Menunggu Konfirmasi')
            ORDER BY rs.created_at DESC
        ");
    }
}
?>
<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;

Auth::requireRole('admin');

/* ======================
   AMBIL DATA
====================== */

// LAPORAN DATA KOST
$laporanKost = Database::fetchAll("
    SELECT k.nama_kost, u.nama_lengkap AS pemilik, k.alamat,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id) AS total_kamar,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Terisi') AS kamar_terisi,
           (SELECT COUNT(*) FROM kamar WHERE kost_id = k.id AND status = 'Kosong') AS kamar_kosong
    FROM kost k
    JOIN users u ON k.pemilik_id = u.id
    ORDER BY k.created_at DESC
");


// LUNAS
$laporanLunas = Database::fetchAll("
    SELECT p.*, u.nama_lengkap AS penyewa,
           km.nomor_kamar,
           ks.nama_kost
    FROM pembayaran p
    JOIN kontrak k ON p.kontrak_id = k.id
    JOIN penyewa py ON k.penyewa_id = py.id
    JOIN users u ON py.user_id = u.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE p.status = 'Lunas'
    ORDER BY p.created_at DESC
");

// BELUM LUNAS
$laporanBelumLunas = Database::fetchAll("
    SELECT p.*, u.nama_lengkap AS penyewa,
           km.nomor_kamar,
           ks.nama_kost
    FROM pembayaran p
    JOIN kontrak k ON p.kontrak_id = k.id
    JOIN penyewa py ON k.penyewa_id = py.id
    JOIN users u ON py.user_id = u.id
    JOIN kamar km ON k.kamar_id = km.id
    JOIN kost ks ON km.kost_id = ks.id
    WHERE p.status IN ('Belum Bayar','Terlambat','Menunggu')
    ORDER BY p.created_at DESC
");

/* ======================
   HTML PDF
====================== */
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #eee; }
    </style>
</head>
<body>

<h3>Laporan Data Kost</h3>
<table>
    <tr>
        <th>Nama Kost</th>
        <th>Pemilik</th>
        <th>Alamat</th>
        <th>Total Kamar</th>
        <th>Terisi</th>
        <th>Kosong</th>
    </tr>
    <?php foreach ($laporanKost as $k): ?>
    <tr>
        <td><?= $k['nama_kost'] ?></td>
        <td><?= $k['pemilik'] ?></td>
        <td><?= $k['alamat'] ?></td>
        <td><?= $k['total_kamar'] ?></td>
        <td><?= $k['kamar_terisi'] ?></td>
        <td><?= $k['kamar_kosong'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<br>


<h2>Laporan Pembayaran Kost</h2>

<h3>Pembayaran Belum Lunas / Tunggakan</h3>
<table>
    <tr>
        <th>Penyewa</th>
        <th>Kost</th>
        <th>Kamar</th>
        <th>Periode</th>
        <th>Jumlah</th>
        <th>Status</th>
    </tr>
    <?php foreach ($laporanBelumLunas as $b): ?>
    <tr>
        <td><?= $b['penyewa'] ?></td>
        <td><?= $b['nama_kost'] ?></td>
        <td><?= $b['nomor_kamar'] ?></td>
        <td><?= $b['periode_bulan'] ?></td>
        <td>Rp <?= number_format($b['jumlah'],0,',','.') ?></td>
        <td><?= $b['status'] ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<h3>Pembayaran Lunas</h3>
<table>
    <tr>
        <th>Penyewa</th>
        <th>Kost</th>
        <th>Kamar</th>
        <th>Periode</th>
        <th>Jumlah</th>
        <th>Tanggal Bayar</th>
    </tr>
    <?php foreach ($laporanLunas as $b): ?>
    <tr>
        <td><?= $b['penyewa'] ?></td>
        <td><?= $b['nama_kost'] ?></td>
        <td><?= $b['nomor_kamar'] ?></td>
        <td><?= $b['periode_bulan'] ?></td>
        <td>Rp <?= number_format($b['jumlah'],0,',','.') ?></td>
        <td><?= date('d/m/Y', strtotime($b['tanggal_bayar'])) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
<?php
$html = ob_get_clean();

/* ======================
   GENERATE PDF
====================== */
$pdf = new Dompdf();
$pdf->loadHtml($html);
$pdf->setPaper('A4', 'portrait');
$pdf->render();
$pdf->stream('laporan_pembayaran.pdf', ['Attachment' => true]);
exit;

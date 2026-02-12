<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pembeli');
$pageTitle = 'Perpanjang Kontrak';
$userId = Auth::id();

$kontrakId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$kontrak = Database::fetchOne("
    SELECT kt.*, k.harga_sewa, k.nomor_kamar, ks.nama_kost
    FROM kontrak kt
    JOIN penyewa p ON kt.penyewa_id = p.id
    JOIN kamar k ON kt.kamar_id = k.id
    JOIN kost ks ON k.kost_id = ks.id
    WHERE kt.id = $kontrakId AND p.user_id = $userId AND kt.status = 'Aktif'
");

if (!$kontrak) {
    header('Location: /coba_kost/pembeli/kontrak.php');
    exit;
}

// Handle Perpanjang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tambahan_bulan = (int)$_POST['tambahan_bulan'];
    $harga_sewa = $kontrak['harga_sewa'];
    $total_tambahan = $harga_sewa * $tambahan_bulan;
    
    // Update tanggal berakhir
    $tgl_berakhir_lama = new DateTime($kontrak['tanggal_berakhir']);
    $tgl_berakhir_baru = clone $tgl_berakhir_lama;
    $tgl_berakhir_baru->modify("+$tambahan_bulan months");
    
    $durasi_baru = $kontrak['durasi_bulan'] + $tambahan_bulan;
    $total_harga_baru = $kontrak['total_harga'] + $total_tambahan;
    
    Database::query("
        UPDATE kontrak 
        SET tanggal_berakhir = '{$tgl_berakhir_baru->format('Y-m-d')}',
            durasi_bulan = $durasi_baru,
            total_harga = $total_harga_baru
        WHERE id = $kontrakId
    ");
    
    // Tambah pembayaran baru
    $bulan_ke_terakhir = Database::fetchOne("
        SELECT MAX(bulan_ke) as max_bulan FROM pembayaran WHERE kontrak_id = $kontrakId
    ")['max_bulan'];
    
    for ($i = 1; $i <= $tambahan_bulan; $i++) {
        $bulan_ke = $bulan_ke_terakhir + $i;
        $periode = clone $tgl_berakhir_lama;
        $periode->modify("+$i months");
        $periode_bulan = $periode->format('F Y');
        
        Database::query("
            INSERT INTO pembayaran (kontrak_id, bulan_ke, periode_bulan, jumlah, status)
            VALUES ($kontrakId, $bulan_ke, '$periode_bulan', $harga_sewa, 'Belum Bayar')
        ");
    }
    
    header('Location: /coba_kost/pembeli/pembayaran.php?success=extended');
    exit;
}

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="card">
    <div class="card-header"><i class="fas fa-plus-circle"></i> Perpanjang Kontrak</div>
    <div class="card-body">
        <div class="alert alert-info">
            <strong>Kost:</strong> <?php echo htmlspecialchars($kontrak['nama_kost']); ?><br>
            <strong>Kamar:</strong> <?php echo htmlspecialchars($kontrak['nomor_kamar']); ?><br>
            <strong>Berakhir:</strong> <?php echo date('d/m/Y', strtotime($kontrak['tanggal_berakhir'])); ?>
        </div>
        
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Tambahan Bulan</label>
                <input type="number" class="form-control" name="tambahan_bulan" id="tambahan_bulan" min="1" max="12" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga per Bulan</label>
                <input type="text" class="form-control" value="Rp <?php echo number_format($kontrak['harga_sewa'], 0, ',', '.'); ?>" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Berakhir Baru</label>
                <input type="text" class="form-control" id="tgl_berakhir_baru" readonly>
            </div>
            <div class="alert alert-warning">
                <strong>Total Pembayaran Tambahan:</strong> <span id="total_display">Rp 0</span>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check"></i> Konfirmasi Perpanjang
            </button>
            <a href="/coba_kost/pembeli/kontrak.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<script>
const hargaSewa = <?php echo $kontrak['harga_sewa']; ?>;
const tglBerakhir = new Date('<?php echo $kontrak['tanggal_berakhir']; ?>');

document.getElementById('tambahan_bulan').addEventListener('change', function() {
    const bulan = parseInt(this.value);
    const total = hargaSewa * bulan;
    document.getElementById('total_display').textContent = 'Rp ' + total.toLocaleString('id-ID');
    
    const tglBaru = new Date(tglBerakhir);
    tglBaru.setMonth(tglBaru.getMonth() + bulan);
    document.getElementById('tgl_berakhir_baru').value = tglBaru.toLocaleDateString('id-ID');
});
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>

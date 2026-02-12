<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';
require_once __DIR__ . '/../lib/request.php'; // Tambahkan ini

Auth::requireRole('pembeli');
$kostId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$kost = Database::fetchOne("
    SELECT k.*, u.nama_lengkap as pemilik, u.no_hp as pemilik_hp 
    FROM kost k 
    JOIN users u ON k.pemilik_id = u.id 
    WHERE k.id = $kostId
");

if (!$kost) {
    header('Location: /coba_kost/pembeli/kost.php');
    exit;
}

$pageTitle = $kost['nama_kost'];
$kamarList = Database::fetchAll("SELECT * FROM kamar WHERE kost_id = $kostId ORDER BY status, harga_sewa");
$fasilitasList = Database::fetchAll("SELECT * FROM fasilitas_kost WHERE kost_id = $kostId");
$aturanList = Database::fetchAll("SELECT * FROM aturan_kost WHERE kost_id = $kostId");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request') {
    require_once __DIR__ . '/../lib/request.php'; // Pastikan library request dimuat
    
    $kamar_id = (int)$_POST['kamar_id'];
    $result = RequestSewa::buatRequest($kamar_id);
    
    if ($result['success']) {
        header('Location: /coba_kost/pembeli/request.php?success=requested');
    } else {
        $error = $result['message'];
    }
    exit;
}

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($error)): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <h3><?php echo htmlspecialchars($kost['nama_kost']); ?></h3>
        <p class="text-muted">
            <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($kost['alamat']); ?>, <?php echo htmlspecialchars($kost['kota']); ?>
        </p>
        <p><?php echo htmlspecialchars($kost['deskripsi']); ?></p>
        <hr>
        <p><strong>Pemilik:</strong> <?php echo htmlspecialchars($kost['pemilik']); ?></p>
        <p><strong>Kontak:</strong> <?php echo htmlspecialchars($kost['pemilik_hp']); ?></p>
    </div>
</div>

<?php if (!empty($fasilitasList)): ?>
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-list"></i> Fasilitas</div>
    <div class="card-body">
        <ul>
            <?php foreach ($fasilitasList as $f): ?>
            <li><?php echo htmlspecialchars($f['nama_fasilitas']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($aturanList)): ?>
<div class="card mb-4">
    <div class="card-header"><i class="fas fa-gavel"></i> Aturan Kost</div>
    <div class="card-body">
        <ul>
            <?php foreach ($aturanList as $a): ?>
            <li><?php echo htmlspecialchars($a['aturan']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><i class="fas fa-door-open"></i> Daftar Kamar</div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($kamarList as $kamar): ?>
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5>Kamar <?php echo htmlspecialchars($kamar['nomor_kamar']); ?></h5>
                        <p class="kost-price">Rp<?php echo number_format($kamar['harga_sewa'], 0, ',', '.'); ?>/bulan</p>
                        <p><small><?php echo htmlspecialchars($kamar['fasilitas']); ?></small></p>
                        <?php if (!empty($kamar['foto'])): ?>
                        <img src="/coba_kost/uploads/kamar/<?php echo htmlspecialchars($kamar['foto']); ?>" 
                             class="img-fluid mb-2" style="height:150px; object-fit:cover;">
                        <?php endif; ?>
                        <span class="badge badge-status badge-<?php echo strtolower($kamar['status']); ?> mb-2">
                            <?php echo $kamar['status']; ?>
                        </span>
                        <?php if ($kamar['status'] === 'Kosong'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="action" value="request">
                            <input type="hidden" name="kamar_id" value="<?php echo $kamar['id']; ?>">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-paper-plane"></i> Ajukan Request
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
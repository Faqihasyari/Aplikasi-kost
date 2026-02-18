<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pembeli');
$pageTitle = 'Cari Kost';

$dataKost = Database::fetchAll("
    SELECT k.*, u.nama_lengkap as pemilik,
           (SELECT COUNT(*) FROM kamar 
            WHERE kost_id = k.id AND status = 'Kosong') as kamar_tersedia
    FROM kost k
    JOIN users u ON k.pemilik_id = u.id
    ORDER BY k.created_at DESC
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="row">
    <?php if (empty($dataKost)): ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada kost tersedia.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($dataKost as $kost): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">

                    <!-- FOTO KOST -->
                    <?php if (!empty($kost['foto'])): ?>
                        <img src="/coba_kost/uploads/kamar/<?php echo htmlspecialchars($kost['foto']); ?>"
                             class="card-img-top"
                             style="height:200px; object-fit:cover;"
                             alt="Foto Kost">
                    <?php else: ?>
                        <img src="/coba_kost/assets/img/default-kost.jpg"
                             class="card-img-top"
                             style="height:200px; object-fit:cover;"
                             alt="Default Kost">
                    <?php endif; ?>

                    <div class="card-body d-flex flex-column">

                        <h5 class="card-title">
                            <?php echo htmlspecialchars($kost['nama_kost']); ?>
                        </h5>

                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($kost['alamat']); ?>
                        </p>

                        <p class="mb-3 flex-grow-1">
                            <?php echo htmlspecialchars(substr($kost['deskripsi'], 0, 100)); ?>...
                        </p>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-info">
                                <?php echo $kost['kamar_tersedia']; ?> kamar tersedia
                            </span>

                            <a href="/coba_kost/pembeli/detail_kost.php?id=<?php echo $kost['id']; ?>"
                               class="btn btn-primary btn-sm">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

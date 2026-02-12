<?php

/**
 * Pemilik - Kelola Kamar
 * File: pemilik/kamar.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');

$pageTitle = 'Kelola Kamar';
$userId = Auth::id();

// Get Kost List
$kostList = Database::fetchAll("SELECT * FROM kost WHERE pemilik_id = $userId");

// Handle Add Kamar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $kost_id = (int)$_POST['kost_id'];
    $nomor_kamar = Database::escape($_POST['nomor_kamar']);
    $harga_sewa = (float)$_POST['harga_sewa'];
    $fasilitas = Database::escape($_POST['fasilitas']);
    $fotoName = null;

    if (isset($_FILES['foto_kamar']) && $_FILES['foto_kamar']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['foto_kamar']['name'], PATHINFO_EXTENSION));

        // validasi ekstensi
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $fotoName = 'kamar_' . time() . '.' . $ext;

            $uploadDir = __DIR__ . '/../uploads/kamar/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            move_uploaded_file(
                $_FILES['foto_kamar']['tmp_name'],
                $uploadDir . $fotoName
            );
        }
    }

    $sql = "INSERT INTO kamar (kost_id, nomor_kamar, harga_sewa, fasilitas, status, foto) 
            VALUES ($kost_id, '$nomor_kamar', $harga_sewa, '$fasilitas', 'Kosong', '$fotoName')";

    Database::query($sql);
    header('Location: /coba_kost/pemilik/kamar.php?success=added');
    exit;
}

// Handle Edit Kamar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['id'];
    $nomor_kamar = Database::escape($_POST['nomor_kamar']);
    $harga_sewa = (float)$_POST['harga_sewa'];
    $status = Database::escape($_POST['status']);
    $fasilitas = Database::escape($_POST['fasilitas']);

    // Ambil foto lama
    $old = Database::fetchAll("SELECT foto FROM kamar WHERE id = $id");
    $fotoName = $old['foto'];

    // Kalau upload foto baru
    if (isset($_FILES['foto_kamar']) && $_FILES['foto_kamar']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['foto_kamar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($ext, $allowed)) {
            $fotoName = 'kamar_' . time() . '.' . $ext;

            $uploadDir = __DIR__ . '/../uploads/kamar/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            move_uploaded_file(
                $_FILES['foto_kamar']['tmp_name'],
                $uploadDir . $fotoName
            );

            // Hapus foto lama
            if (!empty($old['foto']) && file_exists($uploadDir . $old['foto'])) {
                unlink($uploadDir . $old['foto']);
            }
        }
    }

    $sql = "UPDATE kamar SET
            nomor_kamar = '$nomor_kamar',
            harga_sewa = $harga_sewa,
            status = '$status',
            fasilitas = '$fasilitas',
            foto = " . ($fotoName ? "'$fotoName'" : "NULL") . "
            WHERE id = $id";

    Database::query($sql);
    header('Location: /coba_kost/pemilik/kamar.php?success=updated');
    exit;
}


// Handle Delete Kamar
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    Database::query("DELETE FROM kamar WHERE id = $id");
    header('Location: /coba_kost/pemilik/kamar.php?success=deleted');
    exit;
}

// Get Kamar with filter
$filter = isset($_GET['kost_id']) ? (int)$_GET['kost_id'] : 0;
$whereClause = $filter > 0 ? "AND k.kost_id = $filter" : "";

$dataKamar = Database::fetchAll("
    SELECT k.*, ks.nama_kost
    FROM kamar k
    JOIN kost ks ON k.kost_id = ks.id
    WHERE ks.pemilik_id = $userId $whereClause
    ORDER BY ks.nama_kost, k.nomor_kamar
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?php
        if ($_GET['success'] === 'added') echo 'Kamar berhasil ditambahkan!';
        if ($_GET['success'] === 'updated') echo 'Kamar berhasil diupdate!';
        if ($_GET['success'] === 'deleted') echo 'Kamar berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-10">
                <select name="kost_id" class="form-select">
                    <option value="0">Semua Kost</option>
                    <?php foreach ($kostList as $kost): ?>
                        <option value="<?php echo $kost['id']; ?>" <?php echo $filter == $kost['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($kost['nama_kost']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-door-open"></i> Daftar Kamar</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKamarModal">
            <i class="fas fa-plus"></i> Tambah Kamar
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($dataKamar)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada kamar. Silakan tambah kamar baru.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Kost</th>
                            <th>No. Kamar</th>
                            <th>Harga Sewa</th>
                            <th>Fasilitas</th>
                            <th>Status</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKamar as $kamar): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kamar['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($kamar['nomor_kamar']); ?></td>
                                <td>Rp <?php echo number_format($kamar['harga_sewa'], 0, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($kamar['fasilitas']); ?></td>
                                <td>
                                    <span class="badge badge-status badge-<?php echo strtolower($kamar['status']); ?>">
                                        <?php echo $kamar['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($kamar['foto']): ?>
                                        <img src="/coba_kost/uploads/kamar/<?php echo $kamar['foto']; ?>"
                                            width="80" class="img-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        onclick="editKamar(<?php echo htmlspecialchars(json_encode($kamar)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($kamar['status'] === 'Kosong'): ?>
                                        <a href="?delete=<?php echo $kamar['id']; ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirmDelete('Hapus kamar ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Kamar Modal -->
<div class="modal fade" id="addKamarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kamar Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- ⬇️ DI SINI -->
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kost</label>
                        <select class="form-select" name="kost_id" required>
                            <option value="">Pilih Kost</option>
                            <?php foreach ($kostList as $kost): ?>
                                <option value="<?php echo $kost['id']; ?>">
                                    <?php echo htmlspecialchars($kost['nama_kost']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Kamar</label>
                        <input type="text" class="form-control" name="nomor_kamar" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Harga Sewa (per bulan)</label>
                        <input type="number" class="form-control" name="harga_sewa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea class="form-control" name="fasilitas" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Kamar</label>
                        <input type="file" class="form-control" name="foto_kamar" accept="image/*">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
            <!-- ⬆️ SAMPAI SINI -->

        </div>
    </div>
</div>


<!-- Edit Kamar Modal -->
<div class="modal fade" id="editKamarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kamar</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Kamar</label>
                        <input type="text" class="form-control" name="nomor_kamar" id="edit_nomor_kamar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Sewa (per bulan)</label>
                        <input type="number" class="form-control" name="harga_sewa" id="edit_harga_sewa" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" id="edit_status" required>
                            <option value="Kosong">Kosong</option>
                            <option value="Terisi">Terisi</option>
                            <option value="Maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fasilitas</label>
                        <textarea class="form-control" name="fasilitas" id="edit_fasilitas" rows="3"></textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ganti Foto (opsional)</label>
                    <input type="file" class="form-control" name="foto_kamar" accept="image/*">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editKamar(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nomor_kamar').value = data.nomor_kamar;
        document.getElementById('edit_harga_sewa').value = data.harga_sewa;
        document.getElementById('edit_status').value = data.status;
        document.getElementById('edit_fasilitas').value = data.fasilitas;
        new bootstrap.Modal(document.getElementById('editKamarModal')).show();
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
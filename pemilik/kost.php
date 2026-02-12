<?php

/**
 * Pemilik - Kelola Kost
 * File: pemilik/kost.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');

$pageTitle = 'Kelola Kost';
$userId = Auth::id();

// Handle Add Kost
// Handle Add Kost
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $nama_kost = Database::escape($_POST['nama_kost']);
    $alamat    = Database::escape($_POST['alamat']);
    $kota      = Database::escape($_POST['kota']);
    $deskripsi = Database::escape($_POST['deskripsi']);

    // ===== HANDLE FOTO =====
    $fotoName = null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array(strtolower($ext), $allowed)) {
            die('Format foto tidak didukung!');
        }

        $fotoName = 'kost_' . time() . '_' . rand(100, 999) . '.' . $ext;
        $uploadPath = __DIR__ . '/../uploads/kamar/' . $fotoName;

        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath);
    }

    $sql = "INSERT INTO kost (pemilik_id, nama_kost, alamat, kota, deskripsi, foto)
            VALUES ($userId, '$nama_kost', '$alamat', '$kota', '$deskripsi', '$fotoName')";

    Database::query($sql);
    header('Location: /coba_kost/pemilik/kost.php?success=added');
    exit;
}


// Handle Edit Kost
// Handle Edit Kost
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {
    $id = (int)$_POST['id'];
    $nama_kost = Database::escape($_POST['nama_kost']);
    $alamat    = Database::escape($_POST['alamat']);
    $kota      = Database::escape($_POST['kota']);
    $deskripsi = Database::escape($_POST['deskripsi']);

    // Ambil foto lama
    $old = Database::fetchOne("SELECT foto FROM kost WHERE id = $id AND pemilik_id = $userId");
    $fotoName = $old['foto'];

    // Jika upload foto baru
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($ext, $allowed)) {
            die('Format foto tidak didukung!');
        }

        $fotoName = 'kost_' . time() . '_' . rand(100,999) . '.' . $ext;
        $uploadDir = __DIR__ . '/../uploads/kamar/';

        // Upload foto baru
        move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName);

        // Hapus foto lama
        if (!empty($old['foto']) && file_exists($uploadDir . $old['foto'])) {
            unlink($uploadDir . $old['foto']);
        }
    }

    $sql = "UPDATE kost SET 
            nama_kost = '$nama_kost',
            alamat = '$alamat',
            kota = '$kota',
            deskripsi = '$deskripsi',
            foto = " . ($fotoName ? "'$fotoName'" : "NULL") . "
            WHERE id = $id AND pemilik_id = $userId";

    Database::query($sql);
    header('Location: /coba_kost/pemilik/kost.php?success=updated');
    exit;
}


// Handle Delete Kost
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    Database::query("DELETE FROM kost WHERE id = $id AND pemilik_id = $userId");
    header('Location: /coba_kost/pemilik/kost.php?success=deleted');
    exit;
}

// Get Kost
$dataKost = Database::fetchAll("SELECT * FROM kost WHERE pemilik_id = $userId ORDER BY created_at DESC");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i>
        <?php
        if ($_GET['success'] === 'added') echo 'Kost berhasil ditambahkan!';
        if ($_GET['success'] === 'updated') echo 'Kost berhasil diupdate!';
        if ($_GET['success'] === 'deleted') echo 'Kost berhasil dihapus!';
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-building"></i> Daftar Kost</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addKostModal">
            <i class="fas fa-plus"></i> Tambah Kost
        </button>
    </div>
    <div class="card-body">
        <?php if (empty($dataKost)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Belum ada kost. Silakan tambah kost baru.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kost</th>
                            <th>Alamat</th>
                            <th>Kota</th>
                            <th>Tanggal Dibuat</th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dataKost as $kost): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($kost['nama_kost']); ?></td>
                                <td><?php echo htmlspecialchars($kost['alamat']); ?></td>
                                <td><?php echo htmlspecialchars($kost['kota']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($kost['created_at'])); ?></td>
                                <td>
                                    <?php if ($kost['foto']): ?>
                                        <img src="/coba_kost/uploads/kamar/<?php echo $kost['foto']; ?>"
                                            width="80" class="img-thumbnail">
                                    <?php else: ?>
                                        <span class="text-muted">Tidak ada</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button class="btn btn-warning btn-sm"
                                        onclick="editKost(<?php echo htmlspecialchars(json_encode($kost)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $kost['id']; ?>"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirmDelete('Hapus kost ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="/coba_kost/pemilik/kamar.php?kost_id=<?php echo $kost['id']; ?>"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-door-open"></i> Kamar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add Kost Modal -->
<div class="modal fade" id="addKostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kost Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kost</label>
                        <input type="text" class="form-control" name="nama_kost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" class="form-control" name="kota" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Foto Kost</label>
                        <input type="file" class="form-control" name="foto" accept="image/*" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Kost Modal -->
<div class="modal fade" id="editKostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Kost</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Kost</label>
                        <input type="text" class="form-control" name="nama_kost" id="edit_nama_kost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea class="form-control" name="alamat" id="edit_alamat" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kota</label>
                        <input type="text" class="form-control" name="kota" id="edit_kota" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
    <label class="form-label">Ganti Foto Kost (opsional)</label>
    <input type="file" class="form-control" name="foto" accept="image/*">
    <small class="text-muted">Kosongkan jika tidak ingin mengganti foto</small>
</div>

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
    function editKost(data) {
        document.getElementById('edit_id').value = data.id;
        document.getElementById('edit_nama_kost').value = data.nama_kost;
        document.getElementById('edit_alamat').value = data.alamat;
        document.getElementById('edit_kota').value = data.kota;
        document.getElementById('edit_deskripsi').value = data.deskripsi;
        new bootstrap.Modal(document.getElementById('editKostModal')).show();
    }
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>
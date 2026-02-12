<?php
/**
 * Pemilik - Kelola Fasilitas Kost
 * File: pemilik/fasilitas.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');

$pageTitle = 'Fasilitas Kost';
$userId = Auth::id();

// Get Kost List
$kostList = Database::fetchAll("SELECT * FROM kost WHERE pemilik_id = $userId");

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $kost_id = (int)$_POST['kost_id'];
    $nama_fasilitas = Database::escape($_POST['nama_fasilitas']);
    
    Database::query("INSERT INTO fasilitas_kost (kost_id, nama_fasilitas) VALUES ($kost_id, '$nama_fasilitas')");
    header('Location: /coba_kost/pemilik/fasilitas.php?success=added');
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    Database::query("DELETE FROM fasilitas_kost WHERE id = $id");
    header('Location: /coba_kost/pemilik/fasilitas.php?success=deleted');
    exit;
}

// Get Data
$dataFasilitas = Database::fetchAll("
    SELECT f.*, k.nama_kost
    FROM fasilitas_kost f
    JOIN kost k ON f.kost_id = k.id
    WHERE k.pemilik_id = $userId
    ORDER BY k.nama_kost, f.nama_fasilitas
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> 
        <?php echo $_GET['success'] === 'added' ? 'Fasilitas berhasil ditambahkan!' : 'Fasilitas berhasil dihapus!'; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list"></i> Fasilitas Kost</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Tambah Fasilitas
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kost</th>
                        <th>Fasilitas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataFasilitas as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nama_kost']); ?></td>
                            <td><?php echo htmlspecialchars($item['nama_fasilitas']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirmDelete()">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
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
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" name="nama_fasilitas" required>
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

<?php include __DIR__ . '/../layout/footer.php'; ?>

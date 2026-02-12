<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

Auth::requireRole('pemilik');
$pageTitle = 'Aturan Kost';
$userId = Auth::id();

$kostList = Database::fetchAll("SELECT * FROM kost WHERE pemilik_id = $userId");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $kost_id = (int)$_POST['kost_id'];
    $aturan = Database::escape($_POST['aturan']);
    Database::query("INSERT INTO aturan_kost (kost_id, aturan) VALUES ($kost_id, '$aturan')");
    header('Location: /coba_kost/pemilik/aturan.php?success=added');
    exit;
}

if (isset($_GET['delete'])) {
    Database::query("DELETE FROM aturan_kost WHERE id = " . (int)$_GET['delete']);
    header('Location: /coba_kost/pemilik/aturan.php?success=deleted');
    exit;
}

$dataAturan = Database::fetchAll("
    SELECT a.*, k.nama_kost FROM aturan_kost a
    JOIN kost k ON a.kost_id = k.id
    WHERE k.pemilik_id = $userId
    ORDER BY k.nama_kost
");

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> Aturan berhasil <?php echo $_GET['success'] === 'added' ? 'ditambahkan' : 'dihapus'; ?>!
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between">
        <span><i class="fas fa-gavel"></i> Aturan Kost</span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
            <i class="fas fa-plus"></i> Tambah Aturan
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Kost</th>
                        <th>Aturan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataAturan as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['nama_kost']); ?></td>
                            <td><?php echo htmlspecialchars($item['aturan']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger btn-sm"
                                   onclick="return confirmDelete()"><i class="fas fa-trash"></i></a>
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
                <h5 class="modal-title">Tambah Aturan</h5>
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
                                <option value="<?php echo $kost['id']; ?>"><?php echo htmlspecialchars($kost['nama_kost']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aturan</label>
                        <textarea class="form-control" name="aturan" rows="3" required></textarea>
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

<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/functions.php';

Auth::requireRole('admin');

$message = '';

if (isset($_POST['backup'])) {
    $result = Backup::run();
    
    if ($result['success']) {
        $message = "Database telah berhasil dibackup: " . $result['file'];
    } else {
        $message = "Backup database gagal!";
    }
}

$files = Backup::getFiles();

include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/sidebar.php';
?>

<div class="container-fluid px-4 py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold text-dark">Backup Database</h2>
            <p class="text-muted mb-0">Kelola dan download backup database Anda</p>
        </div>
        <form method="POST" class="mb-0">
            <button type="submit" name="backup" class="btn btn-primary btn-lg shadow-sm">
                <i class="fas fa-database me-2"></i>Backup Sekarang
            </button>
        </form>
    </div>

    <!-- Alert Message -->
    <?php if ($message): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Backup Files Card -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-archive me-2 text-primary"></i>Daftar File Backup
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (empty($files)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Belum ada file backup</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 ps-4 py-3">
                                    <i class="fas fa-file-alt me-2 text-muted"></i>Nama File
                                </th>
                                <th class="border-0 text-center py-3" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($files as $file): ?>
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-database text-primary me-3"></i>
                                            <span class="fw-medium"><?php echo $file; ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center py-3">
                                        <a href="download.php?file=<?php echo urlencode($file); ?>" 
                                           class="btn btn-sm btn-success rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-download me-1"></i>Download
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

</div>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3) !important;
    }
    
    .card {
        transition: all 0.3s ease;
    }
    
    .table tbody tr {
        transition: all 0.2s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }
    
    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(17, 153, 142, 0.3);
    }
    
    .alert-success {
        background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
        color: #4c51bf;
        border-left: 4px solid #667eea;
    }
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>
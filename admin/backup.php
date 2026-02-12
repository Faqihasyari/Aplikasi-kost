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

<div class="container mt-4">
    <h3>Backup Data</h3>

    <?php if ($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <button type="submit" name="backup" class="btn btn-primary">
            Backup Sekarang
        </button>
    </form>

    <hr>

    <h5>Daftar File Backup:</h5>
    <ul>
        <?php foreach ($files as $file): ?>
            <li><?php echo $file; ?></li>
        <?php endforeach; ?>
    </ul>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>

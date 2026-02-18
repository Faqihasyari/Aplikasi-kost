<?php
require_once __DIR__ . '/../lib/auth.php';

Auth::requireRole('admin');

if (!isset($_GET['file'])) {
    die('File tidak ditemukan');
}

$filename = basename($_GET['file']); // prevent path traversal
$filepath = __DIR__ . '/../backups/' . $filename;

// Validasi file
if (!file_exists($filepath) || pathinfo($filepath, PATHINFO_EXTENSION) !== 'sql') {
    die('File tidak valid');
}

// Header download
header('Content-Description: File Transfer');
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;

<?php
/**
 * Register Page
 * File: auth/register.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => $_POST['username'] ?? '',
        'password' => $_POST['password'] ?? '',
        'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
        'email' => $_POST['email'] ?? '',
        'no_hp' => $_POST['no_hp'] ?? '',
        'role' => 'pembeli' // Default role untuk registrasi adalah pembeli
    ];
    
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi
    if ($data['password'] !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak sama!';
    } elseif (strlen($data['password']) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        if (Auth::register($data)) {
            $success = 'Registrasi berhasil! Silakan login.';
        } else {
            $error = 'Username sudah digunakan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sewa Kost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/coba_kost/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card card" style="max-width: 500px;">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">DAFTAR AKUN</h3>
                    <p class="text-muted">Buat akun baru untuk mulai mencari kost</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" class="form-control" name="no_hp" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                        <small class="text-muted">Minimal 6 karakter</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus"></i> Daftar
                    </button>
                </form>
                
                <div class="text-center">
                    <p class="text-muted">Sudah punya akun?</p>
                    <a href="/coba_kost/auth/login.php" class="btn btn-outline-primary">
                        <i class="fas fa-sign-in-alt"></i> Login Sekarang
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

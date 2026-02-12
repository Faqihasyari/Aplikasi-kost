<?php
/**
 * Login Page
 * File: auth/login.php
 */

require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/database.php';

// Redirect jika sudah login
if (Auth::check()) {
    $role = Auth::role();
    header("Location: /coba_kost/$role/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (Auth::login($username, $password)) {
        $role = Auth::role();
        header("Location: /coba_kost/$role/dashboard.php");
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sewa Kost</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/coba_kost/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card card">
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fas fa-home fa-3x text-primary mb-3"></i>
                    <h3 class="fw-bold">SEWA KOST</h3>
                    <p class="text-muted">Silakan login untuk melanjutkan</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" class="form-control" name="username" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="text-center">
                    <p class="text-muted mb-2">Belum punya akun?</p>
                    <a href="/coba_kost/auth/register.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus"></i> Daftar Sekarang
                    </a>
                </div>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <small class="text-muted">Demo Login:</small><br>
                    <small>Admin: admin / admin123</small><br>
                    <small>Pemilik: pemilik1 / pemilik123</small><br>
                    <small>Pembeli: pembeli1 / pembeli123</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

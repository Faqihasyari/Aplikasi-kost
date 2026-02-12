<?php
/**
 * Sidebar Layout for Admin
 * File: layout/sidebar.php
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$role = Auth::role();
?>

<div class="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-home fa-2x"></i>
        <h3>SEWA KOST</h3>
        <small><?php echo strtoupper($role); ?> PANEL</small>
    </div>
    
    <ul class="sidebar-menu">
        <?php if ($role === 'admin'): ?>
            <!-- Menu Admin -->
            <li>
                <a href="/coba_kost/admin/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/admin/users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/admin/laporan.php" class="<?php echo $currentPage === 'laporan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-alt"></i>
                    <span>Laporan</span>
                </a>
            </li>
            <li>
    <a href="/coba_kost/admin/backup.php">
        <i class="fas fa-database"></i> Backup Data
    </a>
</li>

            
        <?php elseif ($role === 'pemilik'): ?>
            <!-- Menu Pemilik -->
            <li>
                <a href="/coba_kost/pemilik/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/kost.php" class="<?php echo $currentPage === 'kost.php' ? 'active' : ''; ?>">
                    <i class="fas fa-building"></i>
                    <span>Kelola Kost</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/kamar.php" class="<?php echo $currentPage === 'kamar.php' ? 'active' : ''; ?>">
                    <i class="fas fa-door-open"></i>
                    <span>Kelola Kamar</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/fasilitas.php" class="<?php echo $currentPage === 'fasilitas.php' ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Fasilitas</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/aturan.php" class="<?php echo $currentPage === 'aturan.php' ? 'active' : ''; ?>">
                    <i class="fas fa-gavel"></i>
                    <span>Aturan Kost</span>
                </a>
            </li>

            <?php if(Auth::hasRole('pemilik')): ?>
                <li><a href="/coba_kost/pemilik/request_sewa.php"><i class="fas fa-file-signature"></i> Request Penyewaan</a></li>
            <?php endif; ?>

            <li>
                <a href="/coba_kost/pemilik/penyewa_aktif.php" class="<?php echo $currentPage === 'penyewa_aktif.php' ? 'active' : ''; ?>">
                    <i class="fas fa-user-check"></i>
                    <span>Penyewa Aktif</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/riwayat_penyewa.php" class="<?php echo $currentPage === 'riwayat_penyewa.php' ? 'active' : ''; ?>">
                    <i class="fas fa-history"></i>
                    <span>Riwayat Penyewa</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/kontrak.php" class="<?php echo $currentPage === 'kontrak.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-contract"></i>
                    <span>Kontrak</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pemilik/pembayaran.php" class="<?php echo $currentPage === 'pembayaran.php' ? 'active' : ''; ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Pembayaran</span>
                </a>
            </li>
            
        <?php elseif ($role === 'pembeli'): ?>
            <!-- Menu Pembeli -->
            <li>
                <a href="/coba_kost/pembeli/dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pembeli/kost.php" class="<?php echo $currentPage === 'kost.php' ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i>
                    <span>Cari Kost</span>
                </a>
            </li>

            <?php if(Auth::hasRole('pembeli')): ?>
                <li><a href="/coba_kost/pembeli/request.php"><i class="fas fa-clock"></i> Request Saya</a></li>
            <?php endif; ?>

            <li>
                <a href="/coba_kost/pembeli/kontrak.php" class="<?php echo $currentPage === 'kontrak.php' ? 'active' : ''; ?>">
                    <i class="fas fa-file-contract"></i>
                    <span>Kontrak Saya</span>
                </a>
            </li>
            <li>
                <a href="/coba_kost/pembeli/pembayaran.php" class="<?php echo $currentPage === 'pembayaran.php' ? 'active' : ''; ?>">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>Pembayaran</span>
                </a>
            </li>
        <?php endif; ?>
        
        <li>
            <a href="/coba_kost/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <nav class="navbar-custom">
        <div class="navbar-title">
            <button class="btn btn-link d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h4><?php echo $pageTitle ?? 'Dashboard'; ?></h4>
        </div>
        <div class="navbar-user">
            <div class="user-info">
                <div class="user-name"><?php echo $user['nama_lengkap']; ?></div>
                <div class="user-role"><?php echo ucfirst($user['role']); ?></div>
            </div>
            <i class="fas fa-user-circle fa-2x text-secondary"></i>
        </div>
    </nav>
    
    <div class="content-area">

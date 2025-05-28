<div class="user-sidebar">
    <div class="sidebar-header">
        <h2>User Panel</h2>
    </div>
    
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
            <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
            <div class="user-role">User</div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="../user/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../user/bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'bookings.php' || basename($_SERVER['PHP_SELF']) === 'booking-detail.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Peminjaman Saya</span>
                </a>
            </li>
            <li>
                <a href="../user/create-booking.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'create-booking.php' ? 'active' : ''; ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Buat Peminjaman</span>
                </a>
            </li>
            <li>
                <a href="pengembalian.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/pengembalian/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-undo"></i>
                    <span>Pengembalian</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

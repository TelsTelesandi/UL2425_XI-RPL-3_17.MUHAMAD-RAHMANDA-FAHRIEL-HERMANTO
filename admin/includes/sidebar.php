<div class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
    </div>
    
    <div class="sidebar-user">
        <div class="user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="user-info">
            <div class="user-name"><?php echo $_SESSION['nama_lengkap']; ?></div>
            <div class="user-role">Administrator</div>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="/room-booking/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/room-booking/admin/rooms.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'rooms.php' || basename($_SERVER['PHP_SELF']) === 'room-form.php' ? 'active' : ''; ?>">
                    <i class="fas fa-door-open"></i>
                    <span>Kelola Ruangan</span>
                </a>
            </li>
            <li>
                <a href="/room-booking/admin/user/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/user/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    <span>Kelola User</span>
                </a>
            </li>
            <li>
                <a href="/room-booking/admin/bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'bookings.php' || basename($_SERVER['PHP_SELF']) === 'booking-detail.php' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span>Kelola Peminjaman</span>
                </a>
            </li>
            <li>
                <a href="/room-booking/admin/pengembalian/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/pengembalian/') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-undo"></i>
                    <span>Kelola Pengembalian</span>
                </a>
            </li>
            <li>
                <a href="/room-booking/admin/reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'reports.php' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span>Laporan</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$userRole = $isLoggedIn ? $_SESSION['role'] : '';

// Redirect to login if not logged in (except for login and register pages)
$publicPages = ['login.php', 'register.php', 'index.php'];
$currentPage = basename($_SERVER['PHP_SELF']);

if (!$isLoggedIn && !in_array($currentPage, $publicPages) && $currentPage !== 'index.php') {
    header("Location: login.php");
    exit();
}

// Redirect based on role
if ($isLoggedIn) {
    if ($userRole === 'admin' && $currentPage === 'index.php') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($userRole === 'user' && $currentPage === 'index.php') {
        header("Location: user/dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Peminjaman Ruangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1>Sistem Peminjaman Ruangan</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (!$isLoggedIn): ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    <?php else: ?>
                        <?php if ($userRole === 'admin'): ?>
                            <li><a href="admin/dashboard.php">Dashboard</a></li>
                        <?php else: ?>
                            <li><a href="user/dashboard.php">Dashboard</a></li>
                        <?php endif; ?>
                        <li><a href="../logout.php">Logout</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <main>
            <section class="hero">
                <div class="hero-content">
                    <h2>Selamat Datang di Sistem Peminjaman Ruangan</h2>
                    <p>Sistem ini memudahkan Anda untuk melakukan peminjaman ruangan secara online.</p>
                    <?php if (!$isLoggedIn): ?>
                        <div class="hero-buttons">
                            <a href="login.php" class="btn btn-primary">Login</a>
                            <a href="register.php" class="btn btn-secondary">Register</a>
                        </div>
                    <?php else: ?>
                        <div class="hero-buttons">
                            <?php if ($userRole === 'admin'): ?>
                                <a href="admin/dashboard.php" class="btn btn-primary">Dashboard Admin</a>
                            <?php else: ?>
                                <a href="user/dashboard.php" class="btn btn-primary">Dashboard User</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <section class="features">
                <div class="feature-card">
                    <i class="fas fa-door-open"></i>
                    <h3>Peminjaman Ruangan</h3>
                    <p>Pinjam ruangan dengan mudah dan cepat melalui sistem online.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Approval Cepat</h3>
                    <p>Proses persetujuan peminjaman yang cepat oleh admin.</p>
                </div>
                <div class="feature-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Rekap Data</h3>
                    <p>Lihat rekap data peminjaman dan pengembalian ruangan.</p>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Peminjaman Ruangan. All rights reserved.</p>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get booking statistics
$stats = getBookingStatistics();

// Get user statistics
$userStats = getUserStatistics();

// Get recent bookings (limit to 5)
$recentBookings = array_slice(getAllBookings(), 0, 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sistem Peminjaman Ruangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <?php include 'includes/header.php'; ?>
            
            <main class="admin-main">
                <h2>Dashboard</h2>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Peminjaman</h3>
                            <p><?php echo $stats['total']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Menunggu Approval</h3>
                            <p><?php echo $stats['pending']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Disetujui</h3>
                            <p><?php echo $stats['approved']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total User</h3>
                            <p><?php echo $userStats['total']; ?></p>
                        </div>
                    </div>
                </div>

                
                <div class="dashboard-sections">
                    <div class="dashboard-section">
                        <h3>Peminjaman Terbaru</h3>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Peminjam</th>
                                        <th>Ruangan</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentBookings)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data peminjaman.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td><?php echo $booking['peminjaman_id']; ?></td>
                                                <td><?php echo $booking['nama_lengkap']; ?></td>
                                                <td><?php echo $booking['nama_ruangan']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                                                <td><?php echo $booking['waktu_mulai'] . ' - ' . $booking['waktu_selesai']; ?></td>
                                                <td>
                                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                                        <?php echo $booking['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>" class="btn-icon" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="view-all">
                            <a href="bookings.php" class="btn btn-outline">Lihat Semua</a>
                        </div>
                    </div>
                    
                    <div class="dashboard-section">
                        <h3>Statistik User</h3>
                        <div class="user-stats">
                            <div class="user-stat-item">
                                <div class="stat-label">Administrator</div>
                                <div class="stat-value"><?php echo $userStats['admins']; ?></div>
                            </div>
                            <div class="user-stat-item">
                                <div class="stat-label">User Biasa</div>
                                <div class="stat-value"><?php echo $userStats['users']; ?></div>
                            </div>
                            <div class="user-stat-item">
                                <div class="stat-label">Siswa</div>
                                <div class="stat-value"><?php echo $userStats['siswa']; ?></div>
                            </div>
                            <div class="user-stat-item">
                                <div class="stat-label">Guru</div>
                                <div class="stat-value"><?php echo $userStats['guru']; ?></div>
                            </div>
                        </div>
                        <div class="view-all">
                            <a href="users.php" class="btn btn-outline">Kelola User</a>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>

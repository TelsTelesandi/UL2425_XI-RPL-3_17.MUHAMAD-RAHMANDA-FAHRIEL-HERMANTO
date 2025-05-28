<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Get user's bookings
$userId = $_SESSION['user_id'];
$userBookings = getBookingsByUserId($userId);

// Get recent bookings (limit to 5)
$recentBookings = array_slice($userBookings, 0, 5);

// Count bookings by status
$pendingCount = 0;
$approvedCount = 0;
$rejectedCount = 0;

foreach ($userBookings as $booking) {
    if ($booking['status'] === 'MENUNGGU') {
        $pendingCount++;
    } elseif ($booking['status'] === 'DITERIMA') {
        $approvedCount++;
    } elseif ($booking['status'] === 'DITOLAK') {
        $rejectedCount++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Sistem Peminjaman Ruangan</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="user-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="user-content">
            <?php include 'includes/header.php'; ?>
            
            <main class="user-main">
                <h2>Dashboard</h2>
                
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Peminjaman</h3>
                            <p><?php echo count($userBookings); ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Menunggu Approval</h3>
                            <p><?php echo $pendingCount; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon approved">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Disetujui</h3>
                            <p><?php echo $approvedCount; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon rejected">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Ditolak</h3>
                            <p><?php echo $rejectedCount; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="dashboard-sections">
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h3>Peminjaman Terbaru</h3>
                            <a href="create-booking.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Peminjaman
                            </a>
                        </div>
                        
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th>ID</th>
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
                                            <td colspan="6" class="text-center">Belum ada peminjaman.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td><?php echo $booking['peminjaman_id']; ?></td>
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
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/user.js"></script>
</body>
</html>

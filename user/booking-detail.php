<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}

$bookingId = $_GET['id'];
$userId = $_SESSION['user_id'];

// Get booking details
$booking = getBookingById($bookingId);

// Check if booking exists and belongs to the user
if (!$booking || $booking['user_id'] != $userId) {
    header("Location: bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman - Sistem Peminjaman Ruangan</title>
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
                <div class="page-header">
                    <h2>Detail Peminjaman</h2>
                    <a href="bookings.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <div class="detail-container">
                    <div class="detail-header">
                        <div class="detail-title">
                            <h3>Peminjaman #<?php echo $booking['peminjaman_id']; ?></h3>
                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-sections">
                        <div class="detail-section">
                            <h4>Informasi Ruangan</h4>
                            <div class="detail-item">
                                <div class="detail-label">Nama Ruangan</div>
                                <div class="detail-value"><?php echo $booking['nama_ruangan']; ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Informasi Peminjaman</h4>
                            <div class="detail-item">
                                <div class="detail-label">Tanggal</div>
                                <div class="detail-value"><?php echo date('d/m/Y', strtotime($booking['tanggal'])); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Waktu Mulai</div>
                                <div class="detail-value"><?php echo $booking['waktu_mulai']; ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Waktu Selesai</div>
                                <div class="detail-value"><?php echo $booking['waktu_selesai']; ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Durasi</div>
                                <div class="detail-value"><?php echo $booking['durasi_pinjam']; ?> jam</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Status</div>
                                <div class="detail-value">
                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo $booking['status']; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Status Peminjaman</h4>
                            <div class="status-timeline">
                                <div class="timeline-item <?php echo $booking['status'] !== '' ? 'active' : ''; ?>">
                                    <div class="timeline-icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h5>Peminjaman Dibuat</h5>
                                    </div>
                                </div>
                                
                                <div class="timeline-item <?php echo in_array($booking['status'], ['DITERIMA', 'DITOLAK']) ? 'active' : ''; ?>">
                                    <div class="timeline-icon">
                                        <i class="fas fa-clipboard-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h5>Peminjaman Diproses</h5>
                                    </div>
                                </div>
                                
                                <div class="timeline-item <?php echo $booking['status'] === 'DITERIMA' ? 'active' : ''; ?>">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h5>Peminjaman Disetujui</h5>
                                    </div>
                                </div>
                            </div>
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

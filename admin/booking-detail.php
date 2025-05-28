<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: bookings.php");
    exit();
}

$bookingId = $_GET['id'];
$booking = getBookingById($bookingId);

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

// Process status update
if (isset($_GET['status']) && !empty($_GET['status'])) {
    $status = $_GET['status'];
    
    if (in_array($status, ['DITERIMA', 'DITOLAK'])) {
        if (updateBookingStatus($bookingId, $status)) {
            $success = 'Status peminjaman berhasil diperbarui.';
            // Refresh booking data
            $booking = getBookingById($bookingId);
        } else {
            $error = 'Gagal memperbarui status peminjaman.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman - Sistem Peminjaman Ruangan</title>
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
                <div class="page-header">
                    <h2>Detail Peminjaman</h2>
                    <a href="bookings.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="detail-container">
                    <div class="detail-header">
                        <div class="detail-title">
                            <h3>Peminjaman #<?php echo $booking['peminjaman_id']; ?></h3>
                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                <?php echo $booking['status']; ?>
                            </span>
                        </div>
                        
                        <?php if ($booking['status'] === 'MENUNGGU'): ?>
                            <div class="detail-actions">
                                <a href="#" class="btn btn-success approve-btn">
                                    <i class="fas fa-check"></i> Setujui
                                </a>
                                <a href="#" class="btn btn-danger reject-btn">
                                    <i class="fas fa-times"></i> Tolak
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="detail-sections">
                        <div class="detail-section">
                            <h4>Informasi Peminjam</h4>
                            <div class="detail-item">
                                <div class="detail-label">Nama</div>
                                <div class="detail-value"><?php echo $booking['nama_lengkap']; ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value"><?php echo $booking['username']; ?></div>
                            </div>
                        </div>
                        
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
                    </div>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <!-- Approve Confirmation Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Persetujuan</h3>
            <p>Apakah Anda yakin ingin menyetujui peminjaman ini?</p>
            <div class="modal-actions">
                <button id="cancelApprove" class="btn btn-outline">Batal</button>
                <a id="confirmApprove" href="booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>&status=DITERIMA" class="btn btn-success">Setujui</a>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Penolakan</h3>
            <p>Apakah Anda yakin ingin menolak peminjaman ini?</p>
            <div class="modal-actions">
                <button id="cancelReject" class="btn btn-outline">Batal</button>
                <a id="confirmReject" href="booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>&status=DITOLAK" class="btn btn-danger">Tolak</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Approve confirmation
        const approveBtn = document.querySelector('.approve-btn');
        const approveModal = document.getElementById('approveModal');
        const cancelApprove = document.getElementById('cancelApprove');
        
        if (approveBtn) {
            approveBtn.addEventListener('click', (e) => {
                e.preventDefault();
                approveModal.style.display = 'flex';
            });
        }
        
        cancelApprove.addEventListener('click', () => {
            approveModal.style.display = 'none';
        });
        
        // Reject confirmation
        const rejectBtn = document.querySelector('.reject-btn');
        const rejectModal = document.getElementById('rejectModal');
        const cancelReject = document.getElementById('cancelReject');
        
        if (rejectBtn) {
            rejectBtn.addEventListener('click', (e) => {
                e.preventDefault();
                rejectModal.style.display = 'flex';
            });
        }
        
        cancelReject.addEventListener('click', () => {
            rejectModal.style.display = 'none';
        });
        
        // Close modals when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === approveModal) {
                approveModal.style.display = 'none';
            }
            if (e.target === rejectModal) {
                rejectModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>

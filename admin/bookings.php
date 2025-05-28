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

// Process status update
if (isset($_GET['id']) && !empty($_GET['id']) && isset($_GET['status']) && !empty($_GET['status'])) {
    $bookingId = $_GET['id'];
    $status = $_GET['status'];
    
    if (in_array($status, ['DITERIMA', 'DITOLAK'])) {
        if (updateBookingStatus($bookingId, $status)) {
            $success = 'Status peminjaman berhasil diperbarui.';
        } else {
            $error = 'Gagal memperbarui status peminjaman.';
        }
    }
}

// Get all bookings
$bookings = getAllBookings();

// Filter by status if provided
$statusFilter = $_GET['filter'] ?? '';
if (!empty($statusFilter)) {
    $filteredBookings = [];
    foreach ($bookings as $booking) {
        if ($booking['status'] === $statusFilter) {
            $filteredBookings[] = $booking;
        }
    }
    $bookings = $filteredBookings;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peminjaman - Sistem Peminjaman Ruangan</title>
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
                    <h2>Kelola Peminjaman</h2>
                    <div class="filter-container">
                        <label for="statusFilter">Filter:</label>
                        <select id="statusFilter" onchange="filterBookings(this.value)">
                            <option value="">Semua Status</option>
                            <option value="MENUNGGU" <?php echo $statusFilter === 'MENUNGGU' ? 'selected' : ''; ?>>Menunggu</option>
                            <option value="DITERIMA" <?php echo $statusFilter === 'DITERIMA' ? 'selected' : ''; ?>>Diterima</option>
                            <option value="DITOLAK" <?php echo $statusFilter === 'DITOLAK' ? 'selected' : ''; ?>>Ditolak</option>
                        </select>
                    </div>
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
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($bookings)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data peminjaman.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['peminjaman_id']; ?></td>
                                        <td><?php echo $booking['nama_lengkap']; ?></td>
                                        <td><?php echo $booking['nama_ruangan']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                                        <td><?php echo $booking['waktu_mulai'] . ' - ' . $booking['waktu_selesai']; ?></td>
                                        <td><?php echo $booking['durasi_pinjam']; ?> jam</td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                                <?php echo $booking['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>" class="btn-icon" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if ($booking['status'] === 'MENUNGGU'): ?>
                                                <a href="#" class="btn-icon approve-btn" data-id="<?php echo $booking['peminjaman_id']; ?>" title="Setujui">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <a href="#" class="btn-icon reject-btn" data-id="<?php echo $booking['peminjaman_id']; ?>" title="Tolak">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                <a id="confirmApprove" href="#" class="btn btn-success">Setujui</a>
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
                <a id="confirmReject" href="#" class="btn btn-danger">Tolak</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Filter bookings
        function filterBookings(status) {
            window.location.href = 'bookings.php' + (status ? '?filter=' + status : '');
        }
        
        // Approve confirmation
        const approveButtons = document.querySelectorAll('.approve-btn');
        const approveModal = document.getElementById('approveModal');
        const confirmApprove = document.getElementById('confirmApprove');
        const cancelApprove = document.getElementById('cancelApprove');
        
        approveButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const bookingId = button.getAttribute('data-id');
                confirmApprove.href = `bookings.php?id=${bookingId}&status=DITERIMA`;
                approveModal.style.display = 'flex';
            });
        });
        
        cancelApprove.addEventListener('click', () => {
            approveModal.style.display = 'none';
        });
        
        // Reject confirmation
        const rejectButtons = document.querySelectorAll('.reject-btn');
        const rejectModal = document.getElementById('rejectModal');
        const confirmReject = document.getElementById('confirmReject');
        const cancelReject = document.getElementById('cancelReject');
        
        rejectButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const bookingId = button.getAttribute('data-id');
                confirmReject.href = `bookings.php?id=${bookingId}&status=DITOLAK`;
                rejectModal.style.display = 'flex';
            });
        });
        
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

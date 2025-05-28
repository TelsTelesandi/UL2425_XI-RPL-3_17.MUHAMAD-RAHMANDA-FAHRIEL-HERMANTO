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

// Filter by status if provided
$statusFilter = $_GET['filter'] ?? '';
if (!empty($statusFilter)) {
    $filteredBookings = [];
    foreach ($userBookings as $booking) {
        if ($booking['status'] === $statusFilter) {
            $filteredBookings[] = $booking;
        }
    }
    $userBookings = $filteredBookings;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Saya - Sistem Peminjaman Ruangan</title>
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
                    <h2>Peminjaman Saya</h2>
                    <a href="create-booking.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Peminjaman
                    </a>
                </div>
                
                <div class="filter-container">
                    <label for="statusFilter">Filter:</label>
                    <select id="statusFilter" onchange="filterBookings(this.value)">
                        <option value="">Semua Status</option>
                        <option value="MENUNGGU" <?php echo $statusFilter === 'MENUNGGU' ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="DITERIMA" <?php echo $statusFilter === 'DITERIMA' ? 'selected' : ''; ?>>Diterima</option>
                        <option value="DITOLAK" <?php echo $statusFilter === 'DITOLAK' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($userBookings)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada peminjaman.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($userBookings as $booking): ?>
                                    <tr>
                                        <td><?php echo $booking['peminjaman_id']; ?></td>
                                        <td><?php echo $booking['nama_ruangan']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                                        <td><?php echo $booking['waktu_mulai'] . ' - ' . $booking['waktu_selesai']; ?></td>
                                        <td><?php echo $booking['durasi_pinjam']; ?> jam</td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                                <?php echo $booking['status']; ?>
                                            </span>
                                            <?php if ($booking['status'] === 'DITERIMA'): ?>
                                                <br>
                                                <span class="status-badge status-<?php echo strtolower($booking['return_status'] ?? 'belum_dikembalikan'); ?>">
                                                    <?php 
                                                        $returnStatus = [
                                                            'BELUM_DIKEMBALIKAN' => 'Belum Dikembalikan',
                                                            'PENGAJUAN' => 'Pengajuan Pengembalian',
                                                            'DIKEMBALIKAN' => 'Sudah Dikembalikan',
                                                            'DITOLAK' => 'Pengembalian Ditolak'
                                                        ];
                                                        echo $returnStatus[$booking['return_status'] ?? 'BELUM_DIKEMBALIKAN'];
                                                    ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>" class="btn-icon" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($booking['status'] === 'DITERIMA' && ($booking['return_status'] === 'BELUM_DIKEMBALIKAN' || $booking['return_status'] === 'DITOLAK')): ?>
                                                <a href="pengembalian.php" class="btn-icon" title="Ajukan Pengembalian">
                                                    <i class="fas fa-undo"></i>
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

    <script src="../assets/js/user.js"></script>
    <script>
        // Filter bookings
        function filterBookings(status) {
            window.location.href = 'bookings.php' + (status ? '?filter=' + status : '');
        }
    </script>
</body>
</html>

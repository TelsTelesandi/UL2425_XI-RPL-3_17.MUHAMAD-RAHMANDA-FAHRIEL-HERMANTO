<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all bookings for report
$bookings = getAllBookings();

// Filter by date range if provided
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$statusFilter = $_GET['status'] ?? '';

if (!empty($startDate) || !empty($endDate) || !empty($statusFilter)) {
    $filteredBookings = [];
    
    foreach ($bookings as $booking) {
        $bookingDate = strtotime($booking['tanggal']);
        $includeBooking = true;
        
        if (!empty($startDate) && strtotime($startDate) > $bookingDate) {
            $includeBooking = false;
        }
        
        if (!empty($endDate) && strtotime($endDate) < $bookingDate) {
            $includeBooking = false;
        }
        
        if (!empty($statusFilter) && $booking['status'] !== $statusFilter) {
            $includeBooking = false;
        }
        
        if ($includeBooking) {
            $filteredBookings[] = $booking;
        }
    }
    
    $bookings = $filteredBookings;
}

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_peminjaman_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV header
    fputcsv($output, ['ID', 'Peminjam', 'Ruangan', 'Tanggal', 'Waktu Mulai', 'Waktu Selesai', 'Durasi', 'Status']);
    
    // CSV data
    foreach ($bookings as $booking) {
        fputcsv($output, [
            $booking['peminjaman_id'],
            $booking['nama_lengkap'],
            $booking['nama_ruangan'],
            $booking['tanggal'],
            $booking['waktu_mulai'],
            $booking['waktu_selesai'],
            $booking['durasi_pinjam'] . ' jam',
            $booking['status']
        ]);
    }
    
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Sistem Peminjaman Ruangan</title>
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
                    <h2>Laporan Peminjaman</h2>
                    <div class="export-btn">
                        <a href="reports.php?export=csv<?php echo !empty($startDate) ? '&start_date=' . $startDate : ''; ?><?php echo !empty($endDate) ? '&end_date=' . $endDate : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?>" class="btn btn-primary">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                
                <div class="filter-form">
                    <form action="reports.php" method="get">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_date">Tanggal Mulai</label>
                                <input type="date" id="start_date" name="start_date" value="<?php echo $startDate; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="end_date">Tanggal Akhir</label>
                                <input type="date" id="end_date" name="end_date" value="<?php echo $endDate; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="MENUNGGU" <?php echo $statusFilter === 'MENUNGGU' ? 'selected' : ''; ?>>Menunggu</option>
                                    <option value="DITERIMA" <?php echo $statusFilter === 'DITERIMA' ? 'selected' : ''; ?>>Diterima</option>
                                    <option value="DITOLAK" <?php echo $statusFilter === 'DITOLAK' ? 'selected' : ''; ?>>Ditolak</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="reports.php" class="btn btn-outline">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="report-summary">
                    <div class="summary-card">
                        <div class="summary-title">Total Peminjaman</div>
                        <div class="summary-value"><?php echo count($bookings); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Menunggu</div>
                        <div class="summary-value"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'MENUNGGU'; })); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Diterima</div>
                        <div class="summary-value"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'DITERIMA'; })); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Ditolak</div>
                        <div class="summary-value"><?php echo count(array_filter($bookings, function($b) { return $b['status'] === 'DITOLAK'; })); ?></div>
                    </div>
                </div>
                
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

    <script src="../assets/js/admin.js"></script>
</body>
</html>

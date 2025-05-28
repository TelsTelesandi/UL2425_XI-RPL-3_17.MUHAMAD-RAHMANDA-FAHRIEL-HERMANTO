<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Get return statistics
$returnStats = getReturnStatistics();

// Get all returns for report
$returns = getAllReturns();

// Filter by date range if provided
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$statusFilter = $_GET['status'] ?? '';

if (!empty($startDate) || !empty($endDate) || !empty($statusFilter)) {
    $filteredReturns = [];
    
    foreach ($returns as $return) {
        $returnDate = strtotime($return['tanggal']);
        $includeReturn = true;
        
        if (!empty($startDate) && strtotime($startDate) > $returnDate) {
            $includeReturn = false;
        }
        
        if (!empty($endDate) && strtotime($endDate) < $returnDate) {
            $includeReturn = false;
        }
        
        if (!empty($statusFilter) && $return['return_status'] !== $statusFilter) {
            $includeReturn = false;
        }
        
        if ($includeReturn) {
            $filteredReturns[] = $return;
        }
    }
    
    $returns = $filteredReturns;
}

// Export to CSV if requested
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_pengembalian_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV header
    fputcsv($output, ['ID', 'Peminjam', 'Ruangan', 'Tanggal Pinjam', 'Waktu', 'Batas Pengembalian', 'Status Pengembalian', 'Tanggal Dikembalikan']);
    
    // CSV data
    foreach ($returns as $return) {
        $returnDeadline = date('d/m/Y H:i', strtotime($return['tanggal'] . ' ' . $return['waktu_selesai']));
        $returnDate = isset($return['tanggal_dikembalikan']) ? date('d/m/Y H:i', strtotime($return['tanggal_dikembalikan'])) : '-';
        
        fputcsv($output, [
            $return['peminjaman_id'],
            $return['nama_lengkap'],
            $return['nama_ruangan'],
            $return['tanggal'],
            $return['waktu_mulai'] . ' - ' . $return['waktu_selesai'],
            $returnDeadline,
            $return['return_status'],
            $returnDate
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
    <title>Laporan Pengembalian - Sistem Peminjaman Ruangan</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <div class="admin-content">
            <?php include '../includes/header.php'; ?>
            
            <main class="admin-main">
                <div class="page-header">
                    <h2>Laporan Pengembalian</h2>
                    <div class="export-btn">
                        <a href="laporan.php?export=csv<?php echo !empty($startDate) ? '&start_date=' . $startDate : ''; ?><?php echo !empty($endDate) ? '&end_date=' . $endDate : ''; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?>" class="btn btn-primary">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                
                <div class="filter-form">
                    <form action="laporan.php" method="get">
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
                                <label for="status">Status Pengembalian</label>
                                <select id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="BELUM_DIKEMBALIKAN" <?php echo $statusFilter === 'BELUM_DIKEMBALIKAN' ? 'selected' : ''; ?>>Belum Dikembalikan</option>
                                    <option value="TERLAMBAT" <?php echo $statusFilter === 'TERLAMBAT' ? 'selected' : ''; ?>>Terlambat</option>
                                    <option value="DIKEMBALIKAN" <?php echo $statusFilter === 'DIKEMBALIKAN' ? 'selected' : ''; ?>>Sudah Dikembalikan</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="laporan.php" class="btn btn-outline">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="report-summary">
                    <div class="summary-card">
                        <div class="summary-title">Total Pengembalian</div>
                        <div class="summary-value"><?php echo count($returns); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Belum Dikembalikan</div>
                        <div class="summary-value"><?php echo count(array_filter($returns, function($r) { return $r['return_status'] === 'BELUM_DIKEMBALIKAN'; })); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Terlambat</div>
                        <div class="summary-value"><?php echo count(array_filter($returns, function($r) { return $r['return_status'] === 'TERLAMBAT'; })); ?></div>
                    </div>
                    
                    <div class="summary-card">
                        <div class="summary-title">Sudah Dikembalikan</div>
                        <div class="summary-value"><?php echo count(array_filter($returns, function($r) { return $r['return_status'] === 'DIKEMBALIKAN'; })); ?></div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Peminjam</th>
                                <th>Ruangan</th>
                                <th>Tanggal Pinjam</th>
                                <th>Waktu</th>
                                <th>Batas Pengembalian</th>
                                <th>Status Pengembalian</th>
                                <th>Tanggal Dikembalikan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returns)): ?>
                                <tr>
                                    <td colspan="9" class="text-center">Tidak ada data pengembalian.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($returns as $return): ?>
                                    <tr>
                                        <td><?php echo $return['peminjaman_id']; ?></td>
                                        <td><?php echo htmlspecialchars($return['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($return['nama_ruangan']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($return['tanggal'])); ?></td>
                                        <td><?php echo $return['waktu_mulai'] . ' - ' . $return['waktu_selesai']; ?></td>
                                        <td>
                                            <?php echo date('d/m/Y H:i', strtotime($return['tanggal'] . ' ' . $return['waktu_selesai'])); ?>
                                        </td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace('_', '-', $return['return_status'])); ?>">
                                                <?php 
                                                switch($return['return_status']) {
                                                    case 'BELUM_DIKEMBALIKAN':
                                                        echo 'Belum Dikembalikan';
                                                        break;
                                                    case 'TERLAMBAT':
                                                        echo 'Terlambat';
                                                        break;
                                                    case 'DIKEMBALIKAN':
                                                        echo 'Sudah Dikembalikan';
                                                        break;
                                                    default:
                                                        echo $return['return_status'];
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            if (isset($return['tanggal_dikembalikan'])) {
                                                echo date('d/m/Y H:i', strtotime($return['tanggal_dikembalikan']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="detail.php?id=<?php echo $return['peminjaman_id']; ?>" class="btn-icon" title="Detail">
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
            
            <?php include '../includes/footer.php'; ?>
        </div>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>

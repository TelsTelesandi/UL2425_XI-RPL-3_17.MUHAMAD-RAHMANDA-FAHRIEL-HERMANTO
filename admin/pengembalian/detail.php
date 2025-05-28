<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

$success = '';
$error = '';

// Check if booking ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$bookingId = $_GET['id'];
$booking = getBookingById($bookingId);

if (!$booking) {
    header("Location: index.php");
    exit();
}

// Process return if requested
if (isset($_GET['action']) && $_GET['action'] === 'return') {
    if (processReturn($bookingId)) {
        $success = 'Pengembalian berhasil diproses.';
        // Refresh booking data
        $booking = getBookingById($bookingId);
    } else {
        $error = 'Gagal memproses pengembalian.';
    }
}

// Calculate return status
$returnDeadline = strtotime($booking['tanggal'] . ' ' . $booking['waktu_selesai']);
$currentTime = time();
$isLate = $currentTime > $returnDeadline;
$returnStatus = $booking['return_status'] ?? 'BELUM_DIKEMBALIKAN';

// Calculate late duration if applicable
$lateDuration = '';
if ($isLate && $returnStatus !== 'DIKEMBALIKAN') {
    $lateDiff = $currentTime - $returnDeadline;
    $lateHours = floor($lateDiff / 3600);
    $lateMinutes = floor(($lateDiff % 3600) / 60);
    $lateDuration = $lateHours . ' jam ' . $lateMinutes . ' menit';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengembalian - Sistem Peminjaman Ruangan</title>
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
                    <h2>Detail Pengembalian</h2>
                    <a href="index.php" class="btn btn-outline">
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
                            <div class="status-badges">
                                <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                    <?php echo $booking['status']; ?>
                                </span>
                                <span class="status-badge status-<?php echo strtolower(str_replace('_', '-', $returnStatus)); ?>">
                                    <?php 
                                    switch($returnStatus) {
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
                                            echo $returnStatus;
                                    }
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if ($returnStatus !== 'DIKEMBALIKAN'): ?>
                            <div class="detail-actions">
                                <a href="detail.php?id=<?php echo $booking['peminjaman_id']; ?>&action=return" class="btn btn-success return-btn">
                                    <i class="fas fa-undo"></i> Proses Pengembalian
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="detail-sections">
                        <div class="detail-section">
                            <h4>Informasi Peminjam</h4>
                            <div class="detail-item">
                                <div class="detail-label">Nama</div>
                                <div class="detail-value"><?php echo htmlspecialchars($booking['nama_lengkap']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value"><?php echo htmlspecialchars($booking['username']); ?></div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Informasi Ruangan</h4>
                            <div class="detail-item">
                                <div class="detail-label">Nama Ruangan</div>
                                <div class="detail-value"><?php echo htmlspecialchars($booking['nama_ruangan']); ?></div>
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
                        </div>
                        
                        <div class="detail-section">
                            <h4>Informasi Pengembalian</h4>
                            <div class="detail-item">
                                <div class="detail-label">Batas Pengembalian</div>
                                <div class="detail-value">
                                    <?php echo date('d/m/Y H:i', strtotime($booking['tanggal'] . ' ' . $booking['waktu_selesai'])); ?>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Status Pengembalian</div>
                                <div class="detail-value">
                                    <span class="status-badge status-<?php echo strtolower(str_replace('_', '-', $returnStatus)); ?>">
                                        <?php 
                                        switch($returnStatus) {
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
                                                echo $returnStatus;
                                        }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($isLate && $returnStatus !== 'DIKEMBALIKAN'): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Keterlambatan</div>
                                    <div class="detail-value" style="color: #dc3545; font-weight: bold;">
                                        <?php echo $lateDuration; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($booking['tanggal_dikembalikan'])): ?>
                                <div class="detail-item">
                                    <div class="detail-label">Tanggal Dikembalikan</div>
                                    <div class="detail-value">
                                        <?php echo date('d/m/Y H:i', strtotime($booking['tanggal_dikembalikan'])); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include '../includes/footer.php'; ?>
        </div>
    </div>

    <!-- Return Confirmation Modal -->
    <div id="returnModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Pengembalian</h3>
            <p>Apakah Anda yakin ingin memproses pengembalian ruangan ini?</p>
            <?php if ($isLate): ?>
                <p style="color: #dc3545;"><strong>Catatan:</strong> Pengembalian ini terlambat <?php echo $lateDuration; ?></p>
            <?php endif; ?>
            <div class="modal-actions">
                <button id="cancelReturn" class="btn btn-outline">Batal</button>
                <a href="detail.php?id=<?php echo $booking['peminjaman_id']; ?>&action=return" class="btn btn-success">Proses Pengembalian</a>
            </div>
        </div>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        // Return confirmation
        const returnBtn = document.querySelector('.return-btn');
        const returnModal = document.getElementById('returnModal');
        const cancelReturn = document.getElementById('cancelReturn');
        
        if (returnBtn) {
            returnBtn.addEventListener('click', (e) => {
                e.preventDefault();
                returnModal.style.display = 'flex';
            });
        }
        
        cancelReturn.addEventListener('click', () => {
            returnModal.style.display = 'none';
        });
        
        // Close modal when clicking outside
        window.addEventListener('click', (e) => {
            if (e.target === returnModal) {
                returnModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>

<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_GET['id'];
$user = getUserById($userId);

if (!$user) {
    header("Location: index.php");
    exit();
}

// Get user's bookings
$userBookings = getBookingsByUserId($userId);

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
    <title>Detail User - Sistem Peminjaman Ruangan</title>
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
                    <h2>Detail User</h2>
                    <div class="page-actions">
                        <a href="edit.php?id=<?php echo $user['user_id']; ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                        <a href="index.php" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="detail-container">
                    <div class="detail-header">
                        <div class="detail-title">
                            <h3><?php echo htmlspecialchars($user['nama_lengkap']); ?></h3>
                            <div class="user-badges">
                                <span class="badge badge-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                                <span class="badge badge-<?php echo $user['jenis_pengguna']; ?>">
                                    <?php echo ucfirst($user['jenis_pengguna']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-sections">
                        <div class="detail-section">
                            <h4>Informasi User</h4>
                            <div class="detail-item">
                                <div class="detail-label">ID User</div>
                                <div class="detail-value"><?php echo $user['user_id']; ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Username</div>
                                <div class="detail-value"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Nama Lengkap</div>
                                <div class="detail-value"><?php echo htmlspecialchars($user['nama_lengkap']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">ID Card</div>
                                <div class="detail-value"><?php echo htmlspecialchars($user['id_card']); ?></div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Jenis Pengguna</div>
                                <div class="detail-value">
                                    <span class="badge badge-<?php echo $user['jenis_pengguna']; ?>">
                                        <?php echo ucfirst($user['jenis_pengguna']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Role</div>
                                <div class="detail-value">
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <h4>Statistik Peminjaman</h4>
                            <div class="user-stats">
                                <div class="user-stat-item">
                                    <div class="stat-label">Total Peminjaman</div>
                                    <div class="stat-value"><?php echo count($userBookings); ?></div>
                                </div>
                                <div class="user-stat-item">
                                    <div class="stat-label">Menunggu</div>
                                    <div class="stat-value"><?php echo $pendingCount; ?></div>
                                </div>
                                <div class="user-stat-item">
                                    <div class="stat-label">Disetujui</div>
                                    <div class="stat-value"><?php echo $approvedCount; ?></div>
                                </div>
                                <div class="user-stat-item">
                                    <div class="stat-label">Ditolak</div>
                                    <div class="stat-value"><?php echo $rejectedCount; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <h4>Riwayat Peminjaman</h4>
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
                                                <td><?php echo htmlspecialchars($booking['nama_ruangan']); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($booking['tanggal'])); ?></td>
                                                <td><?php echo $booking['waktu_mulai'] . ' - ' . $booking['waktu_selesai']; ?></td>
                                                <td><?php echo $booking['durasi_pinjam']; ?> jam</td>
                                                <td>
                                                    <span class="status-badge status-<?php echo strtolower($booking['status']); ?>">
                                                        <?php echo $booking['status']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="../booking-detail.php?id=<?php echo $booking['peminjaman_id']; ?>" class="btn-icon" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            
            <?php include '../includes/footer.php'; ?>
        </div>
    </div>

    <script src="../../assets/js/admin.js"></script>
</body>
</html>

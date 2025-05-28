<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Check if user is logged in and is a regular user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$success = '';
$error = '';

// Get all rooms
$rooms = getAllRooms();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $ruanganId = $_POST['ruangan_id'] ?? '';
    $tanggal = $_POST['tanggal'] ?? '';
    $waktuMulai = $_POST['waktu_mulai'] ?? '';
    $durasiPinjam = $_POST['durasi_pinjam'] ?? '';
    
    // Validate input
    if (empty($ruanganId) || empty($tanggal) || empty($waktuMulai) || empty($durasiPinjam)) {
        $error = 'Semua field harus diisi.';
    } else {
        // Check if room is available
        if (isRoomAvailable($ruanganId, $tanggal, $waktuMulai, $durasiPinjam)) {
            // Create booking
            $result = createBooking($userId, $ruanganId, $tanggal, $waktuMulai, $durasiPinjam);
            
            if ($result) {
                $success = 'Peminjaman berhasil dibuat. Menunggu persetujuan admin.';
            } else {
                $error = 'Gagal membuat peminjaman. Silakan coba lagi.';
            }
        } else {
            $error = 'Ruangan tidak tersedia pada waktu yang dipilih. Silakan pilih waktu lain.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Peminjaman - Sistem Peminjaman Ruangan</title>
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
                    <h2>Buat Peminjaman</h2>
                    <a href="bookings.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <p><a href="bookings.php">Lihat peminjaman saya</a></p>
                    </div>
                <?php else: ?>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-container">
                        <form action="create-booking.php" method="post">
                            <div class="form-group">
                                <label for="ruangan_id">Ruangan</label>
                                <select id="ruangan_id" name="ruangan_id" required>
                                    <option value="">Pilih Ruangan</option>
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?php echo $room['ruangan_id']; ?>">
                                            <?php echo $room['nama_ruangan']; ?> (Kapasitas: <?php echo $room['kapasitas']; ?>, Lokasi: <?php echo $room['lokasi']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal">Tanggal</label>
                                <input type="date" id="tanggal" name="tanggal" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="waktu_mulai">Waktu Mulai</label>
                                <input type="time" id="waktu_mulai" name="waktu_mulai" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="durasi_pinjam">Durasi (jam)</label>
                                <input type="number" id="durasi_pinjam" name="durasi_pinjam" min="1" max="8" value="1" required>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Buat Peminjaman</button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/user.js"></script>
</body>
</html>

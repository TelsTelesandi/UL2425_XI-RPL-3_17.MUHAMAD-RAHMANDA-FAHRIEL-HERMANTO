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
$room = [
    'ruangan_id' => '',
    'nama_ruangan' => '',
    'lokasi' => '',
    'kapasitas' => ''
];
$isEdit = false;

// Check if editing existing room
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $roomId = $_GET['id'];
    $roomData = getRoomById($roomId);
    
    if ($roomData) {
        $room = $roomData;
        $isEdit = true;
    } else {
        $error = 'Ruangan tidak ditemukan.';
    }
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_ruangan = $_POST['nama_ruangan'] ?? '';
    $lokasi = $_POST['lokasi'] ?? '';
    $kapasitas = $_POST['kapasitas'] ?? '';
    
    // Validate input
    if (empty($nama_ruangan) || empty($lokasi) || empty($kapasitas)) {
        $error = 'Semua field harus diisi.';
    } else {
        if ($isEdit) {
            // Update existing room
            $result = updateRoom($room['ruangan_id'], $nama_ruangan, $lokasi, $kapasitas);
            
            if ($result) {
                $success = 'Ruangan berhasil diperbarui.';
                // Refresh room data
                $room = getRoomById($room['ruangan_id']);
            } else {
                $error = 'Gagal memperbarui ruangan.';
            }
        } else {
            // Add new room
            $result = addRoom($nama_ruangan, $lokasi, $kapasitas);
            
            if ($result) {
                $success = 'Ruangan berhasil ditambahkan.';
                // Clear form
                $room = [
                    'ruangan_id' => '',
                    'nama_ruangan' => '',
                    'lokasi' => '',
                    'kapasitas' => ''
                ];
            } else {
                $error = 'Gagal menambahkan ruangan.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Ruangan - Sistem Peminjaman Ruangan</title>
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
                    <h2><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> Ruangan</h2>
                    <a href="rooms.php" class="btn btn-outline">
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
                
                <div class="form-container">
                    <form action="room-form.php<?php echo $isEdit ? '?id=' . $room['ruangan_id'] : ''; ?>" method="post">
                        <div class="form-group">
                            <label for="nama_ruangan">Nama Ruangan</label>
                            <input type="text" id="nama_ruangan" name="nama_ruangan" value="<?php echo $room['nama_ruangan']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lokasi">Lokasi</label>
                            <input type="text" id="lokasi" name="lokasi" value="<?php echo $room['lokasi']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="kapasitas">Kapasitas</label>
                            <input type="number" id="kapasitas" name="kapasitas" value="<?php echo $room['kapasitas']; ?>" min="1" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <?php echo $isEdit ? 'Update' : 'Simpan'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </main>
            
            <?php include 'includes/footer.php'; ?>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>

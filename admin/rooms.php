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

// Process room deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $roomId = $_GET['delete'];
    
    if (deleteRoom($roomId)) {
        $success = 'Ruangan berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus ruangan.';
    }
}

// Get all rooms
$rooms = getAllRooms();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ruangan - Sistem Peminjaman Ruangan</title>
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
                    <h2>Kelola Ruangan</h2>
                    <a href="room-form.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah Ruangan
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
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Ruangan</th>
                                <th>Lokasi</th>
                                <th>Kapasitas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rooms)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Tidak ada data ruangan.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($rooms as $room): ?>
                                    <tr>
                                        <td><?php echo $room['ruangan_id']; ?></td>
                                        <td><?php echo $room['nama_ruangan']; ?></td>
                                        <td><?php echo $room['lokasi']; ?></td>
                                        <td><?php echo $room['kapasitas']; ?></td>
                                        <td>
                                            <a href="room-form.php?id=<?php echo $room['ruangan_id']; ?>" class="btn-icon" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" class="btn-icon delete-btn" data-id="<?php echo $room['ruangan_id']; ?>" title="Hapus">
                                                <i class="fas fa-trash"></i>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus ruangan ini?</p>
            <div class="modal-actions">
                <button id="cancelDelete" class="btn btn-outline">Batal</button>
                <a id="confirmDelete" href="#" class="btn btn-danger">Hapus</a>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin.js"></script>
    <script>
        // Delete confirmation
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = document.getElementById('deleteModal');
        const confirmDelete = document.getElementById('confirmDelete');
        const cancelDelete = document.getElementById('cancelDelete');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const roomId = button.getAttribute('data-id');
                confirmDelete.href = `rooms.php?delete=${roomId}`;
                deleteModal.style.display = 'flex';
            });
        });
        
        cancelDelete.addEventListener('click', () => {
            deleteModal.style.display = 'none';
        });
        
        window.addEventListener('click', (e) => {
            if (e.target === deleteModal) {
                deleteModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>

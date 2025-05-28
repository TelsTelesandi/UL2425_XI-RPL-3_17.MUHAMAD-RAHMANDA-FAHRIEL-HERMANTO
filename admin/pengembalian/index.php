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

// Get all pending return requests
$returnRequests = getPendingReturnRequests();

// Process return confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_return'])) {
    $bookingId = $_POST['booking_id'];
    $status = $_POST['status']; // DIKEMBALIKAN or DITOLAK
    $adminNotes = $_POST['admin_notes'];
    
    if (confirmReturnRequest($bookingId, $status, $adminNotes)) {
        $success = "Status pengembalian berhasil diupdate.";
        // Refresh the list
        $returnRequests = getPendingReturnRequests();
    } else {
        $error = "Gagal mengupdate status pengembalian.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengembalian - Sistem Peminjaman Ruangan</title>
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
                    <h2>Kelola Pengembalian</h2>
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
                                <th>Tanggal Pinjam</th>
                                <th>Waktu</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Kondisi Ruangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($returnRequests)): ?>
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada pengajuan pengembalian.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($returnRequests as $request): ?>
                                    <tr>
                                        <td><?php echo $request['peminjaman_id']; ?></td>
                                        <td><?php echo htmlspecialchars($request['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($request['nama_ruangan']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($request['tanggal'])); ?></td>
                                        <td><?php echo $request['waktu_mulai'] . ' - ' . $request['waktu_selesai']; ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($request['return_date'])); ?></td>
                                        <td><?php echo nl2br(htmlspecialchars($request['return_condition'])); ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm" 
                                                    onclick="showConfirmationModal(<?php echo $request['peminjaman_id']; ?>, 
                                                           '<?php echo addslashes($request['return_condition']); ?>')">
                                                Konfirmasi
                                            </button>
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

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Pengembalian</h3>
            <form method="POST" action="">
                <input type="hidden" name="booking_id" id="modalBookingId">
                
                <div class="form-group">
                    <label>Kondisi Ruangan (dari user):</label>
                    <p id="modalCondition" class="form-text"></p>
                </div>

                <div class="form-group">
                    <label for="admin_notes">Catatan Admin:</label>
                    <textarea name="admin_notes" id="admin_notes" rows="4" required 
                              placeholder="Tambahkan catatan atau komentar..."></textarea>
                </div>

                <div class="form-group">
                    <label>Status Pengembalian:</label>
                    <div class="radio-group">
                        <label>
                            <input type="radio" name="status" value="DIKEMBALIKAN" required> Terima Pengembalian
                        </label>
                        <label>
                            <input type="radio" name="status" value="DITOLAK"> Tolak Pengembalian
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn btn-outline" onclick="closeConfirmationModal()">Batal</button>
                    <button type="submit" name="confirm_return" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function showConfirmationModal(bookingId, condition) {
            document.getElementById('modalBookingId').value = bookingId;
            document.getElementById('modalCondition').textContent = condition;
            document.getElementById('confirmationModal').style.display = 'block';
        }

        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('confirmationModal')) {
                closeConfirmationModal();
            }
        }
    </script>
</body>
</html>

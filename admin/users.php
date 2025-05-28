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

// Process user deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $userId = $_GET['delete'];
    
    // Check if current admin is trying to delete themselves
    if ($userId == $_SESSION['user_id']) {
        $error = 'Anda tidak dapat menghapus akun sendiri.';
    } else {
        $result = deleteUser($userId);
        if ($result === true) {
            $success = 'User berhasil dihapus.';
        } else {
            $error = 'Tidak dapat menghapus user yang memiliki data peminjaman.';
        }
    }
}

// Get all users
$users = getAllUsers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola User - Sistem Peminjaman Ruangan</title>
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
                    <h2>Kelola User</h2>
                    <a href="user-form.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Tambah User
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
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>ID Card</th>
                                <th>Jenis Pengguna</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data user.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                        <td><?php echo htmlspecialchars($user['id_card']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['jenis_pengguna']; ?>">
                                                <?php echo ucfirst($user['jenis_pengguna']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo $user['role']; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="user-form.php?id=<?php echo $user['user_id']; ?>" class="btn-icon" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                                                <a href="#" class="btn-icon delete-btn" data-id="<?php echo $user['user_id']; ?>" data-name="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" title="Hapus">
                                                    <i class="fas fa-trash"></i>
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

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Konfirmasi Hapus</h3>
            <p>Apakah Anda yakin ingin menghapus user <strong id="userName"></strong>?</p>
            <p style="color: #dc3545; font-size: 12px;"><strong>Catatan:</strong> User yang memiliki data peminjaman tidak dapat dihapus.</p>
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
        const userName = document.getElementById('userName');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const userId = button.getAttribute('data-id');
                const userNameText = button.getAttribute('data-name');
                
                userName.textContent = userNameText;
                confirmDelete.href = `users.php?delete=${userId}`;
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

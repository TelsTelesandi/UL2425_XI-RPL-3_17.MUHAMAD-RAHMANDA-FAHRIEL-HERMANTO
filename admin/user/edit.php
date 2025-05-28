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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $id_card = $_POST['id_card'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $jenis_pengguna = $_POST['jenis_pengguna'] ?? '';
    $role = $_POST['role'] ?? '';
    
    // Validate input
    if (empty($username) || empty($id_card) || empty($nama_lengkap) || empty($jenis_pengguna) || empty($role)) {
        $error = 'Semua field harus diisi kecuali password.';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (usernameExistsExcept($username, $userId)) {
        $error = 'Username sudah digunakan.';
    } else {
        // Update user
        $result = updateUser($userId, $username, $id_card, $nama_lengkap, $jenis_pengguna, $role, $password);
        
        if ($result) {
            $success = 'User berhasil diperbarui.';
            // Refresh user data
            $user = getUserById($userId);
        } else {
            $error = 'Gagal memperbarui user.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Sistem Peminjaman Ruangan</title>
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
                    <h2>Edit User</h2>
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
                
                <div class="form-container">
                    <form action="edit.php?id=<?php echo $userId; ?>" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" id="password" name="password">
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        
                        <div class="form-group">
                            <label for="id_card">ID Card</label>
                            <input type="text" id="id_card" name="id_card" value="<?php echo htmlspecialchars($user['id_card']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis_pengguna">Jenis Pengguna</label>
                            <select id="jenis_pengguna" name="jenis_pengguna" required>
                                <option value="">Pilih Jenis Pengguna</option>
                                <option value="siswa" <?php echo $user['jenis_pengguna'] === 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                                <option value="guru" <?php echo $user['jenis_pengguna'] === 'guru' ? 'selected' : ''; ?>>Guru</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update
                            </button>
                        </div>
                    </form>
                </div>
            </main>
            
            <?php include '../includes/footer.php'; ?>
        </div>
    </div>

    <script src="../../assets/js/admin.js"></script>
    <script>
        // Password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Password tidak cocok');
            } else {
                confirmPassword.setCustomValidity('');
            }
        }
        
        password.addEventListener('input', validatePassword);
        confirmPassword.addEventListener('input', validatePassword);
    </script>
</body>
</html>

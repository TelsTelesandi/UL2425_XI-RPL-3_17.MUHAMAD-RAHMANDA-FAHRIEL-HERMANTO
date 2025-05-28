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
$user = [
    'user_id' => '',
    'username' => '',
    'id_card' => '',
    'nama_lengkap' => '',
    'jenis_pengguna' => '',
    'role' => 'user'
];
$isEdit = false;

// Check if editing existing user
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $userId = $_GET['id'];
    $userData = getUserById($userId);
    
    if ($userData) {
        $user = $userData;
        $isEdit = true;
    } else {
        $error = 'User tidak ditemukan.';
    }
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
        $error = 'Semua field harus diisi kecuali password jika edit.';
    } elseif (!$isEdit && empty($password)) {
        $error = 'Password harus diisi untuk user baru.';
    } elseif (!empty($password) && $password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (usernameExistsExcept($username, $isEdit ? $user['user_id'] : null)) {
        $error = 'Username sudah digunakan.';
    } else {
        if ($isEdit) {
            // Update existing user
            $result = updateUser($user['user_id'], $username, $id_card, $nama_lengkap, $jenis_pengguna, $role, $password);
            
            if ($result) {
                $success = 'User berhasil diperbarui.';
                // Refresh user data
                $user = getUserById($user['user_id']);
            } else {
                $error = 'Gagal memperbarui user.';
            }
        } else {
            // Add new user
            $result = addUser($username, $password, $id_card, $nama_lengkap, $jenis_pengguna, $role);
            
            if ($result) {
                $success = 'User berhasil ditambahkan.';
                // Clear form
                $user = [
                    'user_id' => '',
                    'username' => '',
                    'id_card' => '',
                    'nama_lengkap' => '',
                    'jenis_pengguna' => '',
                    'role' => 'user'
                ];
            } else {
                $error = 'Gagal menambahkan user.';
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
    <title><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> User - Sistem Peminjaman Ruangan</title>
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
                    <h2><?php echo $isEdit ? 'Edit' : 'Tambah'; ?> User</h2>
                    <a href="users.php" class="btn btn-outline">
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
                    <form action="user-form.php<?php echo $isEdit ? '?id=' . $user['user_id'] : ''; ?>" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password <?php echo $isEdit ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?></label>
                            <input type="password" id="password" name="password" <?php echo !$isEdit ? 'required' : ''; ?>>
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

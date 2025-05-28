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
    if (empty($username) || empty($password) || empty($id_card) || empty($nama_lengkap) || empty($jenis_pengguna) || empty($role)) {
        $error = 'Semua field harus diisi.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (usernameExists($username)) {
        $error = 'Username sudah digunakan.';
    } else {
        // Add new user
        $result = addUser($username, $password, $id_card, $nama_lengkap, $jenis_pengguna, $role);
        
        if ($result) {
            $success = 'User berhasil ditambahkan.';
        } else {
            $error = 'Gagal menambahkan user.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User - Sistem Peminjaman Ruangan</title>
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
                    <h2>Tambah User</h2>
                    <a href="index.php" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                        <p><a href="index.php">Kembali ke daftar user</a></p>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <div class="form-container">
                    <form action="create.php" method="post">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Konfirmasi Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="id_card">ID Card</label>
                            <input type="text" id="id_card" name="id_card" value="<?php echo isset($_POST['id_card']) ? htmlspecialchars($_POST['id_card']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="jenis_  guna">Jenis Pengguna</label>
                            <select id="jenis_pengguna" name="jenis_pengguna" required>
                                <option value="">Pilih Jenis Pengguna</option>
                                <option value="siswa" <?php echo (isset($_POST['jenis_pengguna']) && $_POST['jenis_pengguna'] === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                                <option value="guru" <?php echo (isset($_POST['jenis_pengguna']) && $_POST['jenis_pengguna'] === 'guru') ? 'selected' : ''; ?>>Guru</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="">Pilih Role</option>
                                <option value="user" <?php echo (isset($_POST['role']) && $_POST['role'] === 'user') ? 'selected' : ''; ?>>User</option>
                                <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan
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

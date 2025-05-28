<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
}

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $id_card = $_POST['id_card'] ?? '';
    $nama_lengkap = $_POST['nama_lengkap'] ?? '';
    $jenis_pengguna = $_POST['jenis_pengguna'] ?? '';
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($id_card) || empty($nama_lengkap) || empty($jenis_pengguna)) {
        $error = 'Semua field harus diisi.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } elseif (usernameExists($username)) {
        $error = 'Username sudah digunakan.';
    } else {
        // Register the user
        $result = registerUser($username, $password, $id_card, $nama_lengkap, $jenis_pengguna);
        
        if ($result) {
            $success = 'Registrasi berhasil. Silakan login.';
        } else {
            $error = 'Registrasi gagal. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Peminjaman Ruangan</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <h1>Sistem Peminjaman Ruangan</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php" class="active">Register</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section class="auth-form">
                <div class="form-container">
                    <h2>Register</h2>
                    
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                            <p><a href="login.php">Login sekarang</a></p>
                        </div>
                    <?php else: ?>
                        <form action="register.php" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" required>
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
                                <input type="text" id="id_card" name="id_card" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="jenis_pengguna">Jenis Pengguna</label>
                                <select id="jenis_pengguna" name="jenis_pengguna" required>
                                    <option value="">Pilih Jenis Pengguna</option>
                                    <option value="siswa">Siswa</option>
                                    <option value="guru">Guru</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <div class="auth-links">
                        <p>Sudah punya akun? <a href="login.php">Login</a></p>
                    </div>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; <?php echo date('Y'); ?> Sistem Peminjaman Ruangan. All rights reserved.</p>
        </footer>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>

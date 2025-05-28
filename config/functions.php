<?php
// Function to sanitize input data
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = $conn->real_escape_string($data);
    return $data;
}

// Function to check if username exists
function usernameExists($username) {
    global $conn;
    $username = sanitize($username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Function to register a new user
function registerUser($username, $password, $id_card, $nama_lengkap, $jenis_pengguna) {
    global $conn;
    
    $username = sanitize($username);
    $password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    $id_card = sanitize($id_card);
    $nama_lengkap = sanitize($nama_lengkap);
    $jenis_pengguna = sanitize($jenis_pengguna);
    $role = 'user'; // Default role is user
    
    $query = "INSERT INTO users (id_card, username, password, role, jenis_pengguna, nama_lengkap) 
              VALUES ('$id_card', '$username', '$password', '$role', '$jenis_pengguna', '$nama_lengkap')";
    
    return $conn->query($query);
}

// Function to authenticate user
function loginUser($username, $password) {
    global $conn;
    
    $username = sanitize($username);
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (for compatibility with existing passwords)
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            return $user;
        }
    }
    
    return false;
}

// Function to get all rooms
function getAllRooms() {
    global $conn;
    
    $query = "SELECT * FROM ruangan ORDER BY ruangan_id ASC";
    $result = $conn->query($query);
    
    $rooms = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $rooms[] = $row;
        }
    }
    
    return $rooms;
}

// Function to get a room by ID
function getRoomById($id) {
    global $conn;
    
    $id = sanitize($id);
    $query = "SELECT * FROM ruangan WHERE ruangan_id = '$id'";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to add a new room
function addRoom($nama_ruangan, $lokasi, $kapasitas) {
    global $conn;
    
    $nama_ruangan = sanitize($nama_ruangan);
    $lokasi = sanitize($lokasi);
    $kapasitas = sanitize($kapasitas);
    
    $query = "INSERT INTO ruangan (nama_ruangan, lokasi, kapasitas) 
              VALUES ('$nama_ruangan', '$lokasi', '$kapasitas')";
    
    return $conn->query($query);
}

// Function to update a room
function updateRoom($id, $nama_ruangan, $lokasi, $kapasitas) {
    global $conn;
    
    $id = sanitize($id);
    $nama_ruangan = sanitize($nama_ruangan);
    $lokasi = sanitize($lokasi);
    $kapasitas = sanitize($kapasitas);
    
    $query = "UPDATE ruangan 
              SET nama_ruangan = '$nama_ruangan', lokasi = '$lokasi', kapasitas = '$kapasitas' 
              WHERE ruangan_id = '$id'";
    
    return $conn->query($query);
}

// Function to delete a room
function deleteRoom($id) {
    global $conn;
    
    $id = sanitize($id);
    $query = "DELETE FROM ruangan WHERE ruangan_id = '$id'";
    
    return $conn->query($query);
}

// Function to get all bookings
function getAllBookings() {
    global $conn;
    
    $query = "SELECT p.*, u.username, u.nama_lengkap, r.nama_ruangan 
              FROM peminjaman_ruangan p 
              JOIN users u ON p.user_id = u.user_id 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              ORDER BY p.tanggal DESC, p.waktu_mulai ASC";
    
    $result = $conn->query($query);
    
    $bookings = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    
    return $bookings;
}

// Function to get bookings by user ID
function getBookingsByUserId($userId) {
    global $conn;
    
    $userId = sanitize($userId);
    $query = "SELECT p.*, r.nama_ruangan 
              FROM peminjaman_ruangan p 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              WHERE p.user_id = '$userId' 
              ORDER BY p.tanggal DESC, p.waktu_mulai ASC";
    
    $result = $conn->query($query);
    
    $bookings = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    
    return $bookings;
}

// Function to get a booking by ID
function getBookingById($id) {
    global $conn;
    
    $id = sanitize($id);
    $query = "SELECT p.*, u.username, u.nama_lengkap, r.nama_ruangan 
              FROM peminjaman_ruangan p 
              JOIN users u ON p.user_id = u.user_id 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              WHERE p.peminjaman_id = '$id'";
    
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to create a new booking
function createBooking($userId, $ruanganId, $tanggal, $waktuMulai, $durasiPinjam) {
    global $conn;
    
    $userId = sanitize($userId);
    $ruanganId = sanitize($ruanganId);
    $tanggal = sanitize($tanggal);
    $waktuMulai = sanitize($waktuMulai);
    $durasiPinjam = sanitize($durasiPinjam);
    
    // Calculate end time
    $waktuSelesai = date('H:i', strtotime($waktuMulai . ' + ' . $durasiPinjam . ' hours'));
    
    // Check if return_status column exists, if not, create booking without it
    $checkColumn = $conn->query("SHOW COLUMNS FROM peminjaman_ruangan LIKE 'return_status'");
    
    if ($checkColumn->num_rows > 0) {
        $query = "INSERT INTO peminjaman_ruangan (user_id, ruangan_id, tanggal, waktu_mulai, durasi_pinjam, waktu_selesai, status, return_status) 
                  VALUES ('$userId', '$ruanganId', '$tanggal', '$waktuMulai', '$durasiPinjam', '$waktuSelesai', 'MENUNGGU', 'BELUM_DIKEMBALIKAN')";
    } else {
        $query = "INSERT INTO peminjaman_ruangan (user_id, ruangan_id, tanggal, waktu_mulai, durasi_pinjam, waktu_selesai, status) 
                  VALUES ('$userId', '$ruanganId', '$tanggal', '$waktuMulai', '$durasiPinjam', '$waktuSelesai', 'MENUNGGU')";
    }
    
    return $conn->query($query);
}

// Function to update booking status
function updateBookingStatus($id, $status) {
    global $conn;
    
    $id = sanitize($id);
    $status = sanitize($status);
    
    $query = "UPDATE peminjaman_ruangan SET status = '$status' WHERE peminjaman_id = '$id'";
    
    return $conn->query($query);
}

// Function to check if room is available
function isRoomAvailable($ruanganId, $tanggal, $waktuMulai, $durasiPinjam) {
    global $conn;
    
    $ruanganId = sanitize($ruanganId);
    $tanggal = sanitize($tanggal);
    $waktuMulai = sanitize($waktuMulai);
    $durasiPinjam = sanitize($durasiPinjam);
    
    // Calculate end time
    $waktuSelesai = date('H:i', strtotime($waktuMulai . ' + ' . $durasiPinjam . ' hours'));
    
    // Check for overlapping bookings with status DITERIMA or MENUNGGU
    $query = "SELECT * FROM peminjaman_ruangan 
              WHERE ruangan_id = '$ruanganId' 
              AND tanggal = '$tanggal' 
              AND status IN ('DITERIMA', 'MENUNGGU') 
              AND (
                  (waktu_mulai <= '$waktuMulai' AND waktu_selesai > '$waktuMulai') OR
                  (waktu_mulai < '$waktuSelesai' AND waktu_selesai >= '$waktuSelesai') OR
                  (waktu_mulai >= '$waktuMulai' AND waktu_selesai <= '$waktuSelesai')
              )";
    
    $result = $conn->query($query);
    
    // If there are no overlapping bookings, the room is available
    return $result->num_rows === 0;
}

// Function to get booking statistics
function getBookingStatistics() {
    global $conn;
    
    $stats = [
        'total' => 0,
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0,
        'rooms' => []
    ];
    
    // Get total counts
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'MENUNGGU' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'DITERIMA' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'DITOLAK' THEN 1 ELSE 0 END) as rejected
              FROM peminjaman_ruangan";
    
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'];
        $stats['pending'] = $row['pending'];
        $stats['approved'] = $row['approved'];
        $stats['rejected'] = $row['rejected'];
    }
    
    // Get bookings per room
    $query = "SELECT r.nama_ruangan, COUNT(p.peminjaman_id) as count
              FROM ruangan r
              LEFT JOIN peminjaman_ruangan p ON r.ruangan_id = p.ruangan_id
              GROUP BY r.ruangan_id
              ORDER BY count DESC";
    
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stats['rooms'][] = $row;
        }
    }
    
    return $stats;
}

// Function to get all users
function getAllUsers() {
    global $conn;
    
    $query = "SELECT * FROM users ORDER BY user_id ASC";
    $result = $conn->query($query);
    
    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Function to get a user by ID
function getUserById($id) {
    global $conn;
    
    $id = sanitize($id);
    $query = "SELECT * FROM users WHERE user_id = '$id'";
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to add a new user (admin function)
function addUser($username, $password, $id_card, $nama_lengkap, $jenis_pengguna, $role) {
    global $conn;
    
    $username = sanitize($username);
    $password = password_hash($password, PASSWORD_DEFAULT);
    $id_card = sanitize($id_card);
    $nama_lengkap = sanitize($nama_lengkap);
    $jenis_pengguna = sanitize($jenis_pengguna);
    $role = sanitize($role);
    
    $query = "INSERT INTO users (id_card, username, password, role, jenis_pengguna, nama_lengkap) 
              VALUES ('$id_card', '$username', '$password', '$role', '$jenis_pengguna', '$nama_lengkap')";
    
    return $conn->query($query);
}

// Function to update a user
function updateUser($id, $username, $id_card, $nama_lengkap, $jenis_pengguna, $role, $password = null) {
    global $conn;
    
    $id = sanitize($id);
    $username = sanitize($username);
    $id_card = sanitize($id_card);
    $nama_lengkap = sanitize($nama_lengkap);
    $jenis_pengguna = sanitize($jenis_pengguna);
    $role = sanitize($role);
    
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users 
                  SET username = '$username', password = '$password', id_card = '$id_card', 
                      nama_lengkap = '$nama_lengkap', jenis_pengguna = '$jenis_pengguna', role = '$role' 
                  WHERE user_id = '$id'";
    } else {
        $query = "UPDATE users 
                  SET username = '$username', id_card = '$id_card', 
                      nama_lengkap = '$nama_lengkap', jenis_pengguna = '$jenis_pengguna', role = '$role' 
                  WHERE user_id = '$id'";
    }
    
    return $conn->query($query);
}

// Function to delete a user
function deleteUser($id) {
    global $conn;
    
    $id = sanitize($id);
    
    // Check if user has any bookings
    $checkQuery = "SELECT COUNT(*) as count FROM peminjaman_ruangan WHERE user_id = '$id'";
    $checkResult = $conn->query($checkQuery);
    $checkRow = $checkResult->fetch_assoc();
    
    if ($checkRow['count'] > 0) {
        return false; // Cannot delete user with existing bookings
    }
    
    $query = "DELETE FROM users WHERE user_id = '$id'";
    return $conn->query($query);
}

// Function to check if username exists (excluding current user)
function usernameExistsExcept($username, $userId = null) {
    global $conn;
    
    $username = sanitize($username);
    $query = "SELECT * FROM users WHERE username = '$username'";
    
    if ($userId) {
        $userId = sanitize($userId);
        $query .= " AND user_id != '$userId'";
    }
    
    $result = $conn->query($query);
    return $result->num_rows > 0;
}

// Function to get user statistics
function getUserStatistics() {
    global $conn;
    
    $stats = [
        'total' => 0,
        'admins' => 0,
        'users' => 0,
        'siswa' => 0,
        'guru' => 0
    ];
    
    // Get total counts
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) as users,
                SUM(CASE WHEN jenis_pengguna = 'siswa' THEN 1 ELSE 0 END) as siswa,
                SUM(CASE WHEN jenis_pengguna = 'guru' THEN 1 ELSE 0 END) as guru
              FROM users";
    
    $result = $conn->query($query);
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'];
        $stats['admins'] = $row['admins'];
        $stats['users'] = $row['users'];
        $stats['siswa'] = $row['siswa'];
        $stats['guru'] = $row['guru'];
    }
    
    return $stats;
}

// ========== RETURN MANAGEMENT FUNCTIONS ==========

// Function to check if return columns exist
function checkReturnColumns() {
    global $conn;
    
    $returnStatusExists = $conn->query("SHOW COLUMNS FROM peminjaman_ruangan LIKE 'return_status'");
    $returnDateExists = $conn->query("SHOW COLUMNS FROM peminjaman_ruangan LIKE 'tanggal_dikembalikan'");
    
    return ($returnStatusExists->num_rows > 0 && $returnDateExists->num_rows > 0);
}

// Function to get pending returns (approved bookings that need to be returned)
function getPendingReturns() {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        // Return empty array if columns don't exist
        return [];
    }
    
    $query = "SELECT p.*, u.username, u.nama_lengkap, r.nama_ruangan,
                CASE 
                    WHEN p.return_status = 'DIKEMBALIKAN' THEN 'DIKEMBALIKAN'
                    WHEN NOW() > CONCAT(p.tanggal, ' ', p.waktu_selesai) AND p.return_status != 'DIKEMBALIKAN' THEN 'TERLAMBAT'
                    ELSE COALESCE(p.return_status, 'BELUM_DIKEMBALIKAN')
                END as return_status
              FROM peminjaman_ruangan p 
              JOIN users u ON p.user_id = u.user_id 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              WHERE p.status = 'DITERIMA'
              ORDER BY p.tanggal DESC, p.waktu_mulai ASC";
    
    $result = $conn->query($query);
    
    $returns = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Update return status in database if it's late
            if ($row['return_status'] === 'TERLAMBAT') {
                updateReturnStatus($row['peminjaman_id'], 'TERLAMBAT');
            }
            $returns[] = $row;
        }
    }
    
    return $returns;
}

// Function to get all returns for reporting
function getAllReturns() {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        // Return empty array if columns don't exist
        return [];
    }
    
    $query = "SELECT p.*, u.username, u.nama_lengkap, r.nama_ruangan,
                CASE 
                    WHEN p.return_status = 'DIKEMBALIKAN' THEN 'DIKEMBALIKAN'
                    WHEN NOW() > CONCAT(p.tanggal, ' ', p.waktu_selesai) AND p.return_status != 'DIKEMBALIKAN' THEN 'TERLAMBAT'
                    ELSE COALESCE(p.return_status, 'BELUM_DIKEMBALIKAN')
                END as return_status
              FROM peminjaman_ruangan p 
              JOIN users u ON p.user_id = u.user_id 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              WHERE p.status = 'DITERIMA'
              ORDER BY p.tanggal DESC, p.waktu_mulai ASC";
    
    $result = $conn->query($query);
    
    $returns = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $returns[] = $row;
        }
    }
    
    return $returns;
}

// Function to process return (admin confirms return)
function processReturn($bookingId) {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        return false;
    }
    
    $bookingId = sanitize($bookingId);
    $currentDateTime = date('Y-m-d H:i:s');
    
    $query = "UPDATE peminjaman_ruangan 
              SET return_status = 'DIKEMBALIKAN', tanggal_dikembalikan = '$currentDateTime' 
              WHERE peminjaman_id = '$bookingId'";
    
    return $conn->query($query);
}

// Function to update return status
function updateReturnStatus($bookingId, $status) {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        return false;
    }
    
    $bookingId = sanitize($bookingId);
    $status = sanitize($status);
    
    $query = "UPDATE peminjaman_ruangan 
              SET return_status = '$status' 
              WHERE peminjaman_id = '$bookingId'";
    
    return $conn->query($query);
}

// Function to get return statistics
function getReturnStatistics() {
    global $conn;
    
    $stats = [
        'total' => 0,
        'belum_dikembalikan' => 0,
        'terlambat' => 0,
        'dikembalikan' => 0
    ];
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        return $stats;
    }
    
    $query = "SELECT 
                COUNT(*) as total,
                SUM(CASE 
                    WHEN return_status = 'DIKEMBALIKAN' THEN 0
                    WHEN NOW() > CONCAT(tanggal, ' ', waktu_selesai) AND return_status != 'DIKEMBALIKAN' THEN 0
                    ELSE 1 
                END) as belum_dikembalikan,
                SUM(CASE 
                    WHEN NOW() > CONCAT(tanggal, ' ', waktu_selesai) AND return_status != 'DIKEMBALIKAN' THEN 1 
                    ELSE 0 
                END) as terlambat,
                SUM(CASE WHEN return_status = 'DIKEMBALIKAN' THEN 1 ELSE 0 END) as dikembalikan
              FROM peminjaman_ruangan 
              WHERE status = 'DITERIMA'";
    
    $result = $conn->query($query);
    
    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stats['total'] = $row['total'];
        $stats['belum_dikembalikan'] = $row['belum_dikembalikan'];
        $stats['terlambat'] = $row['terlambat'];
        $stats['dikembalikan'] = $row['dikembalikan'];
    }
    
    return $stats;
}

// ========== USER RETURN FUNCTIONS ==========

// Function to get user's returns (approved bookings for a specific user)
function getUserReturns($userId) {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        // Return empty array if columns don't exist
        return [];
    }
    
    $userId = sanitize($userId);
    $query = "SELECT p.*, r.nama_ruangan,
                CASE 
                    WHEN p.return_status = 'DIKEMBALIKAN' THEN 'DIKEMBALIKAN'
                    WHEN p.return_status = 'MENUNGGU_KONFIRMASI' THEN 'MENUNGGU_KONFIRMASI'
                    WHEN NOW() > CONCAT(p.tanggal, ' ', p.waktu_selesai) AND p.return_status != 'DIKEMBALIKAN' THEN 'TERLAMBAT'
                    ELSE COALESCE(p.return_status, 'BELUM_DIKEMBALIKAN')
                END as return_status
              FROM peminjaman_ruangan p 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              WHERE p.user_id = '$userId' AND p.status = 'DITERIMA'
              ORDER BY p.tanggal DESC, p.waktu_mulai ASC";
    
    $result = $conn->query($query);
    
    $returns = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Update return status in database if it's late
            if ($row['return_status'] === 'TERLAMBAT') {
                updateReturnStatus($row['peminjaman_id'], 'TERLAMBAT');
            }
            $returns[] = $row;
        }
    }
    
    return $returns;
}

// Function to process user return request (user requests return)
function processUserReturn($bookingId) {
    global $conn;
    
    // Check if return columns exist
    if (!checkReturnColumns()) {
        return false;
    }
    
    $bookingId = sanitize($bookingId);
    
    // Set status to waiting for admin confirmation
    $query = "UPDATE peminjaman_ruangan 
              SET return_status = 'MENUNGGU_KONFIRMASI' 
              WHERE peminjaman_id = '$bookingId'";
    
    return $conn->query($query);
}

// Function to get pending return requests
function getPendingReturnRequests() {
    global $conn;
    
    $query = "SELECT p.*, r.nama_ruangan, u.nama_lengkap 
              FROM peminjaman_ruangan p 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              JOIN users u ON p.user_id = u.user_id 
              WHERE p.return_status = 'PENGAJUAN'
              ORDER BY p.return_date DESC";
    
    $result = $conn->query($query);
    $requests = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    
    return $requests;
}

// Function to confirm return request
function confirmReturnRequest($bookingId, $status, $adminNotes) {
    global $conn;
    
    $bookingId = sanitize($bookingId);
    $status = sanitize($status);
    $adminNotes = sanitize($adminNotes);
    
    // Update return status and add admin notes
    $query = "UPDATE peminjaman_ruangan 
              SET return_status = ?, 
                  return_date = NOW()
              WHERE peminjaman_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $status, $bookingId);
    
    return $stmt->execute();
}

// Function to get return request details
function getReturnRequestDetails($bookingId) {
    global $conn;
    
    $bookingId = sanitize($bookingId);
    
    $query = "SELECT p.*, r.nama_ruangan, u.nama_lengkap 
              FROM peminjaman_ruangan p 
              JOIN ruangan r ON p.ruangan_id = r.ruangan_id 
              JOIN users u ON p.user_id = u.user_id 
              WHERE p.peminjaman_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $bookingId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}
?>

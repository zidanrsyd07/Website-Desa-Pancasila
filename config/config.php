<?php
// ================== ERROR REPORTING (WAJIB SAAT DEVELOPMENT) ==================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================== SESSION ==================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================== DATABASE CONFIG ==================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'desa_pancasila');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");

// ================== AUTH FUNCTIONS ==================
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

// ================== USER FUNCTIONS ==================
function getUserById($conn, $id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) {
        return null;
    }

    $result = $stmt->get_result();
    return $result ? $result->fetch_assoc() : null;
}

// ================== DESA INFO ==================
function getDesaInfo($conn) {
    $query = "SELECT * FROM desa_info LIMIT 1";
    $result = $conn->query($query);

    if (!$result || $result->num_rows === 0) {
        return [
            'jumlah_penduduk'     => 0,
            'jumlah_dusun'        => 0,
            'jumlah_rumah_tangga' => 0,
            'sejarah_desa'        => '',
            'no_telepon'          => '-',
            'email'               => '-'
        ];
    }

    return $result->fetch_assoc();
}

// ================== DATE FORMAT ==================
function formatTanggal($date) {
    if (!$date) return '-';

    $months = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    $timestamp = strtotime($date);
    $day   = date('d', $timestamp);
    $month = $months[(int)date('m', $timestamp) - 1];
    $year  = date('Y', $timestamp);

    return "$day $month $year";
}

// ================== FILE UPLOAD ==================
function uploadFile($file, $folder) {

    // Normalisasi path
    $folder = rtrim($folder, '/');

    if (!is_dir($folder)) {
        if (!mkdir($folder, 0755, true)) {
            return ['success' => false, 'message' => 'Folder upload gagal dibuat'];
        }
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Terjadi error saat upload file'];
    }

    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp  = $file['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($file_ext, $allowed_ext)) {
        return ['success' => false, 'message' => 'Format file tidak diizinkan'];
    }

    if ($file_size > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maks 5MB)'];
    }

    $new_file_name = uniqid('img_', true) . '.' . $file_ext;
    $upload_path   = $folder . '/' . $new_file_name;

    if (move_uploaded_file($file_tmp, $upload_path)) {
        return [
            'success'  => true,
            'filename' => $new_file_name
        ];
    }

    return ['success' => false, 'message' => 'Gagal memindahkan file ke folder tujuan'];
}

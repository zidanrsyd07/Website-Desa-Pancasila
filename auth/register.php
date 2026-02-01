<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');

    if ($username === '' || $email === '' || $password === '' || $full_name === '') {
        $error = 'Semua field wajib diisi';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok';
    } else {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1"
        );
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $error = 'Username atau email sudah terdaftar';
        } else {

            $hash = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, password, full_name, role)
                 VALUES (?, ?, ?, ?, 'user')"
            );
            $stmt->bind_param("ssss", $username, $email, $hash, $full_name);

            if ($stmt->execute()) {
                $success = 'Pendaftaran berhasil. Silakan login.';
            } else {
                $error = 'Gagal menyimpan data';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Register - Desa Pancasila</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background:
        linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65)),
        url('../assets/backgrounds/register-bg.jpg') center / cover no-repeat;
}

.bg-accent { background-color: #8B0000; }
.text-accent { color: #8B0000; }

.register-logo {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.register-logo img {
    max-width: 120px;
    max-height: 120px;
    object-fit: contain;
}
</style>
</head>

<body class="min-h-screen flex items-center justify-center px-4 py-10">

<div class="max-w-md w-full">
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        <!-- LOGO -->
        <div class="register-logo">
            <img src="../assets/logo/logo.png" alt="Logo Desa Pancasila">
        </div>

        <h1 class="text-2xl font-bold text-center mb-2">
            Daftar Akun
        </h1>
        <p class="text-center text-gray-500 mb-6">
            Sistem Informasi Desa Pancasila
        </p>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-center">
                <?= htmlspecialchars($success) ?>
                <a href="login.php" class="block font-bold mt-2 hover:underline">
                    Ke halaman login
                </a>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-semibold mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Username</label>
                <input type="text" name="username" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Konfirmasi Password</label>
                <input type="password" name="confirm_password" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full bg-accent hover:bg-red-900 text-white font-bold py-2 rounded-lg transition">
                Daftar
            </button>
        </form>

        <div class="text-center mt-4 text-sm">
            Sudah punya akun?
            <a href="login.php" class="text-accent font-bold hover:underline">
                Login sekarang
            </a>
        </div>

        <div class="text-center mt-2 text-sm">
            <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                ← Kembali ke beranda
            </a>
        </div>

    </div>
</div>

</body>
</html>

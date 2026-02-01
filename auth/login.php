<?php
session_start(); // ⬅️ WAJIB, INI YANG SEBELUMNYA BIKIN BUG

require_once '../config/config.php';

$error = '';

// ===============================
// CEGAH AKSES LOGIN JIKA SUDAH LOGIN
// ===============================
if (isset($_SESSION['user_id'], $_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../dashboard/admin/index.php");
    } else {
        header("Location: ../dashboard/user/index.php");
    }
    exit;
}

// ===============================
// PROSES LOGIN
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi';
    } else {

        $stmt = $conn->prepare("
            SELECT id, username, password, role, full_name
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        if (!$stmt) {
            die("Query error: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                // ===============================
                // NORMALISASI & VALIDASI ROLE
                // ===============================
                $role = strtolower(trim($user['role']));

                if (!in_array($role, ['admin', 'user'])) {
                    $error = 'Role akun tidak valid';
                } else {
                    // ===============================
                    // SET SESSION (INI YANG KRUSIAL)
                    // ===============================
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['role']      = $role;
                    $_SESSION['full_name'] = $user['full_name'];

                    // ===============================
                    // REDIRECT SESUAI ROLE
                    // ===============================
                    if ($role === 'admin') {
                        header("Location: ../dashboard/admin/index.php");
                    } else {
                        header("Location: ../dashboard/user/index.php");
                    }
                    exit;
                }

            } else {
                $error = 'Password salah';
            }

        } else {
            $error = 'Username tidak ditemukan';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Login - Desa Pancasila</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

body {
    font-family: 'Poppins', sans-serif;
    background:
        linear-gradient(rgba(0,0,0,.65), rgba(0,0,0,.65)),
        url('../assets/backgrounds/login-bg.jpg') center / cover no-repeat;
}

/* Warna utama */
.bg-accent { background-color: #8B0000; }
.text-accent { color: #8B0000; }

/* Logo */
.login-logo {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
}

.login-logo img {
    max-width: 120px;
    max-height: 120px;
    object-fit: contain;
}
</style>
</head>

<body class="min-h-screen flex items-center justify-center px-4">

<div class="max-w-md w-full">
    <div class="bg-white rounded-2xl shadow-2xl p-8">

        <!-- LOGO -->
        <div class="login-logo">
            <img src="../assets/logo/logo.png" alt="Logo Desa Pancasila">
        </div>

        <h1 class="text-2xl font-bold text-center mb-2">
            Login Desa Pancasila
        </h1>
        <p class="text-center text-gray-500 mb-6">
            Sistem Informasi Desa
        </p>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 p-3 rounded mb-4">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-4">
                <label class="block mb-1 font-semibold">Username</label>
                <input type="text" name="username" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <div class="mb-6">
                <label class="block mb-1 font-semibold">Password</label>
                <input type="password" name="password" required
                       class="w-full border rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-700 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full bg-accent hover:bg-red-900 text-white py-2 rounded-lg font-bold transition">
                Login
            </button>
        </form>

        <p class="text-center mt-4 text-sm">
            Belum punya akun?
            <a href="register.php" class="text-accent font-bold">Daftar</a>
        </p>

        <p class="text-center mt-2 text-sm">
            <a href="../index.php" class="text-gray-500 hover:text-gray-700">
                ← Kembali ke beranda
            </a>
        </p>

    </div>
</div>

</body>
</html>

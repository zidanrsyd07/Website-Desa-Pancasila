<?php
session_start();
require_once '../../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, full_name, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$success = $error = "";

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];

    if ($full_name === '' || $email === '' || $username === '') {
        $error = "Nama, email, dan username wajib diisi.";
    } else {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare(
                "UPDATE users SET full_name=?, email=?, username=?, password=? WHERE id=?"
            );
            $stmt->bind_param("ssssi", $full_name, $email, $username, $hash, $user_id);
        } else {
            $stmt = $conn->prepare(
                "UPDATE users SET full_name=?, email=?, username=? WHERE id=?"
            );
            $stmt->bind_param("sssi", $full_name, $email, $username, $user_id);
        }

        if ($stmt->execute()) {
            $_SESSION['full_name'] = $full_name;
            $success = "Profil berhasil diperbarui.";
        } else {
            $error = "Gagal memperbarui profil.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Saya</title>
<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
body { font-family: 'Poppins', sans-serif; }
.text-accent { color:#8B0000 }
.bg-accent { background:#8B0000 }
</style>
</head>

<body class="bg-gray-100">

<?php include '../../partials/navbar_login.php'; ?>

<div class="container mx-auto px-4 py-16 max-w-3xl">
    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h2 class="text-3xl font-bold mb-6 text-gray-800">Profile Saya</h2>
        <div class="w-20 h-1 bg-accent mb-8"></div>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-6">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-6">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">

            <div>
                <label class="block font-semibold mb-1">Nama Lengkap</label>
                <input type="text" name="full_name"
                       value="<?= htmlspecialchars($user['full_name']) ?>"
                       class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Username</label>
                <input type="text" name="username"
                       value="<?= htmlspecialchars($user['username']) ?>"
                       class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($user['email']) ?>"
                       class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-700 outline-none">
            </div>

            <div>
                <label class="block font-semibold mb-1">Password Baru</label>
                <input type="password" name="password"
                       placeholder="Kosongkan jika tidak ingin mengganti"
                       class="w-full border rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-700 outline-none">
            </div>

            <div class="flex justify-end gap-4 pt-6">
                <a href="index.php"
                   class="px-6 py-3 rounded-lg border font-semibold text-gray-600 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit"
                        class="bg-accent text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#700000]">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

<?php include '../../partials/footer_login.php'; ?>

</body>
</html>

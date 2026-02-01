<?php
session_start();
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
    exit;
}

$user = getUserById($conn, $_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama_usaha']);
    $pemilik   = trim($_POST['pemilik']);
    $kontak    = trim($_POST['kontak']);
    $harga     = trim($_POST['harga']);
    $deskripsi = trim($_POST['deskripsi']);

    $foto = null;
    $uploadDir = "../../assets/umkm/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {
            $foto = uniqid('umkm_') . '.' . $ext;
            move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $foto);
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO umkm (nama_usaha, pemilik, kontak, harga, deskripsi, foto)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("ssssss", $nama, $pemilik, $kontak, $harga, $deskripsi, $foto);
    $stmt->execute();

    redirect('belanja.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah UMKM</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
body { font-family: 'Poppins', sans-serif; }
.bg-accent { background:#8B0000 }
.text-accent { color:#8B0000 }

.sidebar { transform: translateX(-100%); transition:.3s }
.sidebar.active { transform: translateX(0); }
@media (min-width:768px){
    .sidebar{ transform:translateX(0)!important }
}
</style>
</head>

<body class="bg-gray-100">

<!-- TOGGLE MOBILE -->
<button onclick="toggleSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-accent text-white px-3 py-2 rounded">
☰
</button>

<!-- SIDEBAR -->
<div id="sidebar" class="sidebar fixed left-0 top-0 w-64 h-screen bg-gray-900 text-white z-40">
    <div class="p-6 border-b border-gray-700">
        <div class="flex items-center gap-3">
            <img src="../../assets/logo/logo.png" class="h-10">
            <div>
                <h1 class="font-bold text-lg">Admin Panel</h1>
                <p class="text-xs text-gray-400">Desa Pancasila</p>
            </div>
        </div>
    </div>

    <nav class="p-4 space-y-2">
        <a href="index.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Dashboard</a>
        <a href="berita.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

<!-- CONTENT -->
<div class="md:ml-64 min-h-screen">

<header class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h2 class="text-xl font-bold">Tambah UMKM</h2>
    <a href="belanja.php" class="text-accent font-semibold hover:underline">
        ← Kembali
    </a>
</header>

<main class="p-6">

<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-6">
    <h3 class="text-lg font-bold mb-6">Form UMKM</h3>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">

        <div>
            <label class="text-sm font-semibold">Nama Usaha</label>
            <input name="nama_usaha" required class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-semibold">Pemilik</label>
            <input name="pemilik" required class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-semibold">Kontak (No. WA)</label>
            <input name="kontak" required class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-semibold">Harga / Keterangan</label>
            <input name="harga" class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-semibold">Foto UMKM</label>
            <input type="file" name="foto" accept="image/*"
                   class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div>
            <label class="text-sm font-semibold">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      class="w-full border rounded px-3 py-2 mt-1"></textarea>
        </div>

        <button class="w-full bg-accent text-white py-2 rounded-lg font-bold">
            Simpan UMKM
        </button>

    </form>
</div>

</main>
</div>

<script>
function toggleSidebar(){
    document.getElementById('sidebar').classList.toggle('active');
}
</script>

</body>
</html>

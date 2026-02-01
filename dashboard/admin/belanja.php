<?php
session_start();
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
    exit;
}

$user = getUserById($conn, $_SESSION['user_id']);
$umkm = $conn->query("SELECT * FROM umkm ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola UMKM</title>

<script src="https://cdn.tailwindcss.com"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
body { font-family: 'Poppins', sans-serif; }
.bg-accent { background:#8B0000 }
.text-accent { color:#8B0000 }

.sidebar { transform: translateX(-100%); transition: .3s }
.sidebar.active { transform: translateX(0); }
@media (min-width:768px){
    .sidebar{ transform: translateX(0)!important }
}
</style>
</head>

<body class="bg-gray-100">

<!-- TOGGLE MOBILE -->
<button onclick="toggleSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-accent text-white px-3 py-2 rounded">
☰
</button>

<!-- SIDEBAR (TETAP, TIDAK DIUBAH) -->
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
        <a href="berita.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

<!-- CONTENT -->
<div class="md:ml-64 min-h-screen">

<header class="bg-white shadow px-6 py-4 flex justify-between items-center">
    <h2 class="text-2xl font-bold">Kelola UMKM</h2>

    <!-- TOMBOL TAMBAH DI POJOK KANAN ATAS -->
    <a href="belanja-add.php"
       class="bg-accent text-white px-4 py-2 rounded-lg font-semibold hover:opacity-90">
        + Tambah UMKM
    </a>
</header>

<main class="p-6">

<div class="bg-white rounded-xl shadow p-6">
<h3 class="text-xl font-bold mb-4">Daftar UMKM</h3>

<table class="w-full text-sm">
<thead class="bg-gray-100">
<tr>
<th class="p-3 text-left">Foto</th>
<th class="p-3 text-left">Nama Usaha</th>
<th class="p-3 text-left">Pemilik</th>
<th class="p-3 text-left">Kontak</th>
<th class="p-3 text-left">Harga</th>
<th class="p-3 text-center">Aksi</th>
</tr>
</thead>

<tbody class="divide-y">
<?php while($u = $umkm->fetch_assoc()): ?>
<tr>
<td class="p-3">
<?php if ($u['foto']): ?>
<img src="../../uploads/umkm/<?= htmlspecialchars($u['foto']) ?>"
     class="w-16 h-16 object-cover rounded">
<?php else: ?>
<div class="w-16 h-16 bg-gray-200 flex items-center justify-center text-xs">
No Image
</div>
<?php endif; ?>
</td>

<td class="p-3 font-semibold"><?= htmlspecialchars($u['nama_usaha']) ?></td>
<td class="p-3"><?= htmlspecialchars($u['pemilik']) ?></td>
<td class="p-3"><?= htmlspecialchars($u['kontak']) ?></td>
<td class="p-3 text-accent font-semibold"><?= htmlspecialchars($u['harga']) ?></td>

<td class="p-3 text-center space-x-3">
<a href="belanja-edit.php?id=<?= $u['id'] ?>"
   class="text-blue-600 font-semibold hover:underline">
Edit
</a>

<a href="belanja-delete.php?id=<?= $u['id'] ?>"
   onclick="return confirm('Yakin hapus UMKM ini?')"
   class="text-red-600 font-semibold hover:underline">
Hapus
</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
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

<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$umkm_id = (int)($_GET['id'] ?? 0);
$umkm = null;
$error = '';
$success = '';

// Ambil data UMKM
$stmt = $conn->prepare("SELECT * FROM umkm WHERE id = ?");
$stmt->bind_param("i", $umkm_id);
$stmt->execute();
$result = $stmt->get_result();
$umkm = $result->fetch_assoc();

if (!$umkm) {
    redirect('belanja.php');
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_usaha = trim($_POST['nama_usaha'] ?? '');
    $pemilik    = trim($_POST['pemilik'] ?? '');
    $kontak     = trim($_POST['kontak'] ?? '');
    $harga      = (int)($_POST['harga'] ?? 0);
    $deskripsi  = trim($_POST['deskripsi'] ?? '');

    $foto = $umkm['foto'];

    // Upload foto baru (opsional)
    if (!empty($_FILES['foto']['name'])) {
        $upload = uploadFile($_FILES['foto'], '../../uploads/umkm');
        if ($upload['success']) {
            if ($umkm['foto'] && file_exists('../../uploads/umkm/' . $umkm['foto'])) {
                unlink('../../uploads/umkm/' . $umkm['foto']);
            }
            $foto = $upload['filename'];
        } else {
            $error = $upload['message'];
        }
    }

    if (empty($error)) {
        if ($nama_usaha === '' || $pemilik === '' || $kontak === '') {
            $error = 'Nama usaha, pemilik, dan kontak wajib diisi';
        } else {
            $update = $conn->prepare("
                UPDATE umkm 
                SET nama_usaha = ?, pemilik = ?, kontak = ?, harga = ?, deskripsi = ?, foto = ?
                WHERE id = ?
            ");
            $update->bind_param(
                "sssissi",
                $nama_usaha,
                $pemilik,
                $kontak,
                $harga,
                $deskripsi,
                $foto,
                $umkm_id
            );

            if ($update->execute()) {
                $success = 'UMKM berhasil diperbarui';
                $umkm = array_merge($umkm, [
                    'nama_usaha' => $nama_usaha,
                    'pemilik' => $pemilik,
                    'kontak' => $kontak,
                    'harga' => $harga,
                    'deskripsi' => $deskripsi,
                    'foto' => $foto
                ]);
            } else {
                $error = 'Gagal menyimpan perubahan';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit UMKM - Admin Desa Pancasila</title>

<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
body { font-family: 'Poppins', sans-serif; }
.bg-accent { background:#8B0000; }
.text-accent { color:#8B0000; }
.sidebar { transform: translateX(-100%); }
.sidebar.active { transform: translateX(0); }
@media (min-width: 768px) {
    .sidebar { transform: translateX(0) !important; }
}
</style>
</head>

<body class="bg-gray-100">

<button onclick="toggleSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-accent text-white p-2 rounded-lg">☰</button>

<!-- Sidebar -->
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
        <a href="index.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Dashboard</a>
        <a href="berita.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

<div class="md:ml-64 min-h-screen">
<header class="bg-white shadow">
    <div class="px-6 py-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold">Edit UMKM</h2>
        <a href="belanja.php" class="text-accent font-bold hover:underline">← Kembali</a>
    </div>
</header>

<main class="p-6">
<div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">

<?php if ($error): ?>
<div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
<?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
<?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-4">

<div>
<label class="font-semibold">Nama Usaha</label>
<input type="text" name="nama_usaha" required
value="<?= htmlspecialchars($umkm['nama_usaha']) ?>"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div>
<label class="font-semibold">Pemilik</label>
<input type="text" name="pemilik"
value="<?= htmlspecialchars($umkm['pemilik']) ?>"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div>
<label class="font-semibold">Kontak (WA)</label>
<input type="text" name="kontak"
value="<?= htmlspecialchars($umkm['kontak']) ?>"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div>
<label class="font-semibold">Harga</label>
<input type="number" name="harga"
value="<?= (int)$umkm['harga'] ?>"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div>
<label class="font-semibold">Deskripsi</label>
<textarea name="deskripsi" rows="3"
class="w-full border-2 rounded-lg px-4 py-2"><?= htmlspecialchars($umkm['deskripsi'] ?? '') ?></textarea>
</div>

<div>
<label class="font-semibold">Foto Saat Ini</label>
<img src="../../uploads/umkm/<?= htmlspecialchars($umkm['foto']) ?>"
class="max-w-xs rounded-lg mt-2">
</div>

<div>
<label class="font-semibold">Ganti Foto</label>
<input type="file" name="foto" accept="image/*"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div class="flex gap-4 pt-4">
<button class="flex-1 bg-accent text-white py-3 rounded-lg font-bold">
Simpan Perubahan
</button>

<a href="belanja-delete.php?id=<?= $umkm['id'] ?>"
onclick="return confirm('Yakin hapus UMKM ini?')"
class="flex-1 bg-red-600 text-white py-3 rounded-lg font-bold text-center">
Hapus
</a>
</div>

</form>
</div>
</main>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}
</script>

</body>
</html>

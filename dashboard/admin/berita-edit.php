<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
    exit;
}

$berita_id = (int)($_GET['id'] ?? 0);
if ($berita_id <= 0) {
    redirect('berita.php');
    exit;
}

$error = '';
$success = '';

// Ambil data berita
$stmt = $conn->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->bind_param("i", $berita_id);
$stmt->execute();
$result = $stmt->get_result();
$berita = $result->fetch_assoc();

if (!$berita) {
    redirect('berita.php');
    exit;
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul     = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $konten    = trim($_POST['konten'] ?? '');
    $status    = $_POST['status'] ?? 'published';

    if ($judul === '' || $deskripsi === '') {
        $error = 'Judul dan deskripsi wajib diisi';
    } else {
        $gambar = $berita['gambar'];

        // Upload gambar baru (opsional)
        if (!empty($_FILES['gambar']['name'])) {
            $upload = uploadFile($_FILES['gambar'], '../../uploads/news');
            if ($upload['success']) {
                if ($berita['gambar'] && file_exists('../../uploads/news/' . $berita['gambar'])) {
                    unlink('../../uploads/news/' . $berita['gambar']);
                }
                $gambar = $upload['filename'];
            } else {
                $error = $upload['message'];
            }
        }

        if ($error === '') {
            $update = $conn->prepare("
                UPDATE berita 
                SET judul=?, deskripsi=?, konten=?, gambar=?, status=? 
                WHERE id=?
            ");
            $update->bind_param(
                "sssssi",
                $judul,
                $deskripsi,
                $konten,
                $gambar,
                $status,
                $berita_id
            );

            if ($update->execute()) {
                $success = 'Berita berhasil diperbarui';
                $berita = array_merge($berita, [
                    'judul' => $judul,
                    'deskripsi' => $deskripsi,
                    'konten' => $konten,
                    'gambar' => $gambar,
                    'status' => $status
                ]);
            } else {
                $error = 'Gagal memperbarui berita';
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
<title>Edit Berita - Admin Desa Pancasila</title>

<script src="https://cdn.tailwindcss.com"></script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
body { font-family: 'Poppins', sans-serif; }
.bg-accent { background-color:#8B0000; }
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

<!-- SIDEBAR (SAMA PERSIS) -->
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
        <a href="berita.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

<div class="md:ml-64 min-h-screen">
<header class="bg-white shadow">
    <div class="px-6 py-4 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900">Edit Berita</h2>
        <a href="berita.php" class="text-accent font-bold hover:underline">← Kembali</a>
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
<label class="block font-semibold mb-2">Judul Berita</label>
<input type="text" name="judul" required
value="<?= htmlspecialchars($berita['judul']) ?>"
class="w-full border-2 rounded-lg px-4 py-2 focus:border-accent outline-none">
</div>

<div>
<label class="block font-semibold mb-2">Deskripsi Singkat</label>
<textarea name="deskripsi" rows="3"
class="w-full border-2 rounded-lg px-4 py-2 focus:border-accent outline-none"><?= htmlspecialchars($berita['deskripsi']) ?></textarea>
</div>

<div>
<label class="block font-semibold mb-2">Konten Berita</label>
<textarea name="konten" rows="6"
class="w-full border-2 rounded-lg px-4 py-2 focus:border-accent outline-none"><?= htmlspecialchars($berita['konten']) ?></textarea>
</div>

<div>
<label class="block font-semibold mb-2">Status</label>
<select name="status"
class="w-full border-2 rounded-lg px-4 py-2 focus:border-accent outline-none">
<option value="published" <?= $berita['status']==='published'?'selected':'' ?>>Published</option>
<option value="draft" <?= $berita['status']==='draft'?'selected':'' ?>>Draft</option>
</select>
</div>

<div>
<label class="block font-semibold mb-2">Gambar Saat Ini</label>
<img src="../../uploads/news/<?= htmlspecialchars($berita['gambar']) ?>"
class="max-w-xs rounded-lg mb-2">
</div>

<div>
<label class="block font-semibold mb-2">Ganti Gambar</label>
<input type="file" name="gambar" accept="image/*"
class="w-full border-2 rounded-lg px-4 py-2">
</div>

<div class="flex gap-4 pt-4">
<button type="submit"
class="flex-1 bg-accent text-white py-3 rounded-lg font-bold hover:bg-opacity-90">
Simpan Perubahan
</button>

<a href="berita-delete.php?id=<?= $berita['id'] ?>"
onclick="return confirm('Yakin hapus berita ini?')"
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

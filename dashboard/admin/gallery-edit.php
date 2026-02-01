<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$gallery_id = $_GET['id'] ?? 0;
$gallery = null;
$error = '';
$success = '';

// Get gallery data
$query = "SELECT * FROM gallery WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $gallery_id);
$stmt->execute();
$result = $stmt->get_result();
$gallery = $result->fetch_assoc();

if (!$gallery) {
    redirect('gallery.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    
    $gambar = $gallery['gambar'];
    
    // Handle file upload
    if (!empty($_FILES['gambar']['name'])) {
        $upload = uploadFile($_FILES['gambar'], '../../uploads/gallery');
        if ($upload['success']) {
            // Delete old image
            if ($gallery['gambar'] && file_exists('../../uploads/gallery/' . $gallery['gambar'])) {
                unlink('../../uploads/gallery/' . $gallery['gambar']);
            }
            $gambar = $upload['filename'];
        } else {
            $error = $upload['message'];
        }
    }
    
    if (empty($error)) {
        if (empty($judul)) {
            $error = 'Judul harus diisi';
        } else {
            $update_query = "UPDATE gallery SET judul = ?, deskripsi = ?, gambar = ?, kategori = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssssi", $judul, $deskripsi, $gambar, $kategori, $gallery_id);
            
            if ($update_stmt->execute()) {
                $success = 'Gambar berhasil diperbarui!';
                $gallery['judul'] = $judul;
                $gallery['deskripsi'] = $deskripsi;
                $gallery['gambar'] = $gambar;
                $gallery['kategori'] = $kategori;
            } else {
                $error = 'Gagal memperbarui gambar';
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
    <title>Edit Gallery - Admin Desa Pancasila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .bg-accent { background-color: #8B0000; }
        .text-accent { color: #8B0000; }
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
        <a href="gallery.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <header class="bg-white shadow">
            <div class="px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Edit Gambar</h2>
                <a href="gallery.php" class="text-accent font-bold hover:underline">← Kembali</a>
            </div>
        </header>

        <main class="p-6">
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Judul Gambar</label>
                        <input type="text" name="judul" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" value="<?php echo htmlspecialchars($gallery['judul']); ?>">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none"><?php echo htmlspecialchars($gallery['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                        <select name="kategori" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                            <option value="">Pilih Kategori</option>
                            <option value="Pemandangan" <?php echo $gallery['kategori'] === 'Pemandangan' ? 'selected' : ''; ?>>Pemandangan</option>
                            <option value="Acara" <?php echo $gallery['kategori'] === 'Acara' ? 'selected' : ''; ?>>Acara</option>
                            <option value="Masyarakat" <?php echo $gallery['kategori'] === 'Masyarakat' ? 'selected' : ''; ?>>Masyarakat</option>
                            <option value="Infrastruktur" <?php echo $gallery['kategori'] === 'Infrastruktur' ? 'selected' : ''; ?>>Infrastruktur</option>
                            <option value="Budaya" <?php echo $gallery['kategori'] === 'Budaya' ? 'selected' : ''; ?>>Budaya</option>
                            <option value="Lainnya" <?php echo $gallery['kategori'] === 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Gambar Saat Ini</label>
                        <img src="../../uploads/gallery/<?php echo htmlspecialchars($gallery['gambar']); ?>" alt="<?php echo htmlspecialchars($gallery['judul']); ?>" class="max-w-xs h-auto rounded-lg mb-4">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Ganti Gambar</label>
                        <input type="file" name="gambar" accept="image/*" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2">
                        <p class="text-sm text-gray-600 mt-2">Kosongkan jika tidak ingin mengubah gambar</p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-accent text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Simpan Perubahan
                        </button>
                        <a href="gallery-delete.php?id=<?php echo $gallery['id']; ?>" class="flex-1 bg-red-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 text-center" onclick="return confirm('Yakin hapus?')">
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
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar.contains(event.target) && !event.target.matches('button')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>

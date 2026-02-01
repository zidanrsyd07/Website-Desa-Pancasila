<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    
    if (empty($_FILES['gambar']['name'])) {
        $error = 'Silakan pilih gambar';
    } elseif (empty($judul)) {
        $error = 'Judul harus diisi';
    } else {
        $upload = uploadFile($_FILES['gambar'], '../../uploads/gallery');
        if ($upload['success']) {
            $gambar = $upload['filename'];
            $query = "INSERT INTO gallery (judul, deskripsi, gambar, kategori) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssss", $judul, $deskripsi, $gambar, $kategori);
            
            if ($stmt->execute()) {
                $success = 'Gambar berhasil ditambahkan!';
                $_POST = array();
            } else {
                $error = 'Gagal menambahkan gambar';
            }
        } else {
            $error = $upload['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gallery - Admin Desa Pancasila</title>
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
                <h2 class="text-2xl font-bold text-gray-900">Tambah Gambar</h2>
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
                        <a href="gallery.php" class="block font-bold mt-2 hover:underline">Ke daftar gallery</a>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Judul Gambar</label>
                        <input type="text" name="judul" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Masukkan judul gambar" value="<?php echo htmlspecialchars($_POST['judul'] ?? ''); ?>">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="3" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Deskripsi gambar"><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                        <select name="kategori" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                            <option value="">Pilih Kategori</option>
                            <option value="Pemandangan">Pemandangan</option>
                            <option value="Acara">Acara</option>
                            <option value="Masyarakat">Masyarakat</option>
                            <option value="Infrastruktur">Infrastruktur</option>
                            <option value="Budaya">Budaya</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Gambar</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-accent transition">
                            <input type="file" name="gambar" accept="image/*" required class="hidden" id="fileInput" onchange="previewImage()">
                            <label for="fileInput" class="cursor-pointer block">
                                <span class="text-4xl mb-2 block">🖼️</span>
                                <span class="text-gray-700 font-semibold">Klik untuk upload gambar</span>
                                <p class="text-xs text-gray-600 mt-2">atau drag & drop</p>
                            </label>
                        </div>
                        <img id="preview" style="display:none" class="mt-4 max-w-xs h-auto rounded-lg">
                        <p class="text-sm text-gray-600 mt-2">Format: JPG, PNG, GIF | Max: 5MB</p>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-accent text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Upload Gambar
                        </button>
                        <a href="gallery.php" class="flex-1 bg-gray-300 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 text-center">
                            Batal
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
        
        function previewImage() {
            const file = document.getElementById('fileInput').files[0];
            const preview = document.getElementById('preview');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
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

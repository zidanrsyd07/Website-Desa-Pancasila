<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$user = getUserById($conn, $_SESSION['user_id']);
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Gallery - Admin Desa Pancasila</title>
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
                <h2 class="text-2xl font-bold text-gray-900">Kelola Gallery</h2>
                <a href="gallery-add.php" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-800 transition">+ Tambah Gambar</a>
            </div>
        </header>

        <main class="p-6">
            <!-- Search -->
            <div class="mb-6">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari gallery..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                    <button type="submit" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90">Cari</button>
                </form>
            </div>

            <!-- Gallery Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $query = "SELECT * FROM gallery";
                if (!empty($search)) {
                    $search_param = '%' . $search . '%';
                    $query .= " WHERE judul LIKE ? OR deskripsi LIKE ?";
                }
                $query .= " ORDER BY tanggal_upload DESC";

                if (!empty($search)) {
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("ss", $search_param, $search_param);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $conn->query($query);
                }

                if ($result && $result->num_rows > 0) {
                    while ($gallery = $result->fetch_assoc()) {
                        echo '<div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">';
                        echo '<div class="relative">';
                        echo '<img src="../../uploads/gallery/' . htmlspecialchars($gallery['gambar']) . '" alt="' . htmlspecialchars($gallery['judul']) . '" class="w-full h-48 object-cover">';
                        echo '<div class="absolute top-2 right-2 bg-gray-900 bg-opacity-70 text-white px-3 py-1 rounded-full text-xs">' . htmlspecialchars($gallery['kategori'] ?? 'Uncategorized') . '</div>';
                        echo '</div>';
                        echo '<div class="p-4">';
                        echo '<h3 class="font-bold text-gray-900">' . htmlspecialchars($gallery['judul']) . '</h3>';
                        echo '<p class="text-sm text-gray-600 mt-2 line-clamp-2">' . htmlspecialchars(substr($gallery['deskripsi'], 0, 100)) . '</p>';
                        echo '<div class="flex gap-2 mt-4">';
                        echo '<a href="gallery-edit.php?id=' . $gallery['id'] . '" class="flex-1 text-center bg-blue-500 text-white px-3 py-2 rounded font-semibold text-sm hover:bg-blue-600">Edit</a>';
                        echo '<a href="gallery-delete.php?id=' . $gallery['id'] . '" class="flex-1 text-center bg-red-600 text-white px-3 py-2 rounded font-semibold text-sm hover:bg-red-700" onclick="return confirm(\'Yakin hapus?\')">Hapus</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="col-span-3 text-center text-gray-600 py-8">Belum ada gallery</p>';
                }
                ?>
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

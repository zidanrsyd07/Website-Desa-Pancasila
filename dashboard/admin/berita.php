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
    <title>Kelola Berita - Admin Desa Pancasila</title>
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
        <a href="berita.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
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
                <h2 class="text-2xl font-bold text-gray-900">Kelola Berita</h2>
                <a href="berita-add.php" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90">+ Tambah Berita</a>
            </div>
        </header>

        <main class="p-6">
            <!-- Search -->
            <div class="mb-6">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari berita..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                    <button type="submit" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90">Cari</button>
                </form>
            </div>

            <!-- Berita Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-700">Judul</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-700">Views</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-700">Tanggal</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php
                        $query = "SELECT * FROM berita";
                        if (!empty($search)) {
                            $search_param = '%' . $search . '%';
                            $query .= " WHERE judul LIKE ? OR deskripsi LIKE ?";
                        }
                        $query .= " ORDER BY created_at DESC";

                        if (!empty($search)) {
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("ss", $search_param, $search_param);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $result = $conn->query($query);
                        }

                        if ($result && $result->num_rows > 0) {
                            while ($berita = $result->fetch_assoc()) {
                                $status_color = $berita['status'] === 'published' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50';
                                echo '<tr class="hover:bg-gray-50">';
                                echo '<td class="px-6 py-4 font-semibold text-gray-900">' . htmlspecialchars(substr($berita['judul'], 0, 40)) . '</td>';
                                echo '<td class="px-6 py-4"><span class="px-3 py-1 rounded-full text-xs font-bold ' . $status_color . '">' . ucfirst($berita['status']) . '</span></td>';
                                echo '<td class="px-6 py-4 text-gray-600">' . $berita['views'] . '</td>';
                                echo '<td class="px-6 py-4 text-gray-600">' . formatTanggal($berita['created_at']) . '</td>';
                                echo '<td class="px-6 py-4 space-x-2">';
                                echo '<a href="berita-edit.php?id=' . $berita['id'] . '" class="text-blue-600 font-bold hover:underline">Edit</a>';
                                echo '<a href="berita-delete.php?id=' . $berita['id'] . '" class="text-red-600 font-bold hover:underline" onclick="return confirm(\'Yakin hapus?\')">Hapus</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-600">Belum ada berita</td></tr>';
                        }
                        ?>
                    </tbody>
                </table>
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

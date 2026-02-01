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
    <title>Kelola Peta - Admin Desa Pancasila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
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
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Kelola Peta</a>
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
                <h2 class="text-2xl font-bold text-gray-900">Kelola Peta</h2>
                <a href="peta-add.php" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-red-800 transition">+ Tambah Lokasi</a>
            </div>
        </header>

        <main class="p-6">
            <!-- Search -->
            <div class="mb-6">
                <form method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Cari lokasi..." value="<?php echo htmlspecialchars($search); ?>" class="flex-1 border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                    <button type="submit" class="bg-accent text-white px-6 py-2 rounded-lg font-semibold hover:bg-opacity-90">Cari</button>
                </form>
            </div>

            <!-- Locations Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200 border-b-2 border-gray-300">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900">Nama Lokasi</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900">Kategori</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900">Alamat</th>
                            <th class="px-6 py-4 text-left font-semibold text-gray-900">Status</th>
                            <th class="px-6 py-4 text-center font-semibold text-gray-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM map_locations";
                        if (!empty($search)) {
                            $search_param = '%' . $search . '%';
                            $query .= " WHERE nama LIKE ? OR deskripsi LIKE ?";
                        }
                        $query .= " ORDER BY nama ASC";

                        if (!empty($search)) {
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("ss", $search_param, $search_param);
                            $stmt->execute();
                            $result = $stmt->get_result();
                        } else {
                            $result = $conn->query($query);
                        }

                        if ($result && $result->num_rows > 0) {
                            while ($location = $result->fetch_assoc()) {
                                $icon = match($location['kategori']) {
                                    'Kantor' => '🏛️',
                                    'Sekolah' => '🎓',
                                    'Kesehatan' => '🏥',
                                    'Ibadah' => '🕌',
                                    'Olahraga' => '⚽',
                                    'Pertanian' => '🌾',
                                    'Pariwisata' => '🎫',
                                    default => '📍'
                                };
                                $status_badge = $location['status'] === 'aktif' 
                                    ? '<span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">Aktif</span>'
                                    : '<span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold">Nonaktif</span>';
                                
                                echo '<tr class="border-b border-gray-200 hover:bg-gray-50 transition">';
                                echo '<td class="px-6 py-4"><div class="flex items-center gap-2"><span class="text-xl">' . $icon . '</span><span class="font-semibold">' . htmlspecialchars($location['nama']) . '</span></div></td>';
                                echo '<td class="px-6 py-4">' . htmlspecialchars($location['kategori']) . '</td>';
                                echo '<td class="px-6 py-4 text-sm text-gray-600">' . htmlspecialchars(substr($location['alamat'], 0, 50)) . '...</td>';
                                echo '<td class="px-6 py-4">' . $status_badge . '</td>';
                                echo '<td class="px-6 py-4 text-center flex gap-2 justify-center">';
                                echo '<a href="peta-edit.php?id=' . $location['id'] . '" class="text-blue-500 hover:text-blue-700 font-semibold">Edit</a>';
                                echo '<a href="peta-delete.php?id=' . $location['id'] . '" class="text-red-600 hover:text-red-800 font-semibold" onclick="return confirm(\'Yakin hapus?\')">Hapus</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        } else {
                            echo '<tr><td colspan="5" class="px-6 py-8 text-center text-gray-600">Belum ada lokasi</td></tr>';
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

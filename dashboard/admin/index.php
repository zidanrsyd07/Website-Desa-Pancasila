<?php
require_once '../../config/config.php';
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../../dashboard/user/index.php");
    exit;
}

$user = getUserById($conn, $_SESSION['user_id']);
$desa_info = getDesaInfo($conn);

// Get statistics
$total_berita = $conn->query("SELECT COUNT(*) as count FROM berita")->fetch_assoc()['count'];
$total_gallery = $conn->query("SELECT COUNT(*) as count FROM gallery")->fetch_assoc()['count'];
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_locations = $conn->query("SELECT COUNT(*) as count FROM map_locations")->fetch_assoc()['count'];
$total_umkm = $conn->query("SELECT COUNT(*) as count FROM umkm")->fetch_assoc()['count'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Desa Pancasila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .bg-accent { background-color: #8B0000; }
        .text-accent { color: #8B0000; }
        .sidebar.active { transform: translateX(0); }
        .sidebar { transform: translateX(-100%); }
        @media (min-width: 768px) {
            .sidebar { transform: translateX(0) !important; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Mobile Menu Toggle -->
    <button onclick="toggleSidebar()" class="md:hidden fixed top-4 left-4 z-50 bg-accent text-white p-2 rounded-lg">
        ☰
    </button>

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
        <a href="index.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Dashboard</a>
        <a href="berita.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Berita</a>
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
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Dashboard</h2>
                <div class="text-right">
                    <p class="text-gray-600 text-sm">Selamat datang,</p>
                    <p class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($user['full_name']); ?></p>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="p-6 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-accent">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">Total Berita</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $total_berita; ?></p>
                        </div>
                        <span class="text-4xl">📰</span>
                    </div>
                    <a href="berita.php" class="text-accent font-semibold text-sm mt-4 inline-block hover:underline">Lihat semua →</a>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">Total Gallery</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $total_gallery; ?></p>
                        </div>
                        <span class="text-4xl">🖼️</span>
                    </div>
                    <a href="gallery.php" class="text-blue-500 font-semibold text-sm mt-4 inline-block hover:underline">Lihat semua →</a>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">Total Lokasi Peta</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $total_locations; ?></p>
                        </div>
                        <span class="text-4xl">📍</span>
                    </div>
                    <a href="peta.php" class="text-purple-500 font-semibold text-sm mt-4 inline-block hover:underline">Lihat semua →</a>
                </div>
                
                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
    <div class="flex justify-between items-start">
        <div>
            <p class="text-gray-600 text-sm font-semibold">Total UMKM</p>
            <p class="text-4xl font-bold text-gray-900 mt-2">
                <?php echo $total_umkm; ?>
            </p>
        </div>
        <span class="text-4xl">🛍️</span>
    </div>
    <a href="belanja.php" class="text-orange-500 font-semibold text-sm mt-4 inline-block hover:underline">
        Lihat semua →
    </a>
</div>

                <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-600 text-sm font-semibold">Total Users</p>
                            <p class="text-4xl font-bold text-gray-900 mt-2"><?php echo $total_users; ?></p>
                        </div>
                        <span class="text-4xl">👥</span>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Informasi Desa</h3>
                    <div class="space-y-3 text-gray-700">
                        <div class="flex justify-between">
                            <span class="font-semibold">Jumlah Penduduk:</span>
                            <span class="font-bold text-accent"><?php echo number_format($desa_info['jumlah_penduduk']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Jumlah Dusun:</span>
                            <span class="font-bold text-accent"><?php echo $desa_info['jumlah_dusun']; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold">Rumah Tangga:</span>
                            <span class="font-bold text-accent"><?php echo number_format($desa_info['jumlah_rumah_tangga']); ?></span>
                        </div>
                        <hr class="my-3">
                        <a href="desa-info.php" class="text-accent font-bold hover:underline">Edit Informasi →</a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-2">
                        <a href="berita-add.php" class="block bg-accent text-white px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90 text-center">Tambah Berita Baru</a>
                        <a href="gallery-add.php" class="block bg-blue-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90 text-center">Tambah Gallery</a>
                        <a href="peta-add.php" class="block bg-purple-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90 text-center">Tambah Lokasi Peta</a>
                        <a href="belanja-add.php" class="block bg-orange-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90 text-center">
                        Tambah UMKM
                        </a>

                        <a href="desa-info.php" class="block bg-green-500 text-white px-4 py-2 rounded-lg font-semibold hover:bg-opacity-90 text-center">Edit Info Desa</a>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
    <h3 class="text-xl font-bold text-gray-900 mb-4">UMKM Terbaru</h3>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Nama UMKM</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Pemilik</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Kontak</th>
                    <th class="px-4 py-3 text-left font-bold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php
                $umkm = $conn->query("SELECT * FROM umkm ORDER BY created_at DESC LIMIT 5");
                if ($umkm && $umkm->num_rows > 0):
                    while ($u = $umkm->fetch_assoc()):
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold text-gray-900">
                        <?php echo htmlspecialchars($u['nama_usaha']); ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php echo htmlspecialchars($u['pemilik']); ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php echo htmlspecialchars($u['kontak']); ?>
                    </td>
                    <td class="px-4 py-3">
                        <a href="belanja-edit.php?id=<?php echo $u['id']; ?>"
                           class="text-accent font-bold hover:underline">
                           Edit
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr>
                    <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                        Belum ada UMKM
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <a href="belanja.php"
       class="text-orange-500 font-bold hover:underline mt-4 inline-block">
       Lihat semua UMKM →
    </a>
</div>
            <!-- Recent News -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Berita Terbaru</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 text-left font-bold text-gray-700">Judul</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700">Status</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700">Tanggal</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php
                            $query = "SELECT * FROM berita ORDER BY created_at DESC LIMIT 5";
                            $result = $conn->query($query);
                            if ($result && $result->num_rows > 0) {
                                while ($berita = $result->fetch_assoc()) {
                                    $status_color = $berita['status'] === 'published' ? 'text-green-600 bg-green-50' : 'text-yellow-600 bg-yellow-50';
                                    echo '<tr class="hover:bg-gray-50">';
                                    echo '<td class="px-4 py-3 font-semibold text-gray-900">' . htmlspecialchars(substr($berita['judul'], 0, 30)) . '...</td>';
                                    echo '<td class="px-4 py-3"><span class="px-3 py-1 rounded-full text-xs font-bold ' . $status_color . '">' . ucfirst($berita['status']) . '</span></td>';
                                    echo '<td class="px-4 py-3 text-gray-600">' . formatTanggal($berita['created_at']) . '</td>';
                                    echo '<td class="px-4 py-3"><a href="berita-edit.php?id=' . $berita['id'] . '" class="text-accent font-bold hover:underline">Edit</a></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="4" class="px-4 py-3 text-center text-gray-600">Belum ada berita</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <a href="berita.php" class="text-accent font-bold hover:underline mt-4 inline-block">Lihat semua berita →</a>
            </div>
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar.contains(event.target) && !event.target.matches('button')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>

<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $latitude = floatval($_POST['latitude'] ?? 0);
    $longitude = floatval($_POST['longitude'] ?? 0);
    $status = trim($_POST['status'] ?? 'aktif');
    
    if (empty($nama)) {
        $error = 'Nama lokasi harus diisi';
    } elseif (empty($kategori)) {
        $error = 'Kategori harus dipilih';
    } elseif ($latitude == 0 || $longitude == 0) {
        $error = 'Silakan klik pada peta untuk menentukan lokasi';
    } else {
        $query = "INSERT INTO map_locations (nama, kategori, deskripsi, alamat, latitude, longitude, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssdds", $nama, $kategori, $deskripsi, $alamat, $latitude, $longitude, $status);
        
        if ($stmt->execute()) {
            $success = 'Lokasi berhasil ditambahkan!';
            $_POST = array();
        } else {
            $error = 'Gagal menambahkan lokasi';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lokasi - Admin Desa Pancasila</title>
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
        #addLocationMap { height: 400px; }
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
                <h2 class="text-2xl font-bold text-gray-900">Tambah Lokasi Baru</h2>
                <a href="peta.php" class="text-accent font-bold hover:underline">← Kembali</a>
            </div>
        </header>

        <main class="p-6">
            <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg p-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($success); ?>
                        <a href="peta.php" class="block font-bold mt-2 hover:underline">Ke daftar lokasi</a>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <!-- Form Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Nama Lokasi</label>
                            <input type="text" name="nama" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Contoh: Kantor Desa" value="<?php echo htmlspecialchars($_POST['nama'] ?? ''); ?>">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Kategori</label>
                            <select name="kategori" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                                <option value="">Pilih Kategori</option>
                                <option value="Kantor">Kantor</option>
                                <option value="Sekolah">Sekolah</option>
                                <option value="Kesehatan">Kesehatan</option>
                                <option value="Ibadah">Ibadah</option>
                                <option value="Olahraga">Olahraga</option>
                                <option value="Pertanian">Pertanian</option>
                                <option value="Pariwisata">Pariwisata</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                        <input type="text" name="alamat" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Alamat lengkap lokasi" value="<?php echo htmlspecialchars($_POST['alamat'] ?? ''); ?>">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Deskripsi singkat tentang lokasi"><?php echo htmlspecialchars($_POST['deskripsi'] ?? ''); ?></textarea>
                    </div>

                    <!-- Map Section -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Pilih Lokasi di Peta</label>
                        <p class="text-sm text-gray-600 mb-3">Klik pada peta untuk menentukan koordinat lokasi</p>
                        <div id="addLocationMap" class="rounded-lg border-2 border-gray-300"></div>
                    </div>

                    <!-- Coordinates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-4 rounded-lg">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Latitude</label>
                            <input type="number" name="latitude" id="latitude" step="0.0001" readonly class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 bg-gray-100" placeholder="Klik peta" value="<?php echo htmlspecialchars($_POST['latitude'] ?? ''); ?>">
                        </div>

                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Longitude</label>
                            <input type="number" name="longitude" id="longitude" step="0.0001" readonly class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 bg-gray-100" placeholder="Klik peta" value="<?php echo htmlspecialchars($_POST['longitude'] ?? ''); ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Status</label>
                        <select name="status" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-accent text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Tambah Lokasi
                        </button>
                        <a href="peta.php" class="flex-1 bg-gray-300 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 text-center">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        let addLocationMap;
        let marker;

        function initAddLocationMap() {
            addLocationMap = L.map('addLocationMap').setView([-7.5, 110.5], 13);
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(addLocationMap);

            // Click on map to add marker
            addLocationMap.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                
                // Remove previous marker
                if (marker) {
                    addLocationMap.removeLayer(marker);
                }
                
                // Add new marker
                marker = L.marker([lat, lng]).addTo(addLocationMap);
                
                // Update form fields
                document.getElementById('latitude').value = lat.toFixed(6);
                document.getElementById('longitude').value = lng.toFixed(6);
            });

            // Load existing marker if editing
            const lat = document.getElementById('latitude').value;
            const lng = document.getElementById('longitude').value;
            if (lat && lng) {
                marker = L.marker([parseFloat(lat), parseFloat(lng)]).addTo(addLocationMap);
                addLocationMap.setView([parseFloat(lat), parseFloat(lng)], 16);
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        document.addEventListener('DOMContentLoaded', function() {
            initAddLocationMap();
        });

        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            if (!sidebar.contains(event.target) && !event.target.matches('button')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>

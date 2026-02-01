<?php
session_start();
require_once 'config/config.php';

// Create upload directories if not exist
if (!is_dir('uploads')) mkdir('uploads', 0755, true);
if (!is_dir('uploads/news')) mkdir('uploads/news', 0755, true);
if (!is_dir('uploads/gallery')) mkdir('uploads/gallery', 0755, true);

$desa_info = getDesaInfo($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Desa Pancasila - Beranda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        
        body {
            font-family: 'Poppins', sans-serif;
        }
        
        .hero-bg {
    background:
        linear-gradient(
            135deg,
            rgba(139, 0, 0, 0.8) 0%,
            rgba(0, 0, 0, 0.7) 100%
        ),
        url('assets/backgrounds/tugu.jpg');

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
}

        
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .text-accent {
            color: #8B0000;
        }
        
        .btn-accent {
            background-color: #8B0000;
        }
        
        .btn-accent:hover {
            background-color: #700000;
        }
        
        .card-stats {
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
        }
        
        .icon-stats {
            font-size: 2.5rem;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header/Navbar -->
    <?php include 'partials/navbar.php'; ?>

    <!-- Hero Section -->
    <section id="beranda" class="hero-bg min-h-screen flex items-center justify-center text-center text-white py-20">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="mb-4 inline-block bg-white bg-opacity-20 px-4 py-2 rounded-full backdrop-blur">
                <p class="text-sm font-semibold">Selamat Datang di Desa Kami</p>
            </div>
            <h2 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">Selamat Datang di <span class="text-yellow-300">Desa Pancasila</span></h2>
            <p class="text-xl md:text-2xl mb-8 text-gray-100">Desa yang indah dengan keraifan lokal dan potensi alam yang melimpah, menjadi masa depan yang berkelanjutan</p>
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <button onclick="document.getElementById('berita').scrollIntoView({behavior: 'smooth'})" class="btn-accent text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    Tentang Desa
                </button>
            <a
                href="auth/login.php"
                class="inline-block bg-white bg-opacity-20 text-white px-8 py-3 rounded-lg font-semibold hover:bg-opacity-30 backdrop-blur transition"
            >
                Jelajahi Sekarang
            </a>

            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-16 bg-white relative -mt-12 mx-4 md:mx-auto max-w-5xl rounded-2xl shadow-2xl">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="card-stats p-6 text-center">
                    <div class="icon-stats text-accent mb-3 flex justify-center">👥</div>
                    <h3 class="text-4xl font-bold text-gray-900"><?php echo number_format($desa_info['jumlah_penduduk']); ?></h3>
                    <p class="text-gray-600 font-semibold mt-2">Jumlah Penduduk</p>
                </div>
                <div class="card-stats p-6 text-center">
                    <div class="icon-stats text-accent mb-3 flex justify-center">🏘️</div>
                    <h3 class="text-4xl font-bold text-gray-900"><?php echo $desa_info['jumlah_dusun']; ?></h3>
                    <p class="text-gray-600 font-semibold mt-2">Jumlah Dusun</p>
                </div>
                <div class="card-stats p-6 text-center">
                    <div class="icon-stats text-accent mb-3 flex justify-center">🏠</div>
                    <h3 class="text-4xl font-bold text-gray-900"><?php echo number_format($desa_info['jumlah_rumah_tangga']); ?></h3>
                    <p class="text-gray-600 font-semibold mt-2">Rumah Tangga</p>
                </div>
                <div class="card-stats p-6 text-center">
                    <div class="icon-stats text-accent mb-3 flex justify-center">📍</div>
                    <h3 class="text-2xl font-bold text-gray-900">Aktif</h3>
                    <p class="text-gray-600 font-semibold mt-2">Status Desa</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="berita" class="py-20 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Tentang Desa</h2>
                <div class="w-20 h-1 bg-accent mx-auto rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <img src="assets/backgrounds/sawah.jpg"
     alt="Desa Pancasila"
     class="rounded-xl shadow-lg">

                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-4">Sejarah & Warisan Budaya</h3>
                    <p class="text-gray-700 mb-4 leading-relaxed"><?php echo $desa_info['sejarah_desa']; ?></p>
                    <p class="text-gray-700 mb-6">Desa Pancasila terus berkembang dengan mempertahankan nilai-nilai tradisional sambil merangkul modernisasi untuk kesejahteraan masyarakat.</p>
                    <div class="flex gap-4">
                        <div>
                            <p class="text-2xl font-bold text-accent"><?php echo $desa_info['jumlah_dusun']; ?></p>
                            <p class="text-gray-600">Dusun</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-accent"><?php echo number_format($desa_info['jumlah_penduduk']); ?></p>
                            <p class="text-gray-600">Penduduk</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-accent"><?php echo number_format($desa_info['jumlah_rumah_tangga']); ?></p>
                            <p class="text-gray-600">Rumah Tangga</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Profile Desa -->
<section id="video-profil" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-2">
                Video Profil Desa
            </h2>
            <div class="w-24 h-1 bg-accent mx-auto rounded"></div>
            <p class="text-gray-600 mt-4 max-w-2xl mx-auto">
                Gambaran singkat Desa Pancasila melalui video dokumentasi kegiatan,
                budaya, dan potensi desa.
            </p>
        </div>

        <div class="max-w-5xl mx-auto">
            <div class="relative w-full overflow-hidden rounded-2xl shadow-2xl"
                 style="padding-top: 56.25%;">
                
                <iframe
                    class="absolute top-0 left-0 w-full h-full"
                    src="https://www.youtube.com/embed/o9baNOXFNsw?si=wfyEEwYB3BbCLkti"
                    title="Video Profil Desa Pancasila"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>

            </div>
        </div>
    </div>
</section>

    <!-- Map Section -->
    <section id="peta" class="pt-32 pb-20 bg-gradient-to-br from-blue-50 to-blue-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Peta Lokasi Desa Pancasila</h2>
                <div class="w-20 h-1 bg-accent mx-auto rounded"></div>
            </div>
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
               <div id="map" class="relative z-10" style="height: 500px; width: 100%;"></div>
            </div>
            <div class="text-center mt-8">
                <a href="auth/login.php" class="inline-block btn-accent text-white px-8 py-3 rounded-lg font-semibold hover:shadow-lg transition">
                    Lihat Peta Lengkap
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="galeri" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Galeri Desa</h2>
                <div class="w-20 h-1 bg-accent mx-auto rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php
                $query = "SELECT * FROM gallery ORDER BY tanggal_upload DESC LIMIT 6";
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($gallery = $result->fetch_assoc()) {
                        echo '<div class="rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition">';
                        echo '<img src="uploads/gallery/' . htmlspecialchars($gallery['gambar']) . '" alt="' . htmlspecialchars($gallery['judul']) . '" class="w-full h-64 object-cover hover:scale-110 transition duration-300">';
                        echo '<div class="p-4 bg-gray-50">';
                        echo '<h3 class="font-bold text-gray-900 text-lg">' . htmlspecialchars($gallery['judul']) . '</h3>';
                        echo '<p class="text-sm text-gray-600 mt-2">' . htmlspecialchars(substr($gallery['deskripsi'], 0, 100)) . '...</p>';
                        echo '</div></div>';
                    }
                } else {
                    echo '<p class="text-center text-gray-600 col-span-3">Belum ada galeri</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section class="py-20 bg-gray-100">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Berita & Informasi Terbaru</h2>
                <div class="w-20 h-1 bg-accent mx-auto rounded"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php
                $query = "SELECT * FROM berita WHERE status='published' ORDER BY created_at DESC LIMIT 6";
                $result = $conn->query($query);
                if ($result && $result->num_rows > 0) {
                    while ($berita = $result->fetch_assoc()) {
                        echo '<div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">';
                        if ($berita['gambar']) {
                            echo '<img src="uploads/news/' . htmlspecialchars($berita['gambar']) . '" alt="' . htmlspecialchars($berita['judul']) . '" class="w-full h-48 object-cover">';
                        } else {
                            echo '<div class="w-full h-48 bg-accent/20 flex items-center justify-center"><span class="text-gray-400">No Image</span></div>';
                        }
                        echo '<div class="p-6">';
                        echo '<h3 class="text-xl font-bold text-gray-900 mb-2">' . htmlspecialchars($berita['judul']) . '</h3>';
                        echo '<p class="text-gray-600 mb-4 leading-relaxed">' . htmlspecialchars(substr($berita['deskripsi'], 0, 150)) . '...</p>';
                        echo '<div class="flex justify-between items-center text-sm text-gray-500">';
                        echo '<span>' . formatTanggal($berita['created_at']) . '</span>';
                        echo '<span class="text-accent font-semibold">👁 ' . $berita['views'] . '</span>';
                        echo '</div></div></div>';
                    }
                } else {
                    echo '<p class="text-center text-gray-600 col-span-3">Belum ada berita</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'partials/footer.php'; ?>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
            <div class="bg-accent text-white p-6 rounded-t-xl">
                <h2 class="text-2xl font-bold">Login</h2>
            </div>
            <div class="p-8">
                <form method="POST" action="auth/login.php">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Username</label>
                        <input type="text" name="username" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none transition" placeholder="Masukkan username">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 font-semibold mb-2">Password</label>
                        <input type="password" name="password" required class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none transition" placeholder="Masukkan password">
                    </div>
                    <button type="submit" class="w-full btn-accent text-white font-bold py-3 rounded-lg hover:shadow-lg transition">
                        Login
                    </button>
                </form>
                <p class="text-center text-gray-600 mt-4">
                    Belum punya akun? <a href="auth/register.php" class="text-accent font-bold hover:underline">Daftar sekarang</a>
                </p>
                <button onclick="closeLoginModal()" class="w-full text-gray-600 mt-4 font-semibold hover:text-gray-800">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openLoginModal() {
            document.getElementById('loginModal').classList.add('show');
        }

        function closeLoginModal() {
            document.getElementById('loginModal').classList.remove('show');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        }

        // Initialize preview map
        document.addEventListener('DOMContentLoaded', function () {
    setTimeout(() => {
        const map = L.map('map').setView([-5.255400, 105.265235], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        fetch('api/get-map-locations.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.locations) {
                    data.locations.forEach(location => {
                        const icon = L.divIcon({
                            html: `<div style="font-size: 24px;">${location.emoji}</div>`,
                            className: '',
                            iconSize: [30, 30],
                            iconAnchor: [15, 30]
                        });

                        L.marker(
                            [location.latitude, location.longitude],
                            { icon }
                        )
                        .bindPopup(
                            `<strong>${location.nama}</strong><br>${location.alamat}`
                        )
                        .addTo(map);
                    });
                }
            });

        // 🔥 WAJIB: refresh ukuran map
        setTimeout(() => {
            map.invalidateSize();
        }, 300);

    }, 300);
});


        function getMarkerColor(kategori) {
            const colors = {
                'kantor': '#FF6B6B',
                'sekolah': '#4ECDC4',
                'kesehatan': '#45B7D1',
                'ibadat': '#FFA07A',
                'lainnya': '#95E1D3'
            };
            return colors[kategori] || '#8B0000';
        }
    </script>
</body>
</html>

<?php
require_once 'config/config.php';

$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Desa Pancasila</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        .bg-accent { background-color: #8B0000; }
        .text-accent { color: #8B0000; }
        #map { height: 600px; }
        .marker-popup { max-width: 300px; }
    </style>
</head>
<body class="bg-gray-50">
    <?php include 'partials/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Peta Desa Pancasila</h1>
            <p class="text-gray-600 text-lg">Jelajahi lokasi-lokasi penting di Desa Pancasila</p>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <!-- Map -->
            <div class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                    <div id="map" class="rounded-xl"></div>
                </div>
            </div>

            <!-- Sidebar - Locations List -->
            <div class="bg-white rounded-xl shadow-lg p-6 h-fit">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Daftar Lokasi</h2>
                
                <!-- Search -->
                <form method="GET" class="mb-4">
                    <input type="text" name="search" placeholder="Cari lokasi..." value="<?php echo htmlspecialchars($search); ?>" class="w-full border-2 border-gray-300 rounded-lg px-3 py-2 focus:border-accent outline-none text-sm">
                </form>

                <!-- Locations List -->
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    <?php
                    $query = "SELECT * FROM map_locations WHERE status = 'aktif'";
                    if (!empty($search)) {
                        $query .= " AND (nama LIKE ? OR deskripsi LIKE ?)";
                    }
                    $query .= " ORDER BY nama ASC";

                    if (!empty($search)) {
                        $search_param = '%' . $search . '%';
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("ss", $search_param, $search_param);
                        $stmt->execute();
                        $result = $stmt->get_result();
                    } else {
                        $result = $conn->query($query);
                    }

                    if ($result && $result->num_rows > 0) {
                        while ($location = $result->fetch_assoc()) {
                            $icon_class = match($location['kategori']) {
                                'Kantor' => '🏛️',
                                'Sekolah' => '🎓',
                                'Kesehatan' => '🏥',
                                'Ibadah' => '🕌',
                                'Olahraga' => '⚽',
                                'Pertanian' => '🌾',
                                'Pariwisata' => '🎫',
                                default => '📍'
                            };
                            echo '<div class="border-2 border-gray-200 rounded-lg p-3 hover:border-accent hover:shadow-md transition cursor-pointer location-item" data-lat="' . htmlspecialchars($location['latitude']) . '" data-lng="' . htmlspecialchars($location['longitude']) . '">';
                            echo '<div class="flex items-start gap-2">';
                            echo '<span class="text-xl">' . $icon_class . '</span>';
                            echo '<div class="flex-1">';
                            echo '<h3 class="font-semibold text-gray-900 text-sm">' . htmlspecialchars($location['nama']) . '</h3>';
                            echo '<p class="text-xs text-gray-600">' . htmlspecialchars($location['kategori']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="text-center text-gray-600 text-sm py-4">Tidak ada lokasi</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'partials/footer.php'; ?>

    <script>
        // Initialize map - Center on Pancasila Village (example coordinates)
        const map = L.map('map').setView([-5.255400, 105.265235], 15);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        const locations = [];

        <?php
        $result = $conn->query("SELECT * FROM map_locations WHERE status = 'aktif' ORDER BY nama ASC");
        if ($result && $result->num_rows > 0) {
            while ($loc = $result->fetch_assoc()) {
                $icon_map = match($loc['kategori']) {
                    'Kantor' => 'bg-blue-500',
                    'Sekolah' => 'bg-purple-500',
                    'Kesehatan' => 'bg-red-500',
                    'Ibadah' => 'bg-green-500',
                    'Olahraga' => 'bg-yellow-500',
                    'Pertanian' => 'bg-emerald-500',
                    'Pariwisata' => 'bg-orange-500',
                    default => 'bg-gray-500'
                };
                $icon_emoji = match($loc['kategori']) {
                    'Kantor' => '🏛️',
                    'Sekolah' => '🎓',
                    'Kesehatan' => '🏥',
                    'Ibadah' => '🕌',
                    'Olahraga' => '⚽',
                    'Pertanian' => '🌾',
                    'Pariwisata' => '🎫',
                    default => '📍'
                };
        ?>
        locations.push({
            id: <?php echo $loc['id']; ?>,
            nama: '<?php echo addslashes(htmlspecialchars($loc['nama'])); ?>',
            kategori: '<?php echo htmlspecialchars($loc['kategori']); ?>',
            lat: <?php echo $loc['latitude']; ?>,
            lng: <?php echo $loc['longitude']; ?>,
            deskripsi: '<?php echo addslashes(htmlspecialchars($loc['deskripsi'])); ?>',
            alamat: '<?php echo addslashes(htmlspecialchars($loc['alamat'])); ?>',
            emoji: '<?php echo $icon_emoji; ?>'
        });
        <?php
            }
        }
        ?>

        // Add markers to map
        locations.forEach(function(location) {
            const marker = L.marker([location.lat, location.lng])
                .bindPopup(`
                    <div class="marker-popup">
                        <h3 class="font-bold text-lg mb-1">${location.emoji} ${location.nama}</h3>
                        <p class="text-sm text-gray-600 mb-2"><strong>Kategori:</strong> ${location.kategori}</p>
                        <p class="text-sm text-gray-600 mb-2"><strong>Alamat:</strong> ${location.alamat}</p>
                        <p class="text-sm text-gray-700">${location.deskripsi}</p>
                    </div>
                `)
                .addTo(map);
        });

        // Location list interaction
        document.querySelectorAll('.location-item').forEach(item => {
            item.addEventListener('click', function() {
                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                map.setView([lat, lng], 16);
            });
        });

        // Optional: Fit bounds to all markers if multiple locations
        if (locations.length > 1) {
            const group = new L.featureGroup(map._layers);
            map.fitBounds(group.getBounds().pad(0.1), { maxZoom: 15 });
        }
    </script>
</body>
</html>

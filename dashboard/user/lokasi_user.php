<?php
require_once '../../config/config.php';
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Desa Pancasila</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Leaflet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <style>
          @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');
        body { font-family: 'Poppins', sans-serif; }
        #map { height: 600px; width: 100%; z-index: 10; }
    </style>
</head>
<body class="bg-gray-50">

<?php include '../../partials/navbar_login.php'; ?>

<div class="container mx-auto px-4 py-8">

    <h1 class="text-4xl font-bold mb-2">Peta Desa Pancasila</h1>
    <p class="text-gray-600 mb-6">Klik peta untuk mengaktifkan zoom</p>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        <!-- MAP -->
        <div class="lg:col-span-3 bg-white rounded-xl shadow overflow-hidden">
            <div id="map"></div>
        </div>

        <!-- SIDEBAR -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="font-bold mb-4">Daftar Lokasi</h2>

            <form method="GET" class="mb-4">
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Cari lokasi..."
                    class="w-full border rounded px-3 py-2"
                >
            </form>

            <div class="space-y-3 max-h-[420px] overflow-y-auto">
                <?php
                $sql = "SELECT * FROM map_locations WHERE status='aktif'";
                if ($search) {
                    $sql .= " AND (nama LIKE ? OR deskripsi LIKE ?)";
                    $stmt = $conn->prepare($sql);
                    $s = "%$search%";
                    $stmt->bind_param("ss", $s, $s);
                    $stmt->execute();
                    $result = $stmt->get_result();
                } else {
                    $result = $conn->query($sql);
                }

                if ($result && $result->num_rows > 0):
                    while ($loc = $result->fetch_assoc()):
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
                <div
                    class="border rounded p-3 cursor-pointer hover:border-red-700 location-item"
                    data-lat="<?= $loc['latitude'] ?>"
                    data-lng="<?= $loc['longitude'] ?>"
                >
                    <strong><?= $icon_emoji ?> <?= htmlspecialchars($loc['nama']) ?></strong>
                    <p class="text-xs text-gray-500"><?= htmlspecialchars($loc['kategori']) ?></p>
                </div>
                <?php endwhile; else: ?>
                    <p class="text-sm text-gray-400 text-center">Tidak ada lokasi</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php include '../../partials/footer_login.php'; ?>

<script>
/* MAP */
const map = L.map('map', {
    scrollWheelZoom: false
}).setView([-5.2554, 105.2652], 15);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '© OpenStreetMap'
}).addTo(map);

map.on('click', () => map.scrollWheelZoom.enable());
map.on('mouseout', () => map.scrollWheelZoom.disable());

/* DATA */
const locations = [];

<?php
$q = $conn->query("SELECT * FROM map_locations WHERE status='aktif'");
if ($q):
while ($d = $q->fetch_assoc()):
    $emoji = match($d['kategori']) {
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
    nama: "<?= addslashes($d['nama']) ?>",
    lat: <?= $d['latitude'] ?>,
    lng: <?= $d['longitude'] ?>,
    kategori: "<?= addslashes($d['kategori']) ?>",
    alamat: "<?= addslashes($d['alamat']) ?>",
    deskripsi: "<?= addslashes($d['deskripsi']) ?>",
    emoji: "<?= $emoji ?>"
});
<?php endwhile; endif; ?>

locations.forEach(l => {
    L.marker([l.lat, l.lng]) // ICON DEFAULT LEAFLET (TIDAK DIUBAH)
        .addTo(map)
        .bindPopup(`
            <strong>${l.emoji} ${l.nama}</strong><br>
            ${l.kategori}<br>
            ${l.alamat}<br>
            <small>${l.deskripsi}</small>
        `);
});

document.querySelectorAll('.location-item').forEach(el => {
    el.onclick = () => {
        map.setView([el.dataset.lat, el.dataset.lng], 16);
    };
});
</script>

</body>
</html>

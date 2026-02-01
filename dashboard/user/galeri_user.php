<?php
session_start();
require_once '../../config/config.php';

$heroImage = "../../assets/backgrounds/tugu.jpg";

// Query galeri
$query = "SELECT * FROM gallery ORDER BY tanggal_upload DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Galeri Desa | Desa Pancasila</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }
        .text-accent {
            color: #8B0000;
        }
        .hero-bg {
            background:
                linear-gradient(135deg, rgba(139,0,0,.85), rgba(0,0,0,.7)),
                url('<?= htmlspecialchars($heroImage) ?>');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>

<body class="bg-white">

<!-- NAVBAR -->
<?php include '../../partials/navbar_login.php'; ?>

<!-- HERO -->
<section class="hero-bg py-24 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Galeri Desa
        </h1>
        <p class="text-gray-200">
            Dokumentasi kegiatan dan kehidupan masyarakat Desa Pancasila
        </p>
    </div>
</section>

<!-- GALLERY GRID -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($g = $result->fetch_assoc()): ?>
                    <?php $img = '../../uploads/gallery/' . $g['gambar']; ?>

                    <div
                        class="group relative rounded-xl overflow-hidden shadow-lg cursor-pointer"
                        onclick="openImageModal('<?= htmlspecialchars($img) ?>','<?= htmlspecialchars(addslashes($g['judul'])) ?>')"
                    >
                        <img
                            src="<?= htmlspecialchars($img) ?>"
                            alt="<?= htmlspecialchars($g['judul']) ?>"
                            class="w-full h-72 object-cover group-hover:scale-110 transition duration-300"
                        >

                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-end">
                            <div class="p-4 text-white">
                                <h3 class="font-bold text-lg">
                                    <?= htmlspecialchars($g['judul']) ?>
                                </h3>
                                <p class="text-sm text-gray-200">
                                    <?= htmlspecialchars(substr(strip_tags($g['deskripsi']), 0, 80)) ?>...
                                </p>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <p class="col-span-3 text-center text-gray-500">
                    Galeri belum tersedia.
                </p>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- IMAGE MODAL -->
<div
    id="imageModal"
    class="fixed inset-0 bg-black/80 hidden z-50 flex items-center justify-center px-4"
>
    <div class="relative max-w-4xl w-full">
        <button
            onclick="closeImageModal()"
            class="absolute -top-10 right-0 text-white text-3xl font-bold hover:text-gray-300"
        >
            ✕
        </button>

        <img
            id="modalImage"
            src=""
            alt=""
            class="w-full max-h-[80vh] object-contain rounded-xl shadow-2xl"
        >

        <p
            id="modalCaption"
            class="text-center text-white mt-4 font-semibold"
        ></p>
    </div>
</div>

<!-- FOOTER -->
<?php include '../../partials/footer.php'; ?>

<!-- SCRIPT -->
<script>
function openImageModal(src, title) {
    document.getElementById('modalImage').src = src;
    document.getElementById('modalCaption').innerText = title;
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
}

// klik background untuk tutup
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>

</body>
</html>

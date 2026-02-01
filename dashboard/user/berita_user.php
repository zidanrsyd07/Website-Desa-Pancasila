<?php
session_start();
require_once '../../config/config.php';

$heroImage = "../../assets/backgrounds/tugu.jpg";

// Query berita
$query = "SELECT * FROM berita 
          WHERE status = 'published' 
          ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Desa | Desa Pancasila</title>
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
        .bg-accent {
            background: #8B0000;
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

<body class="bg-gray-50">

<!-- NAVBAR -->
<?php include '../../partials/navbar_login.php'; ?>

<!-- HERO -->
<section class="hero-bg py-24 text-white">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Berita dan Informasi Desa
        </h1>
        <p class="text-gray-200 max-w-2xl mx-auto">
            Informasi resmi dan terbaru seputar kegiatan Desa Pancasila
        </p>
    </div>
</section>

<!-- CONTENT -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($b = $result->fetch_assoc()): ?>
                    <article class="bg-white rounded-xl shadow-lg overflow-hidden hover:-translate-y-1 hover:shadow-xl transition">

                        <?php if (!empty($b['gambar'])): ?>
                            <img
                                src="../../uploads/news/<?= htmlspecialchars($b['gambar']) ?>"
                                alt="<?= htmlspecialchars($b['judul']) ?>"
                                class="w-full h-48 object-cover"
                            >
                        <?php endif; ?>

                        <div class="p-6">
                            <span class="text-sm text-gray-500">
                                <?= formatTanggal($b['created_at']) ?>
                            </span>

                            <h3 class="text-xl font-bold text-gray-900 mt-2 mb-3">
                                <?= htmlspecialchars($b['judul']) ?>
                            </h3>

                            <p class="text-gray-600 mb-4">
                                <?= htmlspecialchars(substr(strip_tags($b['deskripsi']), 0, 130)) ?>...
                            </p>

                            <div class="flex justify-between items-center">
                                <a
                                    href="berita_detail.php?id=<?= (int)$b['id'] ?>"
                                    class="text-accent font-semibold hover:underline"
                                >
                                    Baca Selengkapnya →
                                </a>
                                <span class="text-sm text-gray-500">
                                    👁 <?= (int)$b['views'] ?>
                                </span>
                            </div>

                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="col-span-3 text-center text-gray-500">
                    Belum ada berita.
                </p>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include '../../partials/footer_login.php'; ?>

</body>
</html>

<?php
session_start();
require_once 'config/config.php';

/*
|--------------------------------------------------------------------------
| HERO BACKGROUND SETTING
|--------------------------------------------------------------------------
*/
$heroImage = "assets/backgrounds/hero-bg.jpg";

/*
|--------------------------------------------------------------------------
| VALIDASI ID BERITA
|--------------------------------------------------------------------------
*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: berita.php");
    exit;
}

$id = (int) $_GET['id'];

/*
|--------------------------------------------------------------------------
| AMBIL DATA BERITA
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT * FROM berita 
    WHERE id = ? AND status = 'published'
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: berita.php");
    exit;
}

$berita = $result->fetch_assoc();

/*
|--------------------------------------------------------------------------
| UPDATE VIEWS
|--------------------------------------------------------------------------
*/
$conn->query("UPDATE berita SET views = views + 1 WHERE id = $id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($berita['judul']) ?> | Desa Pancasila</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
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

<body class="bg-gray-50">

<!-- NAVBAR -->
<?php include 'partials/navbar.php'; ?>

<!-- HERO -->
<section class="hero-bg py-24 text-white">
    <div class="container mx-auto px-4 text-center max-w-3xl">
        <h1 class="text-3xl md:text-4xl font-bold mb-4">
            <?= htmlspecialchars($berita['judul']) ?>
        </h1>
        <p class="text-gray-200 text-sm">
            <?= formatTanggal($berita['created_at']) ?> · 👁 <?= (int)$berita['views'] + 1 ?>
        </p>
    </div>
</section>

<!-- CONTENT -->
<section class="py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">

            <?php if (!empty($berita['gambar'])): ?>
                <img
                    src="uploads/news/<?= htmlspecialchars($berita['gambar']) ?>"
                    alt="<?= htmlspecialchars($berita['judul']) ?>"
                    class="w-full h-96 object-cover"
                >
            <?php endif; ?>

            <div class="p-8 prose max-w-none">
                <?= $berita['deskripsi'] ?>
            </div>

            <div class="px-8 pb-8">
                <a href="berita.php" class="text-accent font-semibold hover:underline">
                    ← Kembali ke Daftar Berita
                </a>
            </div>

        </div>
    </div>
</section>

<!-- FOOTER -->
<?php include __DIR__ . '/partials/footer.php'; ?>

</body>
</html>

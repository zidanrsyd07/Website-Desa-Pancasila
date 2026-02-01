<?php
require_once '../../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../../auth/login.php');
}

$desa_info = getDesaInfo($conn);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah_penduduk     = (int)($_POST['jumlah_penduduk'] ?? 0);
    $jumlah_dusun        = (int)($_POST['jumlah_dusun'] ?? 0);
    $jumlah_rumah_tangga = (int)($_POST['jumlah_rumah_tangga'] ?? 0);
    $sejarah_desa        = trim($_POST['sejarah_desa'] ?? '');
    $alamat              = trim($_POST['alamat'] ?? '');
    $no_telepon          = trim($_POST['no_telepon'] ?? '');
    $email               = trim($_POST['email'] ?? '');

    if (empty($sejarah_desa)) {
        $error = 'Sejarah desa harus diisi';
    } else {

        $update_query = "
            UPDATE desa_info 
            SET jumlah_penduduk = ?, 
                jumlah_dusun = ?, 
                jumlah_rumah_tangga = ?, 
                sejarah_desa = ?, 
                alamat = ?, 
                no_telepon = ?, 
                email = ?
            WHERE id = ?
        ";

        $update_stmt = $conn->prepare($update_query);

        if (!$update_stmt) {
            $error = "Prepare gagal: " . $conn->error;
        } else {

            // 3 integer, 4 string, 1 integer = TOTAL 8
            $update_stmt->bind_param(
                "iiissssi",
                $jumlah_penduduk,
                $jumlah_dusun,
                $jumlah_rumah_tangga,
                $sejarah_desa,
                $alamat,
                $no_telepon,
                $email,
                $desa_info['id']
            );

            if ($update_stmt->execute()) {
                $success = 'Informasi desa berhasil diperbarui!';

                // Update data yang sedang ditampilkan
                $desa_info['jumlah_penduduk']     = $jumlah_penduduk;
                $desa_info['jumlah_dusun']        = $jumlah_dusun;
                $desa_info['jumlah_rumah_tangga'] = $jumlah_rumah_tangga;
                $desa_info['sejarah_desa']        = $sejarah_desa;
                $desa_info['alamat']              = $alamat;
                $desa_info['no_telepon']          = $no_telepon;
                $desa_info['email']               = $email;
            } else {
                $error = "Gagal memperbarui data: " . $update_stmt->error;
            }

            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Info Desa - Admin Desa Pancasila</title>
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
        <a href="berita.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Berita</a>
        <a href="gallery.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Gallery</a>
        <a href="peta.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola Peta</a>
        <a href="belanja.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Kelola UMKM</a>
        <a href="desa-info.php" class="block px-4 py-3 rounded-lg bg-accent font-semibold">Informasi Desa</a>
        <hr class="border-gray-700 my-4">
        <a href="../../auth/logout.php" class="block px-4 py-3 rounded-lg text-gray-300 hover:bg-gray-800 font-semibold">Logout</a>
    </nav>
</div>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen">
        <header class="bg-white shadow">
            <div class="px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Edit Informasi Desa</h2>
                <a href="index.php" class="text-accent font-bold hover:underline">← Kembali</a>
            </div>
        </header>

        <main class="p-6">
            <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-lg p-8">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-2 border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="bg-green-100 border-2 border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Jumlah Penduduk</label>
                            <input type="number" name="jumlah_penduduk" value="<?php echo htmlspecialchars($desa_info['jumlah_penduduk']); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Jumlah Dusun</label>
                            <input type="number" name="jumlah_dusun" value="<?php echo htmlspecialchars($desa_info['jumlah_dusun']); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Rumah Tangga</label>
                            <input type="number" name="jumlah_rumah_tangga" value="<?php echo htmlspecialchars($desa_info['jumlah_rumah_tangga']); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Sejarah Desa</label>
                        <textarea name="sejarah_desa" required rows="6" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Tulis sejarah desa..."><?php echo htmlspecialchars($desa_info['sejarah_desa']); ?></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Alamat</label>
                        <input type="text" name="alamat" value="<?php echo htmlspecialchars($desa_info['alamat'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Alamat desa">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">No. Telepon</label>
                            <input type="text" name="no_telepon" value="<?php echo htmlspecialchars($desa_info['no_telepon'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Nomor telepon">
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-2">Email</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($desa_info['email'] ?? ''); ?>" class="w-full border-2 border-gray-300 rounded-lg px-4 py-2 focus:border-accent outline-none" placeholder="Email desa">
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-accent text-white px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 transition">
                            Simpan Perubahan
                        </button>
                        <a href="index.php" class="flex-1 bg-gray-300 text-gray-900 px-6 py-3 rounded-lg font-bold hover:bg-opacity-90 text-center">
                            Batal
                        </a>
                    </div>
                </form>
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

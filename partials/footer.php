<?php
// pastikan koneksi tersedia
if (!isset($conn)) {
    require_once __DIR__ . '/../config/config.php';
}

// ambil data desa di sini
$desa_info = getDesaInfo($conn);
?>

<footer class="bg-gray-900 text-white py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
            <div>
                <h3 class="text-xl font-bold mb-4">Desa Pancasila</h3>
                <p class="text-gray-400">
                    Menghadirkan informasi terpercaya dan transparansi untuk kesejahteraan masyarakat desa.
                </p>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">Navigasi</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="index.php" class="hover:text-white transition">Beranda</a></li>
                    <li><a href="berita.php" class="hover:text-white transition">Berita</a></li>
                    <li><a href="galeri.php" class="hover:text-white transition">Galeri</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-lg font-bold mb-4">Kontak</h3>
                <p class="text-gray-400 mb-2">
                    📞 <?= htmlspecialchars($desa_info['no_telepon'] ?? '-') ?>
                </p>
                <p class="text-gray-400">
                    📧 <?= htmlspecialchars($desa_info['email'] ?? '-') ?>
                </p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 text-center text-gray-500">
            <p>&copy; <?= date('Y') ?> Desa Pancasila. All rights reserved.</p>
        </div>
    </div>
</footer>

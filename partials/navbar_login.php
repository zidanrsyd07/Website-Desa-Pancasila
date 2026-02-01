<header class="bg-white shadow-md sticky top-0 z-40">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">

        <!-- LOGO -->
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 flex items-center justify-center">
                <img src="../../assets/logo/logo.png"
                     alt="Logo Desa Pancasila"
                     class="max-w-full max-h-full object-contain">
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Desa Pancasila
                </h1>
                <p class="text-xs text-gray-600">
                    Sistem Informasi Desa
                </p>
            </div>
        </div>

        <!-- MENU -->
        <nav class="hidden md:flex items-center gap-6">
            <a href="../../dashboard/user/index.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Beranda
            </a>

            <a href="../../dashboard/user/berita_user.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Berita
            </a>

            <a href="../../dashboard/user/galeri_user.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Galeri
            </a>

            <a href="../../dashboard/user/lokasi_user.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Lokasi
            </a>

             <a href="belanja_user.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Belanja
            </a>

            <a href="../../dashboard/user/profile_user.php"
               class="text-gray-700 hover:text-[#8B0000] font-semibold transition">
                Profile
            </a>

        <!-- USER INFO -->
        <div class="flex items-center gap-4">
            <span class="hidden md:block font-bold text-gray-700">
                <?= htmlspecialchars($_SESSION['full_name']); ?>
            </span>

            <a href="../../auth/logout.php"
               class="bg-[#8B0000] text-white px-5 py-2 rounded-lg font-semibold
                      hover:bg-[#700000] transition">
                Logout
            </a>
        </div>

    </div>
</header>

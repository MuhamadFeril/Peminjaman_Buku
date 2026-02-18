<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpustakaan Digital</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body { background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); min-height: 100vh; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
    </style>
</head>
<body class="flex items-center justify-center p-6">
    <div class="glass animate__animated animate__fadeIn max-w-4xl w-full rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <div class="md:w-1/2 bg-blue-600 p-12 text-white flex flex-col justify-center items-center text-center">
            <h1 class="text-5xl font-extrabold mb-4 animate__animated animate__fadeInLeft">Perpustakaan Smk 11 Malang</h1>
            <p class="text-blue-100 opacity-80 italic animate__animated animate__fadeInUp animate__delay-1s">"Jendela dunia dalam satu genggaman digital."</p>
        </div>

        <div class="md:w-1/2 p-10 bg-white/50">
            <div class="flex justify-end space-x-4 mb-12">
                <a href="/login" class="text-gray-600 hover:text-blue-600 font-medium transition duration-300">Login</a>
                <a href="/register" class="bg-blue-600 text-white px-6 py-2 rounded-full shadow-lg hover:bg-blue-700 transition transform hover:scale-105">Daftar</a>
            </div>

            <h2 class="text-3xl font-bold text-gray-800 mb-4 animate__animated animate__fadeInRight">Selamat Datang</h2>
            <p class="text-gray-600 leading-relaxed mb-8 animate__animated animate__fadeInUp">
            Kami hadir untuk memudahkan akses ke dunia literasi. Jelajahi koleksi buku kami, pinjam dengan mudah, dan nikmati pengalaman membaca yang menyenangkan. Bergabunglah dengan komunitas pembaca kami dan temukan inspirasi di setiap halaman dan menjamin keamanan datamu!    
        </p>

            <div class="grid grid-cols-2 gap-4 animate__animated animate__fadeInUp animate__delay-1s">
                <a href="/buku" class="group p-4 border-2 border-blue-50 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition duration-300">
                    <span class="block font-bold text-blue-600 group-hover:translate-x-2 transition transform">Mulai →</span>
                    <span class="text-xs text-gray-500">Masuk ke Dashboard</span>
                </a>
                <a href="/buku" class="group p-4 border-2 border-blue-50 rounded-2xl hover:border-blue-500 hover:bg-blue-50 transition duration-300">
                    <span class="block font-bold text-blue-600 group-hover:translate-x-2 transition transform">Lihat Buku →</span>
                    <span class="text-xs text-gray-500">Cari koleksi kami</span>
                </a>
            </div>

            <p class="mt-12 text-xs text-gray-400 font-light italic">© 2026 Perpustakaan Digital System. Proyek Manajemen Buku Professional.</p>
        </div>
    </div>
</body>
</html>
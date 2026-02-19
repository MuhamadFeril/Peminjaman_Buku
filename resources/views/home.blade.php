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
            <div class="w-full d-flex logo-row" style="display:flex;align-items:center;gap:16px;justify-content:center">
                <div class="site-logo" style="width:96px;height:96px;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,0.1);border-radius:18px;box-shadow:0 8px 20px rgba(0,0,0,0.08)">
                    <!-- Simple book + shield SVG logo -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6v12a1 1 0 0 0 1 1h12"/>
                        <path d="M21 6v12a1 1 0 0 1-1 1H9"/>
                        <path d="M7 6V4a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v2"/>
                        <circle cx="12" cy="12" r="1.5" fill="white" stroke="none" />
                    </svg>
                </div>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold mt-4 mb-2 animate__animated animate__fadeInLeft">Perpustakaan SMK 11 Malang</h1>
            <p class="text-blue-100 opacity-80 italic animate__animated animate__fadeInUp animate__delay-1s">"Jendela dunia dalam satu genggaman digital."</p>
        </div>

        <div class="md:w-1/2 p-10 bg-white/50">
            <div class="flex justify-end items-center space-x-4 mb-12">
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 font-medium transition duration-300 px-3 py-2 rounded-md">Login</a>
                    <a href="{{ route('register') }}" class="bg-blue-600 text-white px-5 py-2 rounded-full shadow-lg hover:bg-blue-700 transition transform hover:scale-105">Daftar</a>
                @else
                    <div class="flex items-center space-x-3">
                        <a href="/dashboard" class="text-sm text-gray-700">{{ auth()->user()->name }}</a>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-sm">Logout</button>
                        </form>
                    </div>
                @endguest
            </div>

            <h2 class="text-3xl font-bold text-gray-800 mb-4 animate__animated animate__fadeInRight">Selamat Datang</h2>
            <p class="text-gray-600 leading-relaxed mb-8 animate__animated animate__fadeInUp">
            Kami hadir untuk memudahkan akses ke dunia literasi. Jelajahi koleksi buku kami, pinjam dengan mudah, dan nikmati pengalaman membaca yang menyenangkan. Bergabunglah dengan komunitas pembaca kami dan temukan inspirasi di setiap halaman,kami juga menjamin keamanan datamu!    
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
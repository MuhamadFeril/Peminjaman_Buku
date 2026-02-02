<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <x-slot name="header">
        <div class="flex items-center justify-between font-sans">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight tracking-tight">
                {{ __('Dashboard Peminjaman') }}
            </h2>

            <div class="flex gap-3">
                <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-500 shadow-sm transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Peminjaman
                </a>
                <a href="{{ route('buku.index') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-500 shadow-sm transition-all active:scale-95">
                    Daftar Buku
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 font-sans bg-gray-50/50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                    <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Total Anggota</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $totalAnggota ?? 0 }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                    <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest mb-1">Total Buku</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $totalBuku ?? 0 }}</p>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 transition-all hover:shadow-md">
                    <p class="text-xs font-bold text-purple-500 uppercase tracking-widest mb-1">Total Peminjaman</p>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $totalPeminjaman ?? 0 }}</p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <span class="w-2 h-6 bg-indigo-500 rounded-full mr-3"></span>
                            5 Peminjaman Terakhir
                        </h3>
                        <a href="{{ route('peminjaman.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500 transition">Lihat Semua &rarr;</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-gray-400 text-xs uppercase tracking-wider border-b dark:border-gray-700">
                                    <th class="pb-4 px-4 font-semibold">ID</th>
                                    <th class="pb-4 px-4 font-semibold">Pelanggan</th>
                                    <th class="pb-4 px-4 font-semibold text-right">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($recentPeminjaman as $peminjaman)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all">
                                    <td class="py-4 px-4 text-sm font-medium text-gray-400 group-hover:text-indigo-500 transition">#{{ $peminjaman->id }}</td>
                                    <td class="py-4 px-4 text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $peminjaman->Anggota->nama ?? '-' }}</td>
                                    <td class="py-4 px-4 text-sm text-gray-500 text-right">{{ optional($peminjaman->created_at)->format('d M, Y') ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-gray-300 dark:text-gray-600 text-sm">Belum ada peminjaman terbaru.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Mengaplikasikan font ke seluruh elemen */
        .font-sans { font-family: 'Inter', sans-serif !important; }
        
        @keyframes slideUp {
            from { transform: translateY(10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .container-animate { animation: slideUp 0.4s ease-out forwards; }
    </style>
</x-app-layout>
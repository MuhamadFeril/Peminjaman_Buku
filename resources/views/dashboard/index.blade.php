<x-app-layout>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <x-slot name="header">
        <div class="flex items-center justify-between font-sans">
            <h2 class="font-bold text-2xl text-gray-800 dark:text-gray-200 leading-tight tracking-tight">
                {{ __('Dashboard Peminjaman') }}
            </h2>

            <div class="flex gap-3 flex-wrap items-center">
                <a href="{{ route('peminjaman.index') }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-500 shadow-sm transition-all active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Peminjaman
                </a>
                <a href="{{ route('buku.index') }}" class="inline-flex items-center px-3 py-2 bg-emerald-600 text-white text-sm font-semibold rounded-lg hover:bg-emerald-500 shadow-sm transition-all active:scale-95">
                    Daftar Buku
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline-flex">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-500 shadow-sm transition-all active:scale-95">Logout</button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 animate-item">
                        <div class="card-body">
                            <div class="text-uppercase text-primary small fw-bold">Profil Saya</div>
                            <div class="h5 fw-semibold count" data-target="{{ auth()->user()->name ? 1 : 0 }}">{{ auth()->user()->name ?? '-' }}</div>
                            <div class="text-muted small">{{ auth()->user()->email ?? '-' }}</div>
                            <div class="mt-3">
                                <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-secondary">Lihat Profil</a>
                                <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-primary ms-2">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 animate-item">
                        <div class="card-body">
                            <div class="text-uppercase text-info small fw-bold">Total Anggota</div>
                            <div class="h3 fw-bold count" data-target="{{ $totalAnggota ?? 0 }}">{{ $totalAnggota ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 animate-item">
                        <div class="card-body">
                            <div class="text-uppercase text-success small fw-bold">Total Buku</div>
                            <div class="h3 fw-bold count" data-target="{{ $totalBuku ?? 0 }}">{{ $totalBuku ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card shadow-sm h-100 animate-item">
                        <div class="card-body">
                            <div class="text-uppercase text-warning small fw-bold">Total Peminjaman</div>
                            <div class="h3 fw-bold count" data-target="{{ $totalPeminjaman ?? 0 }}">{{ $totalPeminjaman ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">5 Peminjaman Terakhir</h6>
                            <a href="{{ route('peminjaman.index') }}" class="small">Lihat Semua &rarr;</a>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="text-muted small text-uppercase">
                                        <tr>
                                            <th>ID</th>
                                            <th>Pelanggan</th>
                                            <th class="text-end">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentPeminjaman as $peminjaman)
                                        <tr>
                                            <td class="text-muted">#{{ $peminjaman->id }}</td>
                                            <td class="fw-semibold">{{ $peminjaman->Anggota->nama ?? '-' }}</td>
                                            <td class="text-end text-muted">{{ optional($peminjaman->created_at)->format('d M, Y') ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">Belum ada peminjaman terbaru.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    <style>
        /* Animation for dashboard items */
        .animate-item { opacity: 0; transform: translateY(8px); transition: opacity .45s ease, transform .45s ease; }
        .animate-item.in { opacity: 1; transform: translateY(0); }

        .table-hover tbody tr { opacity: 0; transform: translateY(6px); transition: opacity .35s ease, transform .35s ease; }
        .table-hover tbody tr.in { opacity: 1; transform: translateY(0); }

        /* subtle hover lift */
        .card:hover { transform: translateY(-4px); transition: transform .18s ease; }

        /* counter small style */
        .count { display:inline-block; min-width:48px; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // animate cards with stagger
            const items = document.querySelectorAll('.animate-item');
            items.forEach((el, i) => setTimeout(() => el.classList.add('in'), i * 100));

            // animate table rows
            const rows = document.querySelectorAll('.table-hover tbody tr');
            rows.forEach((r, i) => setTimeout(() => r.classList.add('in'), 300 + i * 80));

            // simple counter animation for numerical elements
            const counters = document.querySelectorAll('.count');
            counters.forEach(c => {
                const target = Number(c.getAttribute('data-target')) || 0;
                if (target <= 0) return;
                let current = 0;
                const step = Math.max(1, Math.floor(target / 30));
                const interval = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        c.textContent = target;
                        clearInterval(interval);
                    } else {
                        c.textContent = current;
                    }
                }, 25);
            });
        });
    </script>
</x-app-layout>
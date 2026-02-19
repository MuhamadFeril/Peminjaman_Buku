@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container">
        <div class="d-flex justify-content-between mb-4 align-items-center">
        <h3 class="mb-0">Daftar Buku</h3>
        <div class="d-flex align-items-center gap-2">
            <form action="{{ route('buku.index') }}" method="get" class="d-flex">
                <input type="search" name="q" value="{{ isset($q) ? $q : request('q') }}" class="form-control form-control-sm me-2" placeholder="Cari judul, penulis, atau tahun" aria-label="Search">
                <button class="btn btn-sm btn-outline-secondary" type="submit">Cari</button>
            </form>
            @if(Route::has('buku.create') && auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Card grid (desktop only) -->
    <div class="d-none d-md-block mt-3">
        <div class="row g-4">
            @foreach($bukus as $index => $buku)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm book-card">
                        <div class="row g-0 h-100">
                            <div class="col-4 p-3 d-flex align-items-center justify-content-center">
                                @if(!empty($buku->cover_buku))
                                    <img src="{{ asset('storage/' . $buku->cover_buku) }}" alt="{{ $buku->judul }}" class="img-fluid rounded" style="max-height:140px; object-fit:cover">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:140px; width:100%">No Image</div>
                                @endif
                            </div>
                            <div class="col-8">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title mb-1">{{ $buku->judul }}</h5>
                                    <p class="mb-1 small text-muted">{{ $buku->penulis ?? '-' }} • {{ $buku->tahun_terbit ?? '-' }}</p>
                                    <p class="mb-2"><span class="badge bg-info">{{ $buku->persediaan }}</span></p>
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <a href="{{ route('buku.edit', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @endif
                                        @if(auth()->check())
                                            @if($buku->persediaan > 0)
                                                <a href="{{ route('peminjaman.create', ['buku' => $buku->uuid ?? $buku->id_buku]) }}" class="btn btn-sm btn-primary ms-auto">Pinjam</a>
                                            @else
                                                <button class="btn btn-sm btn-secondary ms-auto" disabled>Habis</button>
                                            @endif
                                        @else
                                            <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary ms-auto">Daftar</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Mobile card list (consistent with desktop cards) -->
    <div class="d-block d-md-none mt-3">
        <div class="row g-2">
            @foreach($bukus as $buku)
                <div class="col-12">
                    <div class="card shadow-sm h-100 book-card">
                        <div class="row g-0 h-100">
                            <div class="col-4 p-1 d-flex align-items-center justify-content-center">
                                @if(!empty($buku->cover_buku))
                                    <img src="{{ asset('storage/' . $buku->cover_buku) }}" class="img-fluid rounded book-thumb" alt="{{ $buku->judul }}" style="max-height:84px; object-fit:cover">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:84px; width:100%">No Image</div>
                                @endif
                            </div>
                            <div class="col-8">
                                <div class="card-body d-flex flex-column py-1">
                                    <h6 class="card-title mb-1">{{ Str::limit($buku->judul, 60) }}</h6>
                                    <p class="mb-1 small text-muted">{{ $buku->penulis ?? '-' }} • {{ $buku->tahun_terbit ?? '-' }}</p>
                                    <p class="mb-2"><span class="badge bg-info small">{{ $buku->persediaan }}</span></p>
                                    <div class="mt-auto d-flex gap-2">
                                        <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                        @if(auth()->check() && auth()->user()->role === 'admin')
                                            <a href="{{ route('buku.edit', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @endif
                                        @auth
                                            @if($buku->persediaan > 0)
                                                <a href="{{ route('peminjaman.create', ['buku' => $buku->uuid ?? $buku->id_buku]) }}" class="btn btn-sm btn-primary ms-auto">Pinjam</a>
                                            @else
                                                <button class="btn btn-sm btn-secondary ms-auto" disabled>Habis</button>
                                            @endif
                                        @else
                                            <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary ms-auto">Daftar</a>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-4">
        {{ $bukus->links() }}
    </div>
</div>
<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .container {
        animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes slideIn {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .animate-slide-in {
        animation: slideIn 0.5s ease-in-out;
    }   
</style>
<style>
    /* Table thumbnail and row-card effect */
    .book-thumbnail{width:72px;height:72px;object-fit:cover;border-radius:8px;display:block}
    .book-thumbnail.placeholder{background:#f3f4f6;color:#6b7280;font-size:12px}
    .table-responsive.table-card tbody tr{background:#fff;border-radius:10px;box-shadow:0 6px 18px rgba(15,23,42,.04);transition:transform .18s ease,box-shadow .18s ease}
    .table-responsive.table-card tbody tr:hover{transform:translateY(-6px);box-shadow:0 18px 36px rgba(15,23,42,.08)}
    /* Make table rows appear spaced by using border-collapse separate
       and adding margin via box-shadow area (works best with white page bg) */
    .table-responsive.table-card .table{border-collapse:separate;border-spacing:0 12px}
</style>
<style>
    /* Mobile-specific compact styles */
    @media (max-width: 768px) {
        .book-thumb { max-height:56px !important; width:auto; }
        .book-card .card-body { padding-top: .4rem; padding-bottom: .4rem; }
        .book-card .card-title { font-size: 0.95rem; }
        .badge.small { font-size: 0.72rem; padding: .2rem .4rem; }
        .btn-sm { padding: .25rem .5rem; font-size: .78rem; }
    }
</style>
@include('partials.guest_request_modal')
@endsection

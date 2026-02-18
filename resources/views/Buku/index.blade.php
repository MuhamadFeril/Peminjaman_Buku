@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container">
        <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Buku</h3>
        @if(Route::has('buku.create') && auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Card grid (desktop & mobile) -->
    <div class="d-block mt-3">
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

    <!-- Mobile card list -->
    <div class="d-block d-md-none mt-3">
        <div class="row g-2">
            @foreach($bukus as $buku)
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="row g-0 align-items-center">
                            <div class="col-3">
                                @if(!empty($buku->cover_buku))
                                    <img src="{{ asset('storage/' . $buku->cover_buku) }}" class="img-fluid rounded-start" alt="{{ $buku->judul }}">
                                @else
                                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:72px">No Image</div>
                                @endif
                            </div>
                            <div class="col-9">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1">{{ 
                                        Str::limit($buku->judul, 60) }}</h6>
                                    <p class="mb-1 small text-muted">{{ $buku->penulis ?? '-' }} • {{ $buku->tahun_terbit ?? '-' }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div><span class="badge bg-info">{{ $buku->persediaan }}</span></div>
                                        <div>
                                            <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                                            @auth
                                                @if($buku->persediaan > 0)
                                                    <a href="{{ route('peminjaman.create', ['buku' => $buku->uuid ?? $buku->id_buku]) }}" class="btn btn-sm btn-primary ms-1">Pinjam</a>
                                                @else
                                                    <button class="btn btn-sm btn-secondary ms-1" disabled>Habis</button>
                                                @endif
                                            @else
                                                <div class="btn-group">
                                                    <a href="{{ route('register') }}" class="btn btn-sm btn-outline-primary">Daftar</a>
                                                    <button class="btn btn-sm btn-primary ms-1" onclick="openGuestRequest('{{ $buku->uuid ?? $buku->id_buku }}')">Ajukan</button>
                                                </div>
                                            @endauth
                                        </div>
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
@include('partials.guest_request_modal')
@endsection

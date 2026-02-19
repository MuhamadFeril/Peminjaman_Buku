@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            @if(!empty($buku->cover_buku))
                <img src="{{ asset('storage/' . $buku->cover_buku) }}" class="img-fluid rounded shadow" alt="{{ $buku->judul }}">
            @else
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:400px;">
                    <span class="text-muted">No Image</span>
                </div>
            @endif
        </div>
        <div class="col-md-8">
            <h2>{{ $buku->judul }}</h2>
            <hr>
            <p><strong>Penulis:</strong> {{ $buku->penulis ?? '-' }}</p>
            <p><strong>Tahun Terbit:</strong> {{ $buku->tahun_terbit ?? '-' }}</p>
            <p><strong>Persediaan:</strong> <span class="badge bg-info">{{ $buku->persediaan }}</span></p>
            
            <div class="mt-4 action-row d-flex flex-wrap gap-2 align-items-start">
                @if(auth()->check() && auth()->user()->role === 'admin')
                    <a href="{{ route('buku.edit', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-secondary">Edit</a>
                    <form action="{{ route('buku.destroy', $buku->uuid ?? $buku->id_buku) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Hapus buku?')">Hapus</button>
                    </form>
                @endif

                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Kembali</a>

                @if($buku->persediaan > 0)
                    @auth
                        <a href="{{ route('peminjaman.create', ['buku' => $buku->uuid ?? $buku->id_buku]) }}" class="btn btn-primary ms-2 btn-raise pinjam-btn">Pinjam Sekarang</a>
                    @else
                        <div class="btn-group guest-group ms-2" role="group" aria-label="Guest actions">
                            <a href="{{ route('register') }}" class="btn btn-outline-primary">Daftar</a>
                            <button type="button" class="btn btn-primary" onclick="openGuestRequest('{{ $buku->uuid ?? $buku->id_buku }}')">Ajukan sebagai Tamu</button>
                            <button type="button" class="btn btn-outline-secondary" onclick="(function(){ navigator.clipboard && navigator.clipboard.writeText(window.location.href); alert('Link disalin ke clipboard'); })()">Bagikan</button>
                        </div>
                    @endauth
                @else
                    <button class="btn btn-secondary ms-2" disabled>Stok Habis</button>
                @endif
            </div>

            @if(auth()->check() && auth()->user()->role === 'admin')
                <div class="mt-4 card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Cara Meminjam</h5>
                        <ol class="mb-0">
                            <li>Login ke akun Anda (atau daftar jika belum punya)</li>
                            <li>Buka halaman buku yang ingin dipinjam</li>
                            <li>Tekan tombol "Pinjam Sekarang" untuk membuka formulir peminjaman</li>
                            <li>Pilih tanggal pinjam dan tanggal kembali lalu klik "Simpan Peminjaman"</li>
                            <li>Stok akan otomatis dikurangi, dan Anda bisa melihat daftar peminjaman di halaman Peminjaman</li>
                        </ol>
                    </div>
                </div>
            @endif

            {{-- Sinopsis (Displayed to all; admin can create/edit) --}}
            <div class="mt-4">
                <h4>Sinopsis</h4>
                @if($buku->sinopsis && !empty($buku->sinopsis->konten))
                    <div class="card mb-3"><div class="card-body">{!! nl2br(e($buku->sinopsis->konten)) !!}</div></div>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('buku.sinopsis.edit', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-secondary">Edit Sinopsis</a>
                    @endif
                @else
                    <p class="text-muted">Belum ada sinopsis untuk buku ini.</p>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <a href="{{ route('buku.sinopsis.create', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-primary">Buat Sinopsis</a>
                    @endif
                @endif
            </div>
        </div>
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
    /* Book detail mobile tweaks */
    .action-row .btn { margin-bottom: 6px; }
    .pinjam-btn { min-width: 160px; }
    .badge.stock-badge { font-size: 1rem; padding: .5rem .6rem; border-radius: .6rem; }

    @media (max-width: 768px) {
        .col-md-4, .col-md-8 { width:100%; display:block }
        .col-md-4 { margin-bottom:14px }
        .action-row { flex-direction: column; align-items: stretch; }
        .action-row .btn, .guest-group .btn { width:100%; }
        .pinjam-btn { order: 999; margin-top:8px; }
    }
</style>
@include('partials.guest_request_modal')
@endsection

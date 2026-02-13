@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container">
        <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Buku</h3>
        <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive shadow-sm bg-white rounded d-none d-md-block">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:80px">#</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Tahun</th>
                    <th>Persediaan</th>
                    <th style="width:220px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bukus as $index => $buku)
                    <tr>
                        <td>{{ ($bukus->currentPage()-1) * $bukus->perPage() + $index + 1 }}</td>
                        <td>{{ $buku->judul }}</td>
                        <td>{{ $buku->penulis ?? '-' }}</td>
                        <td>{{ $buku->tahun_terbit ?? '-' }}</td>
                        <td>{{ $buku->persediaan }}</td>
                        <td>
                            <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-outline-primary me-1">Lihat</a>
                            <a href="{{ route('buku.edit', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-sm btn-secondary me-1">Edit</a>
                            <form action="{{ route('buku.destroy', $buku->uuid ?? $buku->id_buku) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
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
@include('partials.guest_request_modal')
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Buku</h3>
        <a href="{{ route('buku.create') }}" class="btn btn-primary">Tambah Buku</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
        @foreach($bukus as $buku)
        <div class="col">
            <div class="card h-100 shadow-sm">
                @if(!empty($buku->cover_buku))
                    <img src="{{ asset('storage/' . $buku->cover_buku) }}" class="card-img-top" alt="{{ $buku->judul }}" style="height:260px; object-fit:cover;">
                @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:260px;">
                        <span class="text-muted">No Image</span>
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $buku->judul }}</h5>
                    <p class="card-text mb-1"><strong>Penulis:</strong> {{ $buku->penulis ?? '-' }}</p>
                    <p class="card-text mb-2"><strong>Tahun:</strong> {{ $buku->tahun_terbit ?? '-' }}</p>
                    <p class="card-text mt-auto"><small class="text-muted">Stok: {{ $buku->persediaan }}</small></p>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('buku.show', $buku->id_buku) }}" class="btn btn-sm btn-outline-primary">Lihat</a>
                        <div>
                            <a href="{{ route('buku.edit', $buku->id_buku) }}" class="btn btn-sm btn-secondary">Edit</a>
                            <form action="{{ route('buku.destroy', $buku->id_buku) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus buku?')">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
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
@endsection

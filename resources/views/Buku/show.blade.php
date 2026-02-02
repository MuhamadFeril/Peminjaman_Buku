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
            
            <div class="mt-4">
                @if(auth()->user()->role === 'admin')
                    <a href="{{ route('buku.edit', $buku->id_buku) }}" class="btn btn-secondary">Edit</a>
                    <form action="{{ route('buku.destroy', $buku->id_buku) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger" onclick="return confirm('Hapus buku?')">Hapus</button>
                    </form>
                @endif
                <a href="{{ route('buku.index') }}" class="btn btn-outline-secondary">Kembali</a>
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
</style>
@endsection

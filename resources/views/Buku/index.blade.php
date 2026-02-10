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

    <div class="table-responsive shadow-sm bg-white rounded">
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

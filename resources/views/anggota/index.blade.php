@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Anggota</h3>
        <a href="{{ route('anggota.create') }}" class="btn btn-primary">Tambah Anggota</a>
    </div>

    <div class="table-responsive shadow-sm bg-white rounded">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:80px">#</th>
                    <th>Nama</th>
                    <th>Alamat</th>
                    <th>Telepon</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($anggotas as $i => $anggota)
                    <tr>
                        <td>{{ ($anggotas->currentPage()-1) * $anggotas->perPage() + $i + 1 }}</td>
                        <td>{{ $anggota->nama }}</td>
                        <td>{{ $anggota->alamat }}</td>
                        <td>{{ $anggota->nomor }}</td>
                        <td>
                            <a href="{{ route('anggota.edit', $anggota->uuid ?? $anggota->id_anggota) }}" class="btn btn-sm btn-secondary me-1">Edit</a>
                            <form action="{{ route('anggota.destroy', $anggota->uuid ?? $anggota->id_anggota) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">{{ $anggotas->links() }}</div>
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

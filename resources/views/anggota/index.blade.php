@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Anggota</h3>
        <a href="{{ route('anggota.create') }}" class="btn btn-primary">Tambah Anggota</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Telepon</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($anggotas as $anggota)
            <tr>
                <td>{{ $anggota->id_anggota }}</td>
                <td>{{ $anggota->nama }}</td>
                <td>{{ $anggota->alamat }}</td>
                <td>{{ $anggota->nomor }}</td>
                <td>
                    <a href="{{ route('anggota.edit', $anggota->id_anggota) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('anggota.destroy', $anggota->id_anggota) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus anggota?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $anggotas->links() }}
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

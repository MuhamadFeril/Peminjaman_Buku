@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Peminjaman</h3>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">Tambah Peminjaman</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Anggota</th>
                <th>Cover</th>
                <th>Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjamans as $peminjaman)
            <tr>
                <td>{{ $peminjaman->id_peminjaman }}</td>
                <td>{{ $peminjaman->Anggota->nama ?? '-' }}</td>
                <td>
                    @if(!empty($peminjaman->Buku->cover_buku))
                        <img src="{{ asset('storage/' . $peminjaman->Buku->cover_buku) }}" alt="cover" style="max-width:60px; max-height:80px; object-fit:cover;">
                    @else
                        &mdash;
                    @endif
                </td>
                <td>{{ $peminjaman->Buku->judul ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') }}</td>
                <td>
                    <a href="{{ route('peminjaman.edit', $peminjaman->id_peminjaman) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('peminjaman.destroy', $peminjaman->id_peminjaman) }}" method="POST" style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus peminjaman?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="d-flex justify-content-center">
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection
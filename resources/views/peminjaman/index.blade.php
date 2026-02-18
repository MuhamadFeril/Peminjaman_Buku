@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-4">
        <h3>Daftar Peminjaman</h3>
        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">Tambah Peminjaman</a>
    </div>

    <div class="table-responsive shadow-sm bg-white rounded">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:80px">#</th>
                    <th>Anggota</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th style="width:200px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peminjamans as $i => $peminjaman)
                    <tr>
                        <td data-label="#">{{ ($peminjamans->currentPage()-1) * $peminjamans->perPage() + $i + 1 }}</td>
                        <td data-label="Anggota">{{ $peminjaman->Anggota->nama ?? '-' }}</td>
                        <td data-label="Buku">{{ $peminjaman->Buku->judul ?? '-' }}</td>
                        <td data-label="Tanggal Pinjam">{{ $peminjaman->tanggal_pinjam ? \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') : '-' }}</td>
                        <td data-label="Tanggal Kembali">{{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d M Y') : '-' }}</td>
                        <td>
                            <a href="{{ route('peminjaman.edit', $peminjaman->uuid ?? $peminjaman->id_peminjaman) }}" class="btn btn-sm btn-secondary me-1">Edit</a>
                            <form action="{{ route('peminjaman.destroy', $peminjaman->uuid ?? $peminjaman->id_peminjaman) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus peminjaman?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-3">{{ $peminjamans->links() }}</div>
</div>
@endsection
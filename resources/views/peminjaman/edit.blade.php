@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Peminjaman</h3>

    <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Anggota</label>
            <select name="anggota_id" class="form-control">
                @foreach($anggotas as $anggota)
                    <option value="{{ $anggota->id_anggota }}" @if($peminjaman->anggota_id == $anggota->id_anggota) selected @endif>{{ $anggota->nama }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Buku</label>
            <select name="buku_id" class="form-control">
                @foreach($bukus as $buku)
                    <option value="{{ $buku->id_buku }}" @if($peminjaman->buku_id == $buku->id_buku) selected @endif>{{ $buku->judul }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam', optional($peminjaman->tanggal_pinjam)->format('Y-m-d')) }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" class="form-control" value="{{ old('tanggal_kembali', optional($peminjaman->tanggal_kembali)->format('Y-m-d')) }}">
        </div>

        <button class="btn btn-primary btn-raise">Update</button>
    </form>
</div>
<style>
    .form-control {
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 10px;
    }
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

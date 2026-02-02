@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h3>Tambah Peminjaman</h3>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjaman.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Anggota</label>
            <input type="text" class="form-control" value="{{ auth()->user()->anggota->nama ?? 'Anggota tidak ditemukan' }}" disabled>
            <small class="text-muted">Otomatis dari user yang login</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Buku</label>
            <select name="buku_id" class="form-control @error('buku_id') is-invalid @enderror" required>
                <option value="">-- Pilih Buku --</option>
                @foreach($bukus as $buku)
                    <option value="{{ $buku->id_buku }}" {{ old('buku_id') == $buku->id_buku ? 'selected' : '' }}>{{ $buku->judul }}</option>
                @endforeach
            </select>
            @error('buku_id') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Pinjam</label>
            <input type="date" name="tanggal_pinjam" class="form-control @error('tanggal_pinjam') is-invalid @enderror" value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" required>
            @error('tanggal_pinjam') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Kembali</label>
            <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali') }}" required>
            @error('tanggal_kembali') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="btn btn-primary shadow-sm">Simpan Peminjaman</button>
    </form>
</div>

<style>
    .form-control { background-color: #f8f9fa; border: 1px solid #ced4da; padding: 10px; }
    .container { animation: fadeIn 0.5s ease-in-out; }
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>
@endsection
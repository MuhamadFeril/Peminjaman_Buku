@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Tambah Anggota</h3>

    <form action="{{ route('anggota.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Alamat</label>
            <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
        </div>
        <button class="btn btn-primary">Simpan</button>
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

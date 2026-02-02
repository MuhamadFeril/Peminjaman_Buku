@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Buku</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('buku.update', $buku->id_buku) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $buku->judul) }}">
            @error('judul') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Penulis</label>
            <input type="text" name="penulis" class="form-control @error('penulis') is-invalid @enderror" value="{{ old('penulis', $buku->penulis) }}">
            @error('penulis') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}">
            @error('tahun_terbit') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Persediaan</label>
            <input type="number" name="persediaan" class="form-control @error('persediaan') is-invalid @enderror" value="{{ old('persediaan', $buku->persediaan) }}">
            @error('persediaan') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label">Cover Buku (opsional)</label>
            <input type="file" name="cover_buku" class="form-control @error('cover_buku') is-invalid @enderror">
            @error('cover_buku') <span class="text-danger">{{ $message }}</span> @enderror
            @if(!empty($buku->cover_buku))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $buku->cover_buku) }}" alt="cover" style="max-width:120px">
                </div>
            @endif
        </div>
        <button class="btn btn-primary">Update</button>
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

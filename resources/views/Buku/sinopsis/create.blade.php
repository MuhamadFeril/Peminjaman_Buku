@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Buat Sinopsis untuk: {{ $buku->judul }}</h3>

    <form action="{{ route('buku.sinopsis.store', $buku->uuid ?? $buku->id_buku) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Sinopsis</label>
            <textarea name="konten" class="form-control" rows="8">{{ old('konten') }}</textarea>
            @error('konten')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button class="btn btn-primary">Simpan Sinopsis</button>
        <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection

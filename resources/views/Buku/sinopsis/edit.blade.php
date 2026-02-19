@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Sinopsis untuk: {{ $buku->judul }}</h3>

    <form action="{{ route('buku.sinopsis.update', $buku->uuid ?? $buku->id_buku) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Sinopsis</label>
            <textarea name="konten" class="form-control" rows="8">{{ old('konten', $sinopsis->konten) }}</textarea>
            @error('konten')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button class="btn btn-primary">Perbarui Sinopsis</button>
        <a href="{{ route('buku.show', $buku->uuid ?? $buku->id_buku) }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

@endsection

@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mobile-form">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <h3 class="mb-3">Tambah Anggota</h3>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @php
                    $current = request()->route() ? request()->route()->getName() : null;
                    $isSelf = $current === 'anggota.createSelfForm';
                    $formAction = $isSelf ? route('anggota.storeSelf') : route('anggota.store');
                    $user = auth()->user();
                @endphp

                <form action="{{ $formAction }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama', isset($anggota) ? $anggota->nama : ($isSelf && $user ? $user->name : '')) }}" required>
                        <small class="text-muted">Otomatis dari user yang login (boleh disunting)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat', isset($anggota) ? $anggota->alamat : '') }}" {{ $isSelf ? 'required' : '' }}>
                        @error('alamat') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="nomor" class="form-control" value="{{ old('nomor', isset($anggota) ? $anggota->nomor : '') }}" {{ $isSelf ? 'required' : '' }}>
                        @error('nomor') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Anggota</button>
                </form>

                @if(request()->has('redirect'))
                    <script>
                        (function(){
                            var f = document.querySelector('form');
                            if(f && !f.querySelector('input[name=redirect]')){
                                var i = document.createElement('input'); i.type='hidden'; i.name='redirect'; i.value = {!! json_encode(request('redirect')) !!}; f.appendChild(i);
                            }
                        })();
                    </script>
                @endif

            </div>
        </div>
    </div>
</div>

<style>
    .mobile-form .form-control { background-color: #f8f9fa; border: 1px solid #e6e9ef; padding: 10px; }
    .mobile-form h3 { font-weight: 600; }
</style>
@endsection

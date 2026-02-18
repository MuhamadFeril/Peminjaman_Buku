@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="mobile-form">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6">
                <h3 class="mb-3">Tambah Anggota</h3>

                @php
                    $current = request()->route() ? request()->route()->getName() : null;
                    $formAction = $current === 'anggota.createSelfForm' ? route('anggota.storeSelf') : route('anggota.store');
                @endphp

                <form action="{{ $formAction }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="alamat" class="form-control" value="{{ old('alamat') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}">
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

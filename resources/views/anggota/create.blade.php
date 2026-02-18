@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="border-radius: 24px; overflow: hidden;">
                <div class="card-body p-5">
                    
                    <div class="text-center mb-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-primary text-white rounded-circle shadow-sm mb-3 animate__animated animate__bounceIn animate__delay-1s d-inline-flex">
                            <i class="bi bi-person-plus-fill fs-3"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Tambah Anggota</h4>
                        <p class="text-muted small">Silakan lengkapi data anggota baru</p>
                    </div>

                    @php
                        $current = request()->route() ? request()->route()->getName() : null;
                        $formAction = $current === 'anggota.createSelfForm' ? route('anggota.storeSelf') : route('anggota.store');
                    @endphp

                    <form action="{{ $formAction }}" method="POST" class="needs-validation">
                        @csrf
                        
                        <div class="mb-3 animate__animated animate__fadeInLeft animate__delay-1s">
                            <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control custom-input" 
                                   value="{{ old('nama') }}" placeholder="Masukkan nama" required>
                        </div>

                        <div class="mb-3 animate__animated animate__fadeInLeft animate__delay-2s">
                            <label class="form-label small fw-bold text-secondary">Alamat</label>
                            <input type="text" name="alamat" class="form-control custom-input" 
                                   value="{{ old('alamat') }}" placeholder="Alamat lengkap" required>
                        </div>

                        <div class="mb-4 animate__animated animate__fadeInLeft animate__delay-3s">
                            <label class="form-label small fw-bold text-secondary">No. Telepon</label>
                            <input type="text" name="telepon" class="form-control custom-input" 
                                   value="{{ old('telepon') }}" placeholder="0812xxxx" required>
                        </div>

                        <button class="btn btn-primary w-100 fw-bold py-3 shadow-sm transition-all animate__animated animate__zoomIn animate__delay-4s" 
                                style="border-radius: 12px; background: linear-gradient(135deg, #0d6efd, #0a58ca); border: none;">
                            Simpan Anggota
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4 animate__animated animate__fadeIn">
                <p class="text-muted" style="font-size: 10px; letter-spacing: 2px; font-weight: 700;">
                    © 2026 PERPUSTAKAAN DIGITAL
                </p>
            </div>
        </div>
    </div>
</div>

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

<style>
    body {
        background-color: #f1f5f9;
    }
    .custom-input {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    .custom-input:focus {
        background-color: #fff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        outline: none;
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }
</style>
@endsection
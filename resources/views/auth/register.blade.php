@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 60px; margin-bottom: 60px;">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm" style="border-radius: 24px; padding: 25px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold text-dark">Daftar Akun</h4>
                        <p class="text-muted small">Lengkapi data untuk bergabung dengan Perpustakaan</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 px-3 small border-0" style="border-radius: 12px;">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control form-control-lg fs-6 border-light-subtle" 
                                   style="border-radius: 12px; background-color: #f8fafc;" 
                                   placeholder="Masukkan nama Anda" value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                            <input type="email" name="email" class="form-control form-control-lg fs-6 border-light-subtle" 
                                   style="border-radius: 12px; background-color: #f8fafc;" 
                                   placeholder="nama@email.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Peran Pengguna (Role)</label>
                            <select name="role" class="form-select form-select-lg fs-6 border-light-subtle" 
                                    style="border-radius: 12px; background-color: #f8fafc;">
                                <option value="user" {{ old('role', 'user') == 'user' ? 'selected' : '' }}>User (Peminjam)</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin (Petugas)</option>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Password</label>
                                <input type="password" name="password" class="form-control form-control-lg fs-6 border-light-subtle" 
                                       style="border-radius: 12px; background-color: #f8fafc;" 
                                       placeholder="••••••••" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label small fw-bold text-secondary">Konfirmasi</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg fs-6 border-light-subtle" 
                                       style="border-radius: 12px; background-color: #f8fafc;" 
                                       placeholder="••••••••" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 mt-2 shadow-sm" 
                                style="border-radius: 14px; background: linear-gradient(135deg, #2563eb, #1d4ed8); border: none;">
                            Daftar Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="small text-muted">Sudah punya akun? 
                            <a href="{{ url('login') }}" class="text-primary fw-bold text-decoration-none">Masuk di sini</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="text-muted" style="font-size: 10px; letter-spacing: 2px; font-weight: 700;">
                    © 2026 PERPUSTAKAAN DIGITAL
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    body {
        background-color: #f1f5f9; /* Warna abu-abu sangat muda agar card putih terlihat kontras */
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }
</style>
@endsection
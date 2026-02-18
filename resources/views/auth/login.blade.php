@extends('layouts.app')

@section('content')
<div class="container" style="margin-top: 100px;">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="border-radius: 20px; padding: 20px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="fw-bold">Login Perpustakaan</h4>
                        <p class="text-muted small">Masuk untuk mengelola buku</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ url('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="nama@email.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg fs-6" placeholder="••••••••" required>
                        </div>

                        <div class="d-flex justify-content-between mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label small" for="remember">Ingat saya</label>
                            </div>
                            <a href="{{ url('register') }}" class="small text-decoration-none">Buat akun</a>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="border-radius: 10px;">
                            Masuk Sekarang
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="text-muted" style="font-size: 10px; letter-spacing: 2px;">© 2024 PERPUSTAKAAN DIGITAL</p>
            </div>
        </div>
    </div>
</div>
@endsection
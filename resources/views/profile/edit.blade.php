<?php /* /resources/views/profile/edit.blade.php */ ?>
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Profil</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Foto Profil</label>
            <input type="file" name="profile_photo" class="form-control">
            @if(!empty($user->profile_photo))
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="profile" style="width:100px; height:100px; object-fit:cover; border-radius:8px">
                </div>
            @endif
        </div>
        <button class="btn btn-primary">Simpan</button>
    </form>
</div>

@endsection

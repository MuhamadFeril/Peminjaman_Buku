<?php /* /resources/views/profile/show.blade.php */ ?>
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if(!empty($user->profile_photo))
                        <img src="{{ asset('storage/' . $user->profile_photo) }}" alt="profile" class="rounded-circle mb-3" style="width:140px;height:140px;object-fit:cover">
                    @else
                        <div class="rounded-circle bg-secondary mb-3" style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;color:#fff">{{ strtoupper(substr($user->name,0,1)) }}</div>
                    @endif
                    <h5>{{ $user->name }}</h5>
                    <p class="text-muted">{{ $user->email }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profil</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h4>Detail Profil</h4>
                    <p><strong>Nama:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Role:</strong> {{ $user->role ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

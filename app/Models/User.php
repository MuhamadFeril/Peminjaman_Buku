<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Ganti baris di bawah ini dari Sanctum ke Passport
use Laravel\Passport\HasApiTokens; 

class User extends Authenticatable
{
    // Pastikan HasApiTokens di sini sekarang merujuk ke Passport
    use HasApiTokens, HasFactory, Notifiable;
    // Use default primary key for users table
    protected $primaryKey = 'id';

    // Route Model Binding key (default is 'id')
    public function getRouteKeyName()
    {
        return 'id';
    }
   protected $fillable = [
    'name',
    'email',
    'password',
    'role', // Tambahkan ini agar bisa register sebagai admin via API
    'profile_photo',
    
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}
<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Ganti baris di bawah ini dari Sanctum ke Passport
use Laravel\Passport\HasApiTokens; 


class User extends Authenticatable
{
    // Pastikan HasApiTokens di sini sekarang merujuk ke Passport
    use HasApiTokens, HasFactory, Notifiable;

    // keep default integer primary key `id`
    protected $primaryKey = 'id';

    // Use uuid for route model binding
    public function getRouteKeyName()
    {
        return 'uuid';
    }
   protected $fillable = [
    'name',
    'email',
    'password',
    'role', // Tambahkan ini agar bisa register sebagai admin via API
    'profile_photo',
    'uuid',
    
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Relation: a User may have one Anggota (member card).
     */
    public function anggota()
    {
        return $this->hasOne(Anggota::class, 'user_id', 'id');
    }
}
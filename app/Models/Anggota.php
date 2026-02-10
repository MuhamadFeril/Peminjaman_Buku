<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Anggota extends Model
{
    use HasFactory;
    protected $table = "table_anggota";
    protected $primaryKey = "id_anggota";
    protected $fillable = [
        'nama',
        'alamat',
        'nomor',
        'uuid',
        'user_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }


    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function Peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'anggota_id', 'id_anggota');
    }
    
    public $timestamps = true;
}

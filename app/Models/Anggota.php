<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    use HasFactory;
    protected $table = "table_anggota";
    protected $primaryKey = "id_anggota";
    protected $fillable = [
        'nama',
        'alamat',
        'nomor',
        'user_id',
    ];

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

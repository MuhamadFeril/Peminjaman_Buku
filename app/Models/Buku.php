<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;
    
    protected $table = "table_buku";
    protected $primaryKey = 'id_buku';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        "judul",
        "penulis",
        "tahun_terbit",
        "persediaan",
        "cover_buku",
    ];

    public function Peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id', 'id_buku');
    }

    public $timestamps = true;
}


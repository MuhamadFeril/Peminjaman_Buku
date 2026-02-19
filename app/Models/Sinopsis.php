<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sinopsis extends Model
{
    use HasFactory;

    protected $table = 'buku_sinopsis';

    protected $fillable = [
        'buku_id',
        'konten',
        'created_by',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'id_buku');
    }
}

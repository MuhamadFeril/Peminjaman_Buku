<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;  
use App\Models\Sinopsis;

class Buku extends Model
{
    use SoftDeletes;
    
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
        'uuid',
    ];

    public function Peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'buku_id', 'id_buku');
    }

    public function sinopsis()
    {
        return $this->hasOne(Sinopsis::class, 'buku_id', 'id_buku');
    }

    public $timestamps = true;

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
}


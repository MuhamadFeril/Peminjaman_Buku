<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    use HasFactory;
    protected $table = "table_peminjaman";
    protected $primaryKey = "id_peminjaman";
    protected $fillable = [
        "anggota_id",
        "buku_id",
        "tanggal_pinjam",
        "tanggal_kembali",
        "status",
        'uuid',
    ];
    public function Anggota()
    {
        return $this->belongsTo(Anggota::class, 'anggota_id', 'id_anggota');
    }

    public function Buku()
    {
        return $this->belongsTo(Buku::class, 'buku_id', 'id_buku');
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

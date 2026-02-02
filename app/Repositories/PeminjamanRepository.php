<?php

namespace App\Repositories;

use App\Models\Peminjaman;
use App\Repositories\BaseRepository;

class PeminjamanRepository extends BaseRepository
{
    /**
     * Constructor untuk menyuntikkan Model Peminjaman ke Parent (BaseRepository)
     */
    public function __construct(Peminjaman $model)
    {
        parent::__construct($model);
    }

    /**
     * Contoh Fungsi Khusus (Polymorphism/Extension)
     * Hanya ada di PeminjamanRepository, tidak ada di BaseRepository
     */
    public function findByNama(string $anggota_id)
    {
        return $this->model->where('anggota_id', 'like', "%$anggota_id%")->get();
    }

    /**
     * Implementasi abstrak: membuat record buku baru.
     */


public function simpanBuku(array $data)
{
    // Pastikan memanggil method create() dari BukuRepository
    return $this->create($data);
}
    /**
     * Implementasi abstrak: menemukan record berdasarkan ID.
     */
public function find($id)
{
    // Gunakan where agar lebih pasti mencari ke primary key yang didefinisikan di model
    return $this->model->where($this->model->getKeyName(), $id)->first();
}


    /**
     * Implementasi abstrak: memperbarui record berdasarkan ID.
     */
    public function getallPeminjaman()
    {
        return $this->model->all();
    }

    public function getPeminjamanByAnggotaId($anggotaId)
    {
        return $this->model->where('anggota_id', $anggotaId)->get();
    }
    
}
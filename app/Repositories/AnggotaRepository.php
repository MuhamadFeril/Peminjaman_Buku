<?php

namespace App\Repositories;

use App\Models\Anggota;
use App\Repositories\BaseRepository;

class AnggotaRepository extends BaseRepository
{
    /**
     * Constructor untuk menyuntikkan Model Anggota ke Parent (BaseRepository)
     */
    public function __construct(Anggota $model)
    {
        parent::__construct($model);
    }

    /**
     * Contoh Fungsi Khusus (Polymorphism/Extension)
     * Hanya ada di AnggotaRepository, tidak ada di BaseRepository
     */
    public function findByNama(string $nama)
    {
        return $this->model->where('nama', 'like', "%$nama%")->get();
    }

    /**
     * Implementasi abstrak: membuat record anggota baru.
     */
   
    public function simpanAnggota(array $data)
    {
        // Pastikan memanggil method create() dari AnggotaRepository
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
 public function getallAnggota()
    {
        return $this->model->all();
    }
    public function getAnggotaByName($name)
    {
        return $this->model->where('nama', 'like', "%$name%")->get();
    }

    /**
     * Implementasi abstrak: memperbarui record berdasarkan ID.
     */
   
}

<?php

namespace App\Repositories;

use App\Models\Buku;
use App\Repositories\BaseRepository;

class BukuRepository extends BaseRepository
{
    /**
     * Constructor untuk menyuntikkan Model Buku ke Parent (BaseRepository)
     */
    public function __construct(Buku $model)
    {
        parent::__construct($model);
    }

    /**
     * Contoh Fungsi Khusus (Polymorphism/Extension)
     * Hanya ada di BukuRepository, tidak ada di BaseRepository
     */
    public function findByJudul(string $judul)
    {
        return $this->model->where('judul', 'like', "%$judul%")->get();
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
    public function getallBuku()
    {
        return $this->model->all();
    }
}
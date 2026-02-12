<?php

namespace App\Repositories\interfaces;

/**
 * Interface BukuRepositoryInterface
 *
 * Provides Buku-specific data access operations in addition to the
 * generic CRUD methods defined in `BaseRepositoryInterface`.
 */
interface BukuRepositoryInterface extends BaseRepositoryInterface
{
   public function ambilSemua(); // Menampilkan semua koleksi buku
    public function cariPerId($id); // Mencari satu buku spesifik
    public function updateStok($id, $jumlah); // Mengurangi atau menambah stok
}

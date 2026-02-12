<?php

namespace App\Repositories\interfaces;

/**
 * Interface AnggotaRepositoryInterface
 *
 * Anggota-specific data access operations.
 */
interface AnggotaRepositoryInterface extends BaseRepositoryInterface
{
   public function daftarAnggota(); // List seluruh anggota
    public function detailAnggota($id); // Cek profil anggota
    public function cekStatusAktif($id); // Memastikan anggota tidak diblokir
}

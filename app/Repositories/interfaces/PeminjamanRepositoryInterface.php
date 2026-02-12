<?php

namespace App\Repositories\interfaces;

/**
 * Interface PeminjamanRepositoryInterface
 *
 * Peminjaman-specific data access operations (loans).
 */
interface PeminjamanRepositoryInterface extends BaseRepositoryInterface
{
   public function simpanPinjaman(array $data); // Mencatat transaksi baru
    public function kembalikanBuku($id); // Update status saat buku balik
    public function riwayatPeminjaman($anggotaId); // Lihat daftar pinjaman per orang
}

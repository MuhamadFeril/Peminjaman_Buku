<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class PeminjamanResource extends JsonResource
{
  public function toArray($request): array
    {
        return [
            'id_peminjaman' => $this->id_peminjaman,
            'status'        => $this->status,

            // Menampilkan data Anggota (Relasi)
            'peminjam' => [
                'id_anggota' => $this->anggota->id_anggota ?? null,
                'nama'       => $this->anggota->nama ?? 'Tidak Diketahui',
                'nomor'      => $this->anggota->nomor ?? '-',
            ],

            // Menampilkan data Buku (Relasi)
            'buku' => [
                'id_buku' => $this->buku->id_buku ?? null,
                'judul'   => $this->buku->judul ?? 'Buku Dihapus',
                'penulis' => $this->buku->penulis ?? $this->buku->pengarang ?? '-',
            ],

            // FORMAT WAKTU INDONESIA (WIB) - gunakan field tanggal_pinjam jika ada
            'tanggal_pinjam' => $this->tanggal_pinjam
                ? Carbon::parse($this->tanggal_pinjam)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB'
                : '-',

            'tanggal_kembali_seharusnya' => $this->tanggal_kembali
                ? Carbon::parse($this->tanggal_kembali)->timezone('Asia/Jakarta')->format('d-m-Y')
                : '-',

            'diperbarui_pada' => $this->updated_at
                ? Carbon::parse($this->updated_at)->timezone('Asia/Jakarta')->format('d-m-Y H:i:s') . ' WIB'
                : '-',
        ];
    }
}
<?php
namespace App\Handler;

use App\Repositories\PeminjamanRepository;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendNotificationJob;
use Illuminate\Support\Facades\DB;
use App\Models\Buku;

class PeminjamanHandler
{
    protected $repo;

    public function __construct(PeminjamanRepository $repo)
    {
        $this->repo = $repo;
    }

    public function create($data)
    {
        $result = DB::transaction(function () use ($data) {
            // lock the book row for update to avoid race conditions
            $buku = Buku::lockForUpdate()->find($data['buku_id']);
            if (! $buku) {
                throw new \Exception("Buku tidak ditemukan");
            }

            if ($buku->persediaan <= 0) {
                throw new \Exception("Buku tidak tersedia");
            }

            $created = $this->repo->create($data);
            if (empty($created)) {
                throw new \Exception("Data peminjaman kosong");
            }

            // decrement stock
            $buku->decrement('persediaan');

            return $created;
        });

        // Clear cache after creating new loan
        Cache::forget('list_peminjaman');

        // Send notification
        dispatch(new SendNotificationJob("Peminjaman baru dibuat: ID " . $result->id));

        return $result;
    }

    public function getPeminjamanById($id)
    {
        $data = $this->repo->find($id);

        if (!$data) {
            throw new \Exception("Peminjaman tidak ditemukan");
        }

        return $data;
    }

    public function getAllPeminjaman()
    {
        // Mengambil data dari Cache selama 60 menit
        return Cache::remember('list_peminjaman', 3600, function () {
            return $this->repo->getAllPeminjaman();
        });
    }

    public function getPeminjamanByAnggotaId($anggotaId)
    {
        return $this->repo->getPeminjamanByAnggotaId($anggotaId);
    }

    public function updatePeminjaman($id, $data)
    {
        $existing = $this->repo->find($id);

        if (!$existing) {
            throw new \Exception("Peminjaman tidak ditemukan");
        }

        $updated = $this->repo->update($id, $data);

        // 2. HAPUS CACHE (Agar saat GET data terbaru yang muncul)
        Cache::forget('list_peminjaman');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Peminjaman telah diperbarui: ID " . $id));

        return $updated;
    }

    public function deletePeminjaman($id)
    {
        $existing = $this->repo->find($id);

        if (!$existing) {
            throw new \Exception("Peminjaman tidak ditemukan");
        }

        $result = $this->repo->delete($id);

        // 2. HAPUS CACHE
        Cache::forget('list_peminjaman');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Peminjaman telah dihapus: ID " . $id));

        return $result;
    }

}
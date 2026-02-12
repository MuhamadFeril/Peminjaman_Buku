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

        $result = DB::transaction(function () use ($id, $data, $existing) {
            // if buku_id changed, restore stock to old book and decrement stock on new book
            if (isset($data['buku_id']) && $data['buku_id'] != $existing->buku_id) {
                // increment old book stock
                $oldBuku = Buku::lockForUpdate()->find($existing->buku_id);
                if ($oldBuku) {
                    $oldBuku->increment('persediaan');
                }

                // decrement new book stock
                $newBuku = Buku::lockForUpdate()->find($data['buku_id']);
                if (! $newBuku) {
                    throw new \Exception("Buku tujuan tidak ditemukan");
                }
                if ($newBuku->persediaan <= 0) {
                    throw new \Exception("Buku tujuan tidak tersedia");
                }
                $newBuku->decrement('persediaan');
            }

            return $this->repo->update($id, $data);
        });

        // clear cache and notify
        Cache::forget('list_peminjaman');
        dispatch(new SendNotificationJob("Peminjaman telah diperbarui: ID " . $id));

        return $result;
    }

    public function deletePeminjaman($id)
    {
        $existing = $this->repo->find($id);

        if (!$existing) {
            throw new \Exception("Peminjaman tidak ditemukan");
        }

        $result = DB::transaction(function () use ($id, $existing) {
            // increment stock back to the book
            $buku = Buku::lockForUpdate()->find($existing->buku_id);
            if ($buku) {
                $buku->increment('persediaan');
            }

            return $this->repo->delete($id);
        });

        // clear cache and notify
        Cache::forget('list_peminjaman');
        dispatch(new SendNotificationJob("Peminjaman telah dihapus: ID " . $id));

        return $result;
    }

}
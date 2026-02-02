<?php
namespace App\Handler;

use App\Repositories\AnggotaRepository;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendNotificationJob;

class AnggotaHandler
{
    protected $repo;

    public function __construct(AnggotaRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAllAnggota()
    {
        // Mengambil data dari Cache selama 60 menit
        return Cache::remember('list_anggota', 3600, function () {
            $data = $this->repo->getAllAnggota();

            if ($data->isEmpty()) {
                throw new \Exception("Data anggota kosong");
            }

            return $data;
        });
    }
    public function getAnggotaByName($name)
    {
        $data = $this->repo->getAnggotaByName($name);

        if ($data->isEmpty()) {
            throw new \Exception("Anggota dengan nama '$name' tidak ditemukan");
        }

        return $data;
    }

    public function getAnggotaById($id)
    {
        $data = $this->repo->find($id);

        // if (!$data) {
        //     throw new \Exception("Anggota tidak ditem");
        // }

        return $data;
    }

    public function StoreAnggota($data)
    {
        $anggota = $this->repo->create($data);

        // Clear cache after creating new member
        Cache::forget('list_anggota');

        // Send notification
        dispatch(new SendNotificationJob("Anggota baru ditambahkan: " . $anggota->nama));

        return $anggota;
    }   

    public function UpdateAnggota ($id, $data)
    {
        $existing = $this->repo->find($id);

        if (!$existing) {
            throw new \Exception("Anggota tidak ditemukan");
        }

        $updated = $this->repo->update($id, $data);

        // 2. HAPUS CACHE (Agar saat GET data terbaru yang muncul)
        Cache::forget('list_anggota');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Anggota telah diperbarui: " . $updated->nama));

        return $updated;
    }

    public function DeleteAnggota($id)
    {
        $existing = $this->repo->find($id);

        if (!$existing) {
            throw new \Exception("Anggota tidak ditemukan");
        }

        $nama = $existing->nama;

        $result = $this->repo->delete($id);

        // 2. HAPUS CACHE
        Cache::forget('list_anggota');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Anggota telah dihapus: " . $nama));

        return $result;
    }

}
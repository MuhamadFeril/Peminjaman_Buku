<?php
namespace App\Handler;

use App\Models\Buku;
use Illuminate\Database\Eloquent\Collection;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SendNotificationJob;

class BukuHandler
{
    protected Buku $model;

    public function __construct(Buku $model)
    {
        $this->model = $model;
    }

    // Repository-like methods for controller compatibility
    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Buku
    {
        return $this->model->find($id);
    }

    public function findByJudul(string $judul): Collection
    {
        return $this->model->where('judul', 'like', "%{$judul}%")->get();
    }

    public function create(array $data): Buku
    {
        $buku = $this->model->create($data);

        // Clear cache after creating new book
        Cache::forget('list_buku');

        // Send notification
        dispatch(new SendNotificationJob("Buku baru ditambahkan: " . $buku->judul));

        return $buku;
    }

    public function update($id, array $data): ?Buku
    {
        $existing = $this->find($id);
        if (! $existing) {
            return null;
        }

        $existing->update($data);

        // 2. HAPUS CACHE (Agar saat GET data terbaru yang muncul)
        Cache::forget('list_buku');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Buku telah diperbarui: " . $existing->judul));

        return $existing->fresh();
    }

    public function delete($id): bool
    {
        $existing = $this->find($id);
        if (! $existing) {
            return false;
        }

        $judul = $existing->judul;

        $result = (bool) $existing->delete();

        // 2. HAPUS CACHE
        Cache::forget('list_buku');

        // 3. KIRIM NOTIFIKASI KE ANTREAN (Queue)
        dispatch(new SendNotificationJob("Buku telah dihapus: " . $judul));

        return $result;
    }

    // Backwards-compatible method names (optional)
    public function getAllBuku(): Collection { 
        // return $this->all();
        // Mengambil data dari Redis selama 60 menit
    return Cache::remember('list_buku', now()->addRealDay, function () {
        return Buku::all(); 
    });
     }
    public function getBukuById($id): ?Buku { return $this->find($id); }
   
    public function storeBuku(array $data, $file)
    {
        return $this->create($data);
        // 1. Proses Upload Gambar
        if ($file) {
            // Simpan ke storage/app/public/buku
            $path = $file->store('buku', 'public'); 
            $data['cover_buku'] = $path;
            
        }


    // 2. Simpan ke Database
    return \App\Models\Buku::create($data);
}
    public function UpdateBuku($id, array $data): ?Buku { return $this->update($id, $data); }
    public function DeleteBuku($id): bool { return $this->delete($id); }
    public function uploadImage($file)
    {
        // 1. Validasi manual (opsional, selain di Request)
        if (!$file->isValid()) {
            throw new Exception('File gambar rusak atau tidak valid.');
        }

        // 2. Simpan file ke folder 'public/buku'
        // store() akan otomatis membuat nama file unik (random string)
        $path = $file->store('buku', 'public');

        if (!$path) {
            throw new Exception('Gagal menyimpan gambar ke server.');
        }

        // 3. Kembalikan nama path untuk disimpan ke Database
        return $path;
    }
    public function deleteImage($path)
{
    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
    }
}


}
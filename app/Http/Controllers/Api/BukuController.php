<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use Illuminate\Http\Request;
use App\Handler\BukuHandler;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BukuResource;
use App\Helpers\SearchHelper;
use App\Helpers\ResponseHelper;
use App\Models\Buku;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SendNotificationJob;
use Exception;

class BukuController extends Controller
{
    protected BukuHandler $bukuhandler;

    public function __construct(BukuHandler $bukuhandler)
    {
        $this->bukuhandler = $bukuhandler;
    }
   public function index(Request $request): JsonResponse
{
        try {
            $keyword = $request->query('search');
            $perPage = $request->query('per_page', null);

            $buku = SearchHelper::searchBuku($keyword, $perPage ? (int) $perPage : null);

            if (is_array($buku) && array_key_exists('data', $buku)) {
                return ResponseHelper::success($buku);
            }

            return ResponseHelper::success(is_object($buku) ? $buku->toArray($request) : (array) $buku);

        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal mengambil data buku: ' . $e->getMessage(), 500);
        }
}
   public function indexpaginate(Request $request): JsonResponse
{
    try {
        // Ambil parameter dari URL
        $keyword = $request->query('search'); // Opsional
        $perPage = $request->query('per_page', default: 10); // Default 10 data

        // Memanggil SearchHelper (mendukung pagination jika per_page diberikan)
        $buku = SearchHelper::searchBuku($keyword, (int) $perPage);

            // Jika helper mengembalikan paginator array (untuk paginated responses), gabungkan langsung
            if (is_array($buku) && array_key_exists('data', $buku)) {
                return ResponseHelper::success($buku);
            }

            // Jika helper mengembalikan Resource collection/object, gunakan toArray
            return ResponseHelper::success(is_object($buku) ? $buku->toArray($request) : (array) $buku);

        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal mengambil data buku: ' . $e->getMessage(), 500);
        }
}

    public function show($id): JsonResponse
    {
        try {
            $buku = Cache::remember("buku_show_{$id}", 3600, function () use ($id) {
                return $this->bukuhandler->find($id);
            });

            if (! $buku) {
                return ResponseHelper::error(null, 'Buku tidak ditemukan', 404);
            }

            return ResponseHelper::success($buku);
        } catch (Exception $e) {
           return ResponseHelper::error(null, 'Gagal melihat buku', 401);
        }
    
    }

    public function store(StoreBukuRequest $request): JsonResponse
{
    // 1. MONITORING & SECURITY: Cek Admin terlebih dahulu
    if (!auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
        Log::info('Buku::store - unauthorized attempt', [
            'user_id' => optional(auth()->user())->id, 
            'role' => optional(auth()->user())->role
        ]);
        return ResponseHelper::error(null, 'Unauthorized. Hanya admin yang diperbolehkan.', 403);
    }

    try {
        $data = $request->validated();

        // 2. BATCH PROCESSING LOGIC: Handle Upload Gambar
        if ($request->hasFile('cover_buku')) {
            $data['cover_buku'] = $this->bukuhandler->uploadImage($request->file('cover_buku'));
        }

        // Simpan Data ke Database
        $buku = $this->bukuhandler->create($data);

        // 3. MESSAGE QUEUE
       SendNotificationJob::dispatch("Buku baru ditambahkan: " . $buku->judul);

        // 4. CACHING
        Cache::forget('list_buku');

        // PERBAIKAN: Gunakan 'new BukuResource($buku)' agar waktu diubah ke WIB
        return ResponseHelper::success(new BukuResource($buku), null, 201);

    } catch (Exception $e) {
       
        return ResponseHelper::error(null, 'Gagal menambahkan buku: ' . $e->getMessage(), 400);
    }
}
    public function update(Request $request, $id): JsonResponse
{
    // Cek Admin
    if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
        Log::info('Buku::update - unauthorized attempt', ['user_id' => optional(auth()->user())->id, 'role' => optional(auth()->user())->role]);
        return ResponseHelper::error(null, 'Unauthorized. Hanya admin yang diperbolehkan.', 403);
    }

    try {
        $data = $request->all();
        $buku = $this->bukuhandler->update($id, $data);

        if (! $buku) {
            return ResponseHelper::error(null, 'Buku tidak ditemukan', 404);
        }

        $this->clearBukuCache($id);

        // PERBAIKAN: Gunakan 'new BukuResource($buku)' agar waktu diubah ke WIB
        return ResponseHelper::success(new BukuResource($buku));

    } catch (Exception $e) {
      
        return ResponseHelper::error(null, 'Gagal memperbarui data buku.', 500);
    }
}
    public function destroy($id): JsonResponse
    {
        if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
            Log::info('Buku::destroy - unauthorized attempt', ['user_id' => optional(auth()->user())->id, 'role' => optional(auth()->user())->role]);
            return ResponseHelper::error(null, 'Unauthorized. Hanya admin yang diperbolehkan.', 403);
        }

        try {
            $deleted = $this->bukuhandler->delete($id);

            if (!$deleted) {
                return ResponseHelper::error(null, 'Buku tidak ditemukan.', 404);
            }

            $this->clearBukuCache($id);

            return ResponseHelper::success(null);
        } catch (Exception $e) {
          
            return ResponseHelper::error(null, 'Terjadi kesalahan sistem saat menghapus data.', 500);
        }
    }

    private function clearBukuCache($id = null)
    {
        Cache::forget('list_buku');
        if ($id) {
            Cache::forget("buku_show_{$id}");
        }
    }
    public function search(Request $request): JsonResponse
{
    $keyword = $request->query('search');

        try {
            // Memanggil fungsi tanpa paginate
            $results = SearchHelper::searchBukuTanpaPaginate($keyword);

            return ResponseHelper::success($results);

        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal melakukan pencarian: ' . $e->getMessage(), 500);
        }
}
    public function searchpaginate(Request $request): JsonResponse
{
    $keyword = $request->query('search'); 
    // Pastikan mengambil input per_page, jika tidak ada baru gunakan default 2
    $perPage = $request->query('per_page', 10); 

    try {
        // Kirimkan variabel $perPage ke helper
        $results = SearchHelper::searchBuku($keyword, (int) $perPage);

            if (is_array($results) && array_key_exists('data', $results)) {
                return ResponseHelper::success($results);
            }

            return ResponseHelper::success(is_object($results) ? $results->toArray($request) : (array) $results);

        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal: ' . $e->getMessage(), 500);
        }
}
// 1. LIHAT ISI TEMPAT SAMPAH
public function trash(): JsonResponse
{
    try {
        $bukuTerhapus = Buku::onlyTrashed()->get();
        return ResponseHelper::success(BukuResource::collection($bukuTerhapus));
    } catch (Exception $e) {
        Log::error('Buku::trash - gagal mengambil sampah', ['error' => $e->getMessage()]);
        return ResponseHelper::error(null, 'Gagal mengambil data sampah: ' . $e->getMessage(), 500);
    }
}

// 2. KEMBALIKAN DATA (RESTORE)
public function restore($id): JsonResponse
{
    // Hanya admin yang boleh merestore
    if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
        Log::info('Buku::restore - unauthorized attempt', ['user_id' => optional(auth()->user())->id]);
        return ResponseHelper::error(null, 'Unauthorized. Hanya admin yang diperbolehkan.', 403);
    }

    try {
        $buku = Buku::onlyTrashed()->find($id);
        if (! $buku) {
            return ResponseHelper::error(null, 'Data tidak ada di sampah', 404);
        }

        $buku->restore();
        $this->clearBukuCache($id);

        return ResponseHelper::success(new BukuResource($buku), 'Buku berhasil dikembalikan!');
    } catch (Exception $e) {
        Log::error('Buku::restore - gagal merestore', ['error' => $e->getMessage(), 'id' => $id]);
        return ResponseHelper::error(null, 'Gagal mengembalikan data: ' . $e->getMessage(), 500);
    }
}

// 3. HAPUS SELAMANYA (FORCE DELETE)
public function forceDelete($id): JsonResponse
{
    // Hanya admin yang boleh melakukan force delete
    if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
        Log::info('Buku::forceDelete - unauthorized attempt', ['user_id' => optional(auth()->user())->id]);
        return ResponseHelper::error(null, 'Unauthorized. Hanya admin yang diperbolehkan.', 403);
    }

    try {
        $buku = Buku::onlyTrashed()->find($id);
        if (! $buku) {
            return ResponseHelper::error(null, 'Data tidak ditemukan', 404);
        }

        // Hapus file cover jika ada
        if (! empty($buku->cover) && Storage::disk('public')->exists($buku->cover)) {
            try { Storage::disk('public')->delete($buku->cover); } catch (Exception $ex) { Log::warning('Gagal menghapus cover file', ['file' => $buku->cover, 'error' => $ex->getMessage()]); }
        }

        $buku->forceDelete();
        $this->clearBukuCache($id);

        return ResponseHelper::success(null, 'Buku berhasil dihapus permanen');
    } catch (Exception $e) {
        Log::error('Buku::forceDelete - gagal memusnahkan', ['error' => $e->getMessage(), 'id' => $id]);
        return ResponseHelper::error(null, 'Gagal menghapus permanen: ' . $e->getMessage(), 500);
    }
}
}
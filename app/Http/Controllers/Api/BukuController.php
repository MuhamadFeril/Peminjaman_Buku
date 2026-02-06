<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use Illuminate\Http\Request;
use App\Handler\BukuHandler;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\BukuResource;
use App\Helpers\SearchHelper;
use App\Models\Buku;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
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
        // Menggunakan method get() untuk mengambil seluruh data tanpa pagination
        $data = \App\Models\Buku::orderBy('id_buku', 'desc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Menampilkan semua data tanpa pagination',
            'data' => BukuResource::collection($data) // Gunakan Resource agar waktu terformat
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal mengambil data: ' . $e->getMessage()
        ], 500);
    }
}
   public function indexpaginate(Request $request): JsonResponse
{
    try {
        // Ambil parameter dari URL
        $keyword = $request->query('search'); // Opsional
        $perPage = $request->query('per_page', 10); // Default 10 data

        // Memanggil SearchHelper (mendukung pagination jika per_page diberikan)
        $buku = SearchHelper::searchBuku($keyword, (int) $perPage);

        // Jika helper mengembalikan paginator array (untuk paginated responses), gabungkan langsung
        if (is_array($buku) && array_key_exists('data', $buku)) {
            return response()->json(array_merge(['status' => 'success'], $buku), 200);
        }

        // Jika helper mengembalikan Resource collection/object, gunakan toArray
        return response()->json(array_merge(
            ['status' => 'success'],
            is_object($buku) ? $buku->toArray($request) : (array) $buku
        ), 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal mengambil data buku: ' . $e->getMessage()
        ], 500);
    }
}

    public function show($id): JsonResponse
    {
        try {
            $buku = Cache::remember("buku_show_{$id}", 3600, function () use ($id) {
                return $this->bukuhandler->find($id);
            });

            if (! $buku) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Buku tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data'   => $buku
            ], 200);
        } catch (Exception $e) {
            Log::error('Error Show Buku: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data.'
            ], 500);
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
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized. Hanya admin yang diperbolehkan.'
        ], 403);
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
        \App\Jobs\SendNotificationJob::dispatch("Buku baru ditambahkan: " . $buku->judul);

        // 4. CACHING
        Cache::forget('list_buku');

        // PERBAIKAN: Gunakan 'new BukuResource($buku)' agar waktu diubah ke WIB
        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil ditambahkan dan notifikasi diproses di background',
            'data' => new BukuResource($buku), // Ini akan memanggil format timezone di Resource
            'path' => isset($data['cover_buku']) ? asset('storage/' . $data['cover_buku']) : null
        ], 201);

    } catch (Exception $e) {
        Log::error('Error Store Buku: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menambahkan buku: ' . $e->getMessage()
        ], 400);
    }
}
    public function update(Request $request, $id): JsonResponse
{
    // Cek Admin
    if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
        Log::info('Buku::update - unauthorized attempt', ['user_id' => optional(auth()->user())->id, 'role' => optional(auth()->user())->role]);
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized. Hanya admin yang diperbolehkan.'
        ], 403);
    }

    try {
        $data = $request->all();
        $buku = $this->bukuhandler->update($id, $data);

        if (! $buku) {
            return response()->json([
                'status' => 'error',
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        $this->clearBukuCache($id);

        // PERBAIKAN: Gunakan 'new BukuResource($buku)' agar waktu diubah ke WIB
        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil diperbarui',
            'data' => new BukuResource($buku) // Memastikan respon mengikuti format waktu Indonesia
        ], 200);

    } catch (Exception $e) {
        Log::error('Error Update Buku: ' . $e->getMessage());
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal memperbarui data buku.'
        ], 500);
    }
}
    public function destroy($id): JsonResponse
    {
        if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
            Log::info('Buku::destroy - unauthorized attempt', ['user_id' => optional(auth()->user())->id, 'role' => optional(auth()->user())->role]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized. Hanya admin yang diperbolehkan.'
            ], 403);
        }

        try {
            $deleted = $this->bukuhandler->delete($id);

            if (!$deleted) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Buku tidak ditemukan.'
                ], 404);
            }

            $this->clearBukuCache($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Buku berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            Log::error('Error Destroy Buku: ' . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghapus data.'
            ], 500);
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
        $results = \App\Helpers\SearchHelper::searchBukuTanpaPaginate($keyword);

        return response()->json([
            'status' => 'success',
            'message' => 'Hasil pencarian untuk: ' . ($keyword ?? 'Semua Data'),
            'data' => $results // Ini akan langsung berupa array objek
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal melakukan pencarian: ' . $e->getMessage()
        ], 500);
    }
}
    public function searchpaginate(Request $request): JsonResponse
{
    $keyword = $request->query('search'); 
    // Pastikan mengambil input per_page, jika tidak ada baru gunakan default 2
    $perPage = $request->query('per_page', 10); 

    try {
        // Kirimkan variabel $perPage ke helper
        $results = \App\Helpers\SearchHelper::searchBuku($keyword, (int) $perPage);

        if (is_array($results) && array_key_exists('data', $results)) {
            return response()->json(array_merge(['status' => 'success'], $results), 200);
        }

        return response()->json(array_merge(['status' => 'success'], is_object($results) ? $results->toArray($request) : (array) $results), 200);

    } catch (Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal: ' . $e->getMessage()
        ], 500);
    }
}
}
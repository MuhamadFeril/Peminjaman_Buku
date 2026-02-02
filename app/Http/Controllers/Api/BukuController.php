<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBukuRequest;
use Illuminate\Http\Request;
use App\Handler\BukuHandler;
use Illuminate\Http\JsonResponse;
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
            if ($request->filled('judul')) {
                $judul = $request->judul;
                $data = Cache::remember("buku_search_{$judul}", 600, function () use ($judul) {
                    return $this->bukuhandler->findByJudul($judul);
                });
            } else {
                $data = Cache::remember('list_buku', 3600, function () {
                    return $this->bukuhandler->all();
                });
            }

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            Log::error("Error Index Buku: " . $e->getMessage());
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data buku.'
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

        // 3. MESSAGE QUEUE: Kirim ke antrean latar belakang
        // Menjalankan sleep(5) di background tanpa mengganggu user
        \App\Jobs\SendNotificationJob::dispatch("Buku baru ditambahkan: " . $buku->judul);

        // 4. CACHING: Hapus cache lama agar data terbaru muncul di GET
        Cache::forget('list_buku');

        return response()->json([
            'status' => 'success',
            'message' => 'Buku berhasil ditambahkan dan notifikasi diproses di background',
            'data' => $buku,
            'path' => isset($data['cover_buku']) ? asset('storage/' . $data['cover_buku']) : null
        ], 201);

    } catch (Exception $e) {
        // 5. MONITORING: Mencatat error ke log sistem
        Log::error('Error Store Buku: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal menambahkan buku: ' . $e->getMessage()
        ], 400);
    }
}
    public function update(Request $request, $id): JsonResponse
    {
        // Only admin may update books
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

            return response()->json([
                'status' => 'success',
                'message' => 'Buku berhasil diperbarui',
                'data' => $buku
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
}

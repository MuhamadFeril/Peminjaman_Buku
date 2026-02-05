<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnggotaRequest;
use App\Handler\AnggotaHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Helpers\SearchHelper;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AnggotaController extends Controller
{
   protected $handler;

    public function __construct(AnggotaHandler $handler)
    {
        $this->handler = $handler;
    }

   public function index(Request $request): JsonResponse
{
    try {
        // Ambil parameter dari URL
        $keyword = $request->query('search'); // Opsional
        $perPage = $request->query('per_page', 10); // Default 10 data

        // Memanggil SearchHelper (mendukung pagination jika per_page diberikan)
        $anggota = SearchHelper::searchAnggota($keyword, (int) $perPage);

        // Jika SearchHelper mengembalikan array paginator, gabungkan keys ke response
        if (is_array($anggota) && array_key_exists('data', $anggota)) {
            return response()->json(array_merge(['status' => 'success'], $anggota), 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => $anggota
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Gagal mengambil data anggota: ' . $e->getMessage()
        ], 500);
    }
}

    public function store(StoreAnggotaRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['nama', 'alamat', 'nomor']);
            $anggota = $this->handler->StoreAnggota($data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Anggota berhasil ditambahkan',
                'data'    => $anggota
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $anggota = $this->handler->getAnggotaById($id);

            if (! $anggota) {
                throw new Exception('Anggota tidak ditemukan');
            }

            return response()->json([
                'status' => 'success',
                'data'   => $anggota
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
        
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->only(['nama', 'alamat', 'nomor']);
            $anggota = $this->handler->UpdateAnggota($id, $data);
            
            if (! $anggota) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anggota tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Anggota berhasil diperbarui',
                'data' => $anggota
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data anggota.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        // 1. Cek Otorisasi
        if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya admin yang boleh menghapus anggota'
            ], 403);
        }

        try {
            // 2. Coba hapus anggota lewat repository
            $deleted = $this->handler->DeleteAnggota($id);
            
            if (! $deleted) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Anggota tidak ditemukan.'
                ], 404);
            }
            
            return response()->json([
                'status'  => 'success',
                'message' => 'Anggota berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            // Untuk error lainnya (masalah database, dll)
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan sistem saat menghapus data.'
            ], 500);
        }
    }
    public function search(Request $request): JsonResponse
    {
        // Mengambil parameter ?search=... dari URL
        $keyword = $request->query('search');

        if (empty($keyword)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Keyword pencarian tidak boleh kosong.'
            ], 400);
        }  
        $keyword = $request->query('search'); // Mengambil input ?search=...
    $perPage = $request->query('per_page', 2); // Mengambil input ?per_page=...

        try {
            // Memanggil logika pencarian di SearchHelper
            $results = SearchHelper::searchAnggota($keyword);

            return response()->json([
                'status' => 'success',
                'message' => 'Hasil pencarian anggota untuk: ' . $keyword,
                'data' => $results
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal melakukan pencarian: ' . $e->getMessage()
            ], 500);
        }
    }
    }
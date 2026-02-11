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
use App\Helpers\ResponseHelper;

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
        $keyword = $request->query('search');
        $perPage = $request->query('per_page', null);

        $anggota = SearchHelper::searchAnggota($keyword, $perPage ? (int) $perPage : null);

        if (is_array($anggota) && array_key_exists('data', $anggota)) {
            return ResponseHelper::success($anggota);
        }

        return ResponseHelper::success(is_object($anggota) ? $anggota->toArray($request) : (array) $anggota);

    } catch (Exception $e) {
        return ResponseHelper::error(null, 'Gagal mengambil data anggota: ' . $e->getMessage(), 500);
    }
}

    public function store(StoreAnggotaRequest $request): JsonResponse
    {
        try {
            $data = $request->only(['nama', 'alamat', 'nomor']);
            $anggota = $this->handler->StoreAnggota($data);
            return ResponseHelper::success($anggota, 'Anggota berhasil ditambahkan', 201);
        } catch (Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 400);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $anggota = $this->handler->getAnggotaById($id);

            if (! $anggota) {
                throw new Exception('Anggota tidak ditemukan');
            }
            return ResponseHelper::success($anggota);
        } catch (Exception $e) {
            return ResponseHelper::error(null, $e->getMessage(), 404);
        }
        
    }

    public function update(Request $request, $id): JsonResponse
    {
        try {
            $data = $request->only(['nama', 'alamat', 'nomor']);
            $anggota = $this->handler->UpdateAnggota($id, $data);
            
            if (! $anggota) {
                return ResponseHelper::error(null, 'Anggota tidak ditemukan', 404);
            }
            return ResponseHelper::success($anggota, 'Anggota berhasil diperbarui');
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Terjadi kesalahan saat memperbarui data anggota.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        // 1. Cek Otorisasi
        if (! auth()->check() || strtolower(trim(auth()->user()->role ?? '')) !== 'admin') {
            return ResponseHelper::error(null, 'Hanya admin yang boleh menghapus anggota', 403);
        }

        try {
            // 2. Coba hapus anggota lewat repository
            $deleted = $this->handler->DeleteAnggota($id);
            
            if (! $deleted) {
                return ResponseHelper::error(null, 'Anggota tidak ditemukan.', 404);
            }
            return ResponseHelper::success(null, 'Anggota berhasil dihapus');
        } catch (Exception $e) {
            // Untuk error lainnya (masalah database, dll)
            return ResponseHelper::error(null, 'Terjadi kesalahan sistem saat menghapus data.', 500);
        }
    }
    public function search(Request $request): JsonResponse
    {
        // Mengambil parameter ?search=... dari URL
        $keyword = $request->query('search');

        if (empty($keyword)) {
            return ResponseHelper::error(null, 'Keyword pencarian tidak boleh kosong.', 400);
        }

        $perPage = $request->query('per_page', null);

        try {
            $results = SearchHelper::searchAnggota($keyword, $perPage ? (int) $perPage : null);

            if (is_array($results) && array_key_exists('data', $results)) {
                return ResponseHelper::success($results, 'Hasil pencarian anggota untuk: ' . $keyword);
            }

            return ResponseHelper::success(is_object($results) ? $results->toArray($request) : (array) $results, 'Hasil pencarian anggota untuk: ' . $keyword);

        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal melakukan pencarian: ' . $e->getMessage(), 500);
        }
    }
    }
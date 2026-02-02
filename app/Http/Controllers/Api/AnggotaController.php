<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnggotaRequest;
use App\Handler\AnggotaHandler;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
            $data = $request->filled('nama')
                ? $this->handler->getAnggotaByName($request->nama)
                : $this->handler->getAllAnggota();

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data anggota'
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
            return response(ModelNotFoundException::class)->json([
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
   throw new Exception(' anggota tidak ditemukana ');
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
}

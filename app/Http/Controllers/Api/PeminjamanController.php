<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Handler\PeminjamanHandler;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PeminjamanController extends Controller
{
    protected $peminjamanHandler;
    public function __construct(PeminjamanHandler $peminjamanHandler)
    {
        $this->peminjamanHandler = $peminjamanHandler;
    }
    // Controller now uses the `Peminjaman` model directly (no service)

    public function index(Request $request): JsonResponse
    {
        try {
            // Admin can view all or filter by query param
            if (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin') {
                $data = $request->filled('anggota_id')
                    ? $this->peminjamanHandler->getPeminjamanByAnggotaId($request->anggota_id)
                    : $this->peminjamanHandler->getAllPeminjaman();
            } else {
                // Non-admin must provide anggota_id to view their own records
                if (! $request->filled('anggota_id')) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Untuk user umum, sertakan parameter anggota_id untuk melihat riwayat Anda.'
                    ], 403);
                }

                $data = $this->peminjamanHandler->getPeminjamanByAnggotaId($request->anggota_id);
            }

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data peminjaman'
            ], 500);
        }
    }
    public function show($id): JsonResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data'   => $peminjaman
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Peminjaman tidak ditemukan'
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data peminjaman'
            ], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali']);

            // Set default tanggal_pinjam if not provided
            if (empty($data['tanggal_pinjam'])) {
                return response()->json(['status' => 'error', 'message' => 'tanggal_pinjam wajib diisi.'], 400);
            }

            if (empty($data['anggota_id']) || empty($data['buku_id'])) {
                return response()->json(['status' => 'error', 'message' => 'anggota_id dan buku_id wajib diisi.'], 400);
            }

            // Additional simple checks: anggota and buku exist
            if (! \App\Models\Anggota::find($data['anggota_id'])) {
                return response()->json(['status' => 'error', 'message' => 'Anggota tidak ditemukan.'], 404);
            }
            $buku = \App\Models\Buku::find($data['buku_id']);
            if (! $buku) {
                return response()->json(['status' => 'error', 'message' => 'Buku tidak ditemukan.'], 404);
            }
            if ($buku->persediaan <= 0) {
                return response()->json(['status' => 'error', 'message' => 'Buku tidak tersedia.'], 400);
            }

            $peminjaman = $this->peminjamanHandler->create($data);

            // Optionally decrement buku persediaan
            $buku->decrement('persediaan');

            return response()->json([
                'status'  => 'success',
                'message' => 'Peminjaman berhasil dibuat',
                'data'    => $peminjaman
            ], 201);
        } 
        catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Ambil data yang valid
        $data = $request->only(['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali']);

        try {
            $peminjaman = $this->peminjamanHandler->getPeminjamanById($id);
            if (! $peminjaman) {
                return response()->json(['status' => 'error', 'message' => 'Peminjaman tidak ditemukan'], 404);
            }

            // Authorization: admin can update any. Non-admin may update only if they supply matching anggota_id.
            if (! (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')) {
                if (empty($data['anggota_id']) || $data['anggota_id'] != $peminjaman->anggota_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized. Hanya owner atau admin yang bisa mengubah.'], 403);
                }
            }

            $updated = $this->peminjamanHandler->updatePeminjaman($id, $data);
            return response()->json(['status' => 'success', 'message' => 'Peminjaman berhasil diperbarui', 'data' => $updated], 200);
        } catch (Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan saat memperbarui data peminjaman.'
            ], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $peminjaman = $this->peminjamanHandler->getPeminjamanById($id);

            if (!$peminjaman) {
                return response()->json(['status' => 'error', 'message' => 'Peminjaman tidak ditemukan.'], 404);
            }

            // Authorization: admin can delete any; non-admin can delete only if they provide matching anggota_id
            if (! (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')) {
                $anggota_id = request()->get('anggota_id');
                if (empty($anggota_id) || $anggota_id != $peminjaman->anggota_id) {
                    return response()->json(['status' => 'error', 'message' => 'Unauthorized. Hanya owner atau admin yang bisa menghapus.'], 403);
                }
            }

            $this->peminjamanHandler->deletePeminjaman($id);

            return response()->json(['status' => 'success', 'message' => 'Peminjaman berhasil dihapus'], 200);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat menghapus data.'], 500);
        }
    }
}

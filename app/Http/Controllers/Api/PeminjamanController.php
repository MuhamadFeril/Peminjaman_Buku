<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Handler\PeminjamanHandler;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Helpers\SearchHelper;
use App\Http\Resources\PeminjamanResource;
use App\Models\Buku;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\Log;


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
            $perPage = $request->query('per_page', null);

            if ($perPage) {
                $perPage = (int) $perPage;

                if (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin') {
                    $query = $request->filled('anggota_id')
                        ? \App\Models\Peminjaman::where('anggota_id', $request->anggota_id)
                        : \App\Models\Peminjaman::query();
                } else {
                    if (! $request->filled('anggota_id')) {
                        return ResponseHelper::error(null, 'Untuk user umum, sertakan parameter anggota_id untuk melihat riwayat Anda.', 403);
                    }
                    $query = \App\Models\Peminjaman::where('anggota_id', $request->anggota_id);
                }

                $paginator = $query->orderBy('created_at', 'desc')
                                   ->paginate($perPage)
                                   ->appends(['per_page' => $perPage, 'anggota_id' => $request->query('anggota_id')]);

                $transformed = PeminjamanResource::collection($paginator->items())->resolve();
                $paginatorArray = collect($paginator)->all() + ['data' => $transformed];

                if (empty($paginatorArray['total'])) {
                    $paginatorArray['from'] = 0;
                    $paginatorArray['to'] = 0;
                }

                return ResponseHelper::success($paginatorArray);
            }

            // No pagination: existing behavior
            if (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin') {
                $data = $request->filled('anggota_id')
                    ? $this->peminjamanHandler->getPeminjamanByAnggotaId($request->anggota_id)
                    : $this->peminjamanHandler->getAllPeminjaman();
            } else {
                if (! $request->filled('anggota_id')) {
                    return ResponseHelper::error(null, 'Untuk user umum, sertakan parameter anggota_id untuk melihat riwayat Anda.', 403);
                }

                $data = $this->peminjamanHandler->getPeminjamanByAnggotaId($request->anggota_id);
            }

            $payload = is_iterable($data)
                ? PeminjamanResource::collection($data)
                : new PeminjamanResource($data);

            return ResponseHelper::success($payload);
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal mengambil data peminjaman', 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $keyword = $request->query('search');

        if (empty($keyword)) {
            return ResponseHelper::error(null, 'Keyword pencarian tidak boleh kosong.', 400);
        }

        $perPage = $request->query('per_page', null);

        try {
            $results = SearchHelper::searchPeminjaman($keyword, $perPage ? (int) $perPage : null);

            if (is_array($results) && array_key_exists('data', $results)) {
                return ResponseHelper::success($results, 'Hasil pencarian peminjaman untuk: ' . $keyword);
            }

            return ResponseHelper::success(is_object($results) ? $results->toArray($request) : (array) $results, 'Hasil pencarian peminjaman untuk: ' . $keyword);
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal melakukan pencarian: ' . $e->getMessage(), 500);
        }
    }
    
    // history feature removed
    public function show($id): JsonResponse
    {
        try {
            $peminjaman = Peminjaman::findOrFail($id);
            return ResponseHelper::success(new PeminjamanResource($peminjaman));
        } catch (ModelNotFoundException $e) {
            return ResponseHelper::error(null, 'Peminjaman tidak ditemukan', 404);
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Gagal mengambil data peminjaman', 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $data = $request->only(['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali']);

            // Set default tanggal_pinjam if not provided
            if (empty($data['tanggal_pinjam'])) {
                return ResponseHelper::error(null, 'tanggal_pinjam wajib diisi.', 400);
            }

            if (empty($data['anggota_id']) || empty($data['buku_id'])) {
                return ResponseHelper::error(null, 'anggota_id dan buku_id wajib diisi.', 400);
            }

            // Additional simple checks: anggota and buku exist
            if (! \App\Models\Anggota::find($data['anggota_id'])) {
                return ResponseHelper::error(null, 'Anggota tidak ditemukan.', 404);
            }
            $buku = Buku::find($data['buku_id']);
            if (! $buku) {
                return ResponseHelper::error(null, 'Buku tidak ditemukan.', 404);
            }
            if ($buku->persediaan <= 0) {
                return ResponseHelper::error(null, 'Buku tidak tersedia.', 400);
            }


            $peminjaman = $this->peminjamanHandler->create($data);

            return ResponseHelper::success(new PeminjamanResource($peminjaman), 'Peminjaman berhasil dibuat', 201);
        }
        catch (Exception $e) {
            Log::error('Peminjaman::store exception', [
                'exception_class' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $data
            ]);

            return ResponseHelper::error(null, $e->getMessage() ?: 'Terjadi kesalahan saat membuat peminjaman. Periksa log untuk detail.', 400);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        // Ambil data yang valid
        $data = $request->only(['anggota_id', 'buku_id', 'tanggal_pinjam', 'tanggal_kembali']);

        try {
            $peminjaman = $this->peminjamanHandler->getPeminjamanById($id);
            if (! $peminjaman) {
                return ResponseHelper::error(null, 'Peminjaman tidak ditemukan', 404);
            }

            // Authorization: admin can update any. Non-admin may update only if they supply matching anggota_id.
            if (! (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')) {
                if (empty($data['anggota_id']) || $data['anggota_id'] != $peminjaman->anggota_id) {
                    return ResponseHelper::error(null, 'Unauthorized. Hanya owner atau admin yang bisa mengubah.', 403);
                }
            }
            $updated = $this->peminjamanHandler->updatePeminjaman($id, $data);
            return ResponseHelper::success(new PeminjamanResource($updated), 'Peminjaman berhasil diperbarui');
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Terjadi kesalahan saat memperbarui data peminjaman.', 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $peminjaman = $this->peminjamanHandler->getPeminjamanById($id);

            if (!$peminjaman) {
                return ResponseHelper::error(null, 'Peminjaman tidak ditemukan.', 404);
            }

            // Authorization: admin can delete any; non-admin can delete only if they provide matching anggota_id
            if (! (auth()->check() && strtolower(trim(auth()->user()->role ?? '')) === 'admin')) {
                $anggota_id = request()->get('anggota_id');
                if (empty($anggota_id) || $anggota_id != $peminjaman->anggota_id) {
                    return ResponseHelper::error(null, 'Unauthorized. Hanya owner atau admin yang bisa menghapus.', 403);
                }
            }
            $this->peminjamanHandler->deletePeminjaman($id);

            return ResponseHelper::success(null, 'Peminjaman berhasil dihapus');
        } catch (Exception $e) {
            return ResponseHelper::error(null, 'Terjadi kesalahan sistem saat menghapus data.', 500);
        }
    }
    
}

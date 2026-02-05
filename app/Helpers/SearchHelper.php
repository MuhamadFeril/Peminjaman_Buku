<?php

namespace App\Helpers;

use App\Models\Buku;
use App\Models\Anggota;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\BukuResource;
use App\Http\Resources\AnggotaResource;
use App\Http\Resources\PeminjamanResource;

class SearchHelper
{
    public static function searchBukuTanpaPaginate($keyword = null)
{
    $query = \App\Models\Buku::query();

    if ($keyword) {
        $query->where('judul', 'like', "%{$keyword}%")
              ->orWhere('penulis', 'like', "%{$keyword}%");
    }

    // Menggunakan get() untuk mengambil semua hasil pencarian sekaligus
    return $query->orderBy('id_buku', 'desc')->get();
}
    public static function searchBuku($keyword, $perPage = null)
    {
        // If pagination requested, do not use cache and return paginated results
        if (!empty($perPage) && is_int($perPage) && $perPage > 0) {
            $query = Buku::where('judul', 'like', "%{$keyword}%")
                         ->orWhere('penulis', 'like', "%{$keyword}%")
                         ->orWhere('tahun_terbit', 'like', "%{$keyword}%");

            $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(['search' => $keyword, 'per_page' => $perPage]);

            $paginatorArray = $paginator->toArray();
            $transformed = BukuResource::collection($paginator->getCollection())->resolve();
            $paginatorArray['data'] = $transformed;

            if (empty($paginatorArray['total'])) {
                $paginatorArray['from'] = 0;
                $paginatorArray['to'] = 0;
            }

            return $paginatorArray;
        }

        // Cache key berdasarkan keyword agar unik
        $cacheKey = "search_all_" . md5($keyword);

        $results = Cache::remember($cacheKey, 600, function () use ($keyword) {
            return Buku::where('judul', 'like', "%{$keyword}%")
                       ->orWhere('penulis', 'like', "%{$keyword}%") // Cari berdasarkan Penulis
                       ->orWhere('tahun_terbit', 'like', "%{$keyword}%") // Cari berdasarkan Tahun
                       ->get();
        });

        return BukuResource::collection($results);
    }
    public static function searchAnggota($keyword, $perPage = null)
    {
        // If pagination requested, do not use cache and return paginated results
        if (!empty($perPage) && is_int($perPage) && $perPage > 0) {
            $query = Anggota::where('nama', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhere('nomor', 'like', "%{$keyword}%")
                        ->orWhereHas('user', function ($q) use ($keyword) {
                            $q->where('email', 'like', "%{$keyword}%");
                        });

            $paginator = $query->orderBy('created_at', 'desc')->paginate($perPage)->appends(['search' => $keyword, 'per_page' => $perPage]);

            $paginatorArray = $paginator->toArray();
            $transformed = AnggotaResource::collection($paginator->getCollection())->resolve();
            $paginatorArray['data'] = $transformed;

            if (empty($paginatorArray['total'])) {
                $paginatorArray['from'] = 0;
                $paginatorArray['to'] = 0;
            }

            return $paginatorArray;
        }

        // Cache key berdasarkan keyword agar unik
        $cacheKey = "search_anggota_" . md5($keyword);

        $results = Cache::remember($cacheKey, 600, function () use ($keyword) {
            return Anggota::where('nama', 'like', "%{$keyword}%")
                          ->orWhere('alamat', 'like', "%{$keyword}%") // Cari berdasarkan Alamat
                          ->orWhere('nomor', 'like', "%{$keyword}%") // Cari berdasarkan Nomor
                          ->orWhereHas('user', function ($q) use ($keyword) {
                              $q->where('email', 'like', "%{$keyword}%");
                          }) // Cari berdasarkan Email pada relasi User
                          ->get();
        });

        return AnggotaResource::collection($results);
    }   
    public static function searchPeminjaman($keyword)
    {
        // Cache key berdasarkan keyword agar unik
        $cacheKey = "search_peminjaman_" . md5($keyword);

        $results = Cache::remember($cacheKey, 600, function () use ($keyword) {
            return Peminjaman::where('id_peminjaman', 'like', "%{$keyword}%")
                             ->orWhere('status', 'like', "%{$keyword}%") // Cari berdasarkan Status
                             ->orWhereHas('buku', function ($query) use ($keyword) {
                                 $query->where('judul', 'like', "%{$keyword}%");
                             }) // Cari berdasarkan Judul Buku
                             ->orWhereHas('anggota', function ($query) use ($keyword) {
                                 $query->where('nama', 'like', "%{$keyword}%");
                             }) // Cari berdasarkan Nama Anggota
                             ->get();
        });

        return PeminjamanResource::collection($results);
    }
  
}
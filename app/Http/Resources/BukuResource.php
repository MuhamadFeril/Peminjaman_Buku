<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class BukuResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Set locale ke Indonesia agar nama bulan/hari tidak dalam bahasa Inggris
        Carbon::setLocale('id');

        $id = $this->id_buku ?? $this->getKey() ?? 0;

        // Pastikan mengambil kolom 'pengarang' dari DB jika 'penulis' null
        $penulis = $this->penulis ?? $this->pengarang ?? '-';

        // Logika persediaan
        $persediaanVal = $this->persediaan ?? null;
        $available = false;
        if (is_numeric($persediaanVal)) {
            $available = ((int)$persediaanVal) > 0;
        } else {
            $str = strtolower((string) $persediaanVal);
            $available = in_array($str, ['ya', 'yes', 'true']);
        }

        return [
            'id'           => $id,
            'judul'        => $this->judul ?? '-',
            'penulis'      => $penulis,
            'persediaan'   => $available ? 'Ya' : 'Tidak',
            // Format d-m-Y tetap dipertahankan untuk tahun terbit
            'tahun_terbit' => !empty($this->tahun_terbit) ? Carbon::parse($this->tahun_terbit)->format('d-m-Y') : '-',
            
         
           'created_at' => $this->created_at 
    ? \Carbon\Carbon::parse($this->created_at)
        ->timezone('Asia/Jakarta') // Menambah 7 jam secara otomatis ke WIB
        ->format('d-m-Y H:i:s') . ' WIB' 
    : '-',

'updated_at' => $this->updated_at 
    ? \Carbon\Carbon::parse($this->updated_at)
        ->timezone('Asia/Jakarta') // Memastikan waktu update juga WIB
        ->format('d-m-Y H:i:s') . ' WIB' 
    : '-', 
                ];

    }     
    }

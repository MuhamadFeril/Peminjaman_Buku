<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AnggotaResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id_anggota" => $this->id_anggota,
            "nama"       => $this->nama,
            "alamat"     => $this->alamat,
            "nomor"      => $this->nomor,
            "user_id"    => $this->user_id,
            // Format Waktu Indonesia Barat
            "bergabung_pada" => $this->created_at
                ? Carbon::parse($this->created_at)->timezone('Asia/Jakarta')->translatedFormat('d F Y H:i') . ' WIB'
                : '-',
        ];
    }
}
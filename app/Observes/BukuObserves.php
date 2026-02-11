<?php

namespace App\Observer;

use App\Models\Buku;
use illuminate\Support\Facades\Log;

class BukuObserver
{
    public function created(Buku $buku)
    {
        Log::info("Buku created: " . $buku->judul . " (ID: " . $buku->id . ")");
    }
}
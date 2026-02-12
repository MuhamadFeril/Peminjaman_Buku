<?php

namespace App\Observers;

use App\Models\Buku;
use Illuminate\Support\Facades\Log;

class BukuObserver
{
    public function created(Buku $buku)
    {
        Log::info('Buku created: ' . ($buku->judul ?? 'n/a') . ' (ID: ' . ($buku->id ?? 'n/a') . ')');
    }
}

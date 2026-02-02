<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
   public function handle()
{
    // Simulasikan proses berat seperti kirim email
    sleep(5);

    // Handle both Buku object and string message
    if (is_string($this->data)) {
        Log::info("Notifikasi: " . $this->data);
    } elseif (is_object($this->data) && isset($this->data->judul)) {
        Log::info("Notifikasi terkirim untuk buku: " . $this->data->judul);
    } else {
        Log::warning("SendNotificationJob: Invalid data type received");
    }
}
}

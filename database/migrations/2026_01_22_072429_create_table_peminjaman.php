<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('table_peminjaman', function (Blueprint $table) {
            $table->id("id_peminjaman");
            $table->string("anggota_id");
            $table->string("buku_id");
            $table->timestamp("tanggal_pinjam");
 $table->date("tanggal_kembali");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_peminjaman');
    }
};

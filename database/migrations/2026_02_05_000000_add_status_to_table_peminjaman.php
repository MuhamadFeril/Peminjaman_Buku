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
        Schema::table('table_peminjaman', function (Blueprint $table) {
            // status: 'pinjam' | 'kembali' | null
            $table->string('status')->default('pinjam')->after('tanggal_kembali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_peminjaman', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};

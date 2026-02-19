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
        if (!Schema::hasTable('buku_sinopsis')) {
            Schema::create('buku_sinopsis', function (Blueprint $table) {
                $table->id();
                $table->integer('buku_id')->unsigned();
                $table->text('konten');
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();

                $table->foreign('buku_id')->references('id_buku')->on('table_buku')->onDelete('cascade');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buku_sinopsis');
    }
};

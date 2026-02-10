<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['table_anggota', 'table_buku', 'table_peminjaman', 'users'];
        $database = DB::getDatabaseName();
        foreach ($tables as $t) {
            if (Schema::hasColumn($t, 'uuid')) {
                // find indexes on the uuid column
                $indexes = DB::select(
                    'SELECT INDEX_NAME FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$database, $t, 'uuid']
                );

                foreach ($indexes as $idx) {
                    $indexName = $idx->INDEX_NAME ?? $idx->index_name ?? null;
                    if ($indexName) {
                        try {
                            DB::statement("ALTER TABLE `{$t}` DROP INDEX `{$indexName}`");
                        } catch (\Exception $e) {
                            // ignore if drop fails
                        }
                    }
                }

                // finally drop the column
                Schema::table($t, function (Blueprint $table) use ($t) {
                    $table->dropColumn('uuid');
                });
            }
        }
    }

    public function down(): void
    {
        // Re-create nullable uuid columns (no data recovery)
        if (! Schema::hasColumn('table_anggota', 'uuid')) {
            Schema::table('table_anggota', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_anggota');
            });
        }
        if (! Schema::hasColumn('table_buku', 'uuid')) {
            Schema::table('table_buku', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_buku');
            });
        }
        if (! Schema::hasColumn('table_peminjaman', 'uuid')) {
            Schema::table('table_peminjaman', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_peminjaman');
            });
        }
        if (! Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
        }
    }
};

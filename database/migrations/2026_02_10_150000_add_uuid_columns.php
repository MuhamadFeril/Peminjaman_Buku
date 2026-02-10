<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'table_anggota' => 'id_anggota',
            'table_buku' => 'id_buku',
            'table_peminjaman' => 'id_peminjaman',
            'users' => 'id',
        ];

        foreach ($tables as $table => $pk) {
            if (! Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $t) use ($pk) {
                    $t->uuid('uuid')->nullable()->after($pk);
                });
            }

            $rows = DB::table($table)->whereNull('uuid')->orWhere('uuid', '')->get([$pk]);
            foreach ($rows as $r) {
                DB::table($table)
                    ->where($pk, $r->$pk)
                    ->update(['uuid' => (string) Str::uuid()]);
            }

            try {
                DB::statement("ALTER TABLE `{$table}` MODIFY `uuid` CHAR(36) NOT NULL;");
            } catch (\Exception $e) {
                // ignore
            }

            try {
                DB::statement("ALTER TABLE `{$table}` ADD UNIQUE `{$table}_uuid_unique` (`uuid`);");
            } catch (\Exception $e) {
                // ignore if index exists
            }
        }
    }

    public function down(): void
    {
        $tables = ['table_anggota', 'table_buku', 'table_peminjaman', 'users'];
        $database = DB::getDatabaseName();

        foreach ($tables as $t) {
            if (Schema::hasColumn($t, 'uuid')) {
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
                            // ignore
                        }
                    }
                }

                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn('uuid');
                });
            }
        }
    }
};

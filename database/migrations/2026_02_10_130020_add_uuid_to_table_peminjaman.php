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
        if (! Schema::hasColumn('table_peminjaman', 'uuid')) {
            Schema::table('table_peminjaman', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_peminjaman');
            });
        }

        // Populate missing/empty uuids
        $rows = DB::table('table_peminjaman')->whereNull('uuid')->orWhere('uuid', '')->get(['id_peminjaman']);
        foreach ($rows as $r) {
            DB::table('table_peminjaman')
                ->where('id_peminjaman', $r->id_peminjaman)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        // Make column NOT NULL and add unique constraint (best-effort)
        try {
            DB::statement("ALTER TABLE `table_peminjaman` MODIFY `uuid` CHAR(36) NOT NULL;");
            DB::statement("ALTER TABLE `table_peminjaman` ADD UNIQUE `table_peminjaman_uuid_unique` (`uuid`);");
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('table_peminjaman', 'uuid')) {
            Schema::table('table_peminjaman', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

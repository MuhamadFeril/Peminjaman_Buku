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
        if (! Schema::hasColumn('table_buku', 'uuid')) {
            Schema::table('table_buku', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_buku');
            });
        }

        // Populate missing/empty uuids
        $rows = DB::table('table_buku')->whereNull('uuid')->orWhere('uuid', '')->get(['id_buku']);
        foreach ($rows as $r) {
            DB::table('table_buku')
                ->where('id_buku', $r->id_buku)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        // Make column NOT NULL and add unique constraint (best-effort)
        try {
            DB::statement("ALTER TABLE `table_buku` MODIFY `uuid` CHAR(36) NOT NULL;");
            DB::statement("ALTER TABLE `table_buku` ADD UNIQUE `table_buku_uuid_unique` (`uuid`);");
        } catch (\Exception $e) {
            // ignore if index already exists or DB cannot change column
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('table_buku', 'uuid')) {
            Schema::table('table_buku', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

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
        if (! Schema::hasColumn('table_anggota', 'uuid')) {
            Schema::table('table_anggota', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id_anggota');
            });
        }

        // Populate missing/empty uuids
        $rows = DB::table('table_anggota')->whereNull('uuid')->orWhere('uuid', '')->get(['id_anggota']);
        foreach ($rows as $r) {
            DB::table('table_anggota')
                ->where('id_anggota', $r->id_anggota)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        try {
            DB::statement("ALTER TABLE `table_anggota` MODIFY `uuid` CHAR(36) NOT NULL;");
            DB::statement("ALTER TABLE `table_anggota` ADD UNIQUE `table_anggota_uuid_unique` (`uuid`);");
        } catch (\Exception $e) {
            // ignore
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('table_anggota', 'uuid')) {
            Schema::table('table_anggota', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

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
        if (! Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->after('id');
            });
        }

        // Populate missing/empty uuids
        $rows = DB::table('users')->whereNull('uuid')->orWhere('uuid', '')->get(['id']);
        foreach ($rows as $r) {
            DB::table('users')
                ->where('id', $r->id)
                ->update(['uuid' => (string) Str::uuid()]);
        }

        try {
            DB::statement("ALTER TABLE `users` MODIFY `uuid` CHAR(36) NOT NULL;");
            DB::statement("ALTER TABLE `users` ADD UNIQUE `users_uuid_unique` (`uuid`);");
        } catch (\Exception $e) {
            // ignore if cannot alter
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'uuid')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('uuid');
            });
        }
    }
};

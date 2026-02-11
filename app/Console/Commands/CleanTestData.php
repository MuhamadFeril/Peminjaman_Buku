<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-test-data {--yes : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove test/demo rows from table_anggota';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for test entries in table_anggota...');

        $names = [
            'Test User',
            'Updated User',
            'MTYUI',
        ];

        // also delete rows with suspicious phone used in tests
        $testPhones = ['123456789', '09893848934'];

        $query = DB::table('table_anggota')
            ->whereIn('nama', $names)
            ->orWhereIn('telepon', $testPhones);

        $count = $query->count();

        if ($count === 0) {
            $this->info('No test rows found.');
            return 0;
        }

        $this->line("Found <fg=yellow>$count</> test row(s) in table_anggota.");

        if (! $this->option('yes')) {
            if (! $this->confirm('Do you want to delete these rows?')) {
                $this->info('Aborted. No rows were deleted.');
                return 0;
            }
        }

        $deleted = $query->delete();

        $this->info("Deleted $deleted row(s) from table_anggota.");
        return 0;
    }
}

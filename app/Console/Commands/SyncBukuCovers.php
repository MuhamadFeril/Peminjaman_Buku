<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Buku;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class SyncBukuCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'buku:sync-covers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync existing buku cover files into storage/app/public/covers and update DB paths';

    public function handle()
    {
        $this->info('Starting buku cover sync...');

        $bukus = Buku::all();
        $bar = $this->output->createProgressBar($bukus->count());
        $moved = 0;
        $skipped = 0;

        foreach ($bukus as $buku) {
            $bar->advance();

            $val = $buku->cover_buku;
            if (empty($val)) {
                $skipped++;
                continue;
            }

            // If already stored on public disk under the given path, skip
            if (Storage::disk('public')->exists($val)) {
                $skipped++;
                continue;
            }

            $basename = pathinfo($val, PATHINFO_BASENAME);

            // candidate locations to look for the file
            $candidates = [
                storage_path('app/public/' . $val),
                storage_path('app/public/covers/' . $basename),
                public_path($val),
                public_path('images/' . $basename),
                storage_path('app/' . $val),
                base_path($val),
            ];

            $found = false;
            foreach ($candidates as $src) {
                if ($src && File::exists($src)) {
                    $found = $src;
                    break;
                }
            }

            if (! $found) {
                // try URL -> download
                if (filter_var($val, FILTER_VALIDATE_URL)) {
                    try {
                        $contents = @file_get_contents($val);
                        if ($contents !== false) {
                            Storage::disk('public')->put('covers/' . $basename, $contents);
                            $buku->cover_buku = 'covers/' . $basename;
                            $buku->save();
                            $moved++;
                            continue;
                        }
                    } catch (\Exception $e) {
                        // ignore
                    }
                }

                $this->line("\nCould not find file for Buku id={$buku->id_buku} value={$val}");
                continue;
            }

            // ensure destination dir exists
            $destDir = storage_path('app/public/covers');
            if (! File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true);
            }

            $destPath = $destDir . DIRECTORY_SEPARATOR . $basename;

            // if source is already the dest, just update DB path
            if (realpath($found) === realpath($destPath)) {
                $buku->cover_buku = 'covers/' . $basename;
                $buku->save();
                $skipped++;
                continue;
            }

            // copy file to public storage
            try {
                File::copy($found, $destPath);
                // set visibility via storage
                Storage::disk('public')->setVisibility('covers/' . $basename, 'public');
                $buku->cover_buku = 'covers/' . $basename;
                $buku->save();
                $moved++;
            } catch (\Exception $e) {
                $this->error("Failed to copy for Buku id={$buku->id_buku}: " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->line('');
        $this->info("Done. moved={$moved}, skipped={$skipped}");

        return 0;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CleanOrphanedMedia extends Command
{
    protected $signature = 'media:clean
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Find and remove orphaned media (no associated model)';

    public function handle(): int
    {
        $orphaned = Media::whereNull('model_id')
            ->orWhere('model_id', 0)
            ->get();

        if ($orphaned->isEmpty()) {
            $this->info('No orphaned media found.');
            return self::SUCCESS;
        }

        $this->info("Found {$orphaned->count()} orphaned media items:");

        $tableData = $orphaned->map(fn ($media) => [
            'ID' => $media->id,
            'Name' => $media->name,
            'File' => $media->file_name,
            'MIME' => $media->mime_type,
            'Size' => $this->formatBytes($media->size),
            'Collection' => $media->collection_name,
        ])->toArray();

        $this->table(['ID', 'Name', 'File', 'MIME', 'Size', 'Collection'], $tableData);

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no files were deleted.');
            return self::SUCCESS;
        }

        if ($this->confirm("Delete these {$orphaned->count()} orphaned media items?")) {
            $bar = $this->output->createProgressBar($orphaned->count());
            $bar->start();

            foreach ($orphaned as $media) {
                $media->delete();
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("Deleted {$orphaned->count()} orphaned media items.");
        } else {
            $this->info('Operation cancelled.');
        }

        return self::SUCCESS;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

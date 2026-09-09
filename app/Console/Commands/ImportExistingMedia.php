<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ImportExistingMedia extends Command
{
    protected $signature = 'media:import-existing
                            {--path=storage/app/public : The relative path to scan for existing files}
                            {--collection=attachments : The collection to import into}
                            {--disk=public : The disk to import from}
                            {--dry-run : Show what would be imported without actually importing}';

    protected $description = 'Import existing uploaded files into the Media Library';

    public function handle(): int
    {
        $scanPath = $this->option('path');
        $collection = $this->option('collection');
        $disk = $this->option('disk');
        $dryRun = $this->option('dry-run');

        $diskInstance = Storage::disk($disk);

        if (!$diskInstance->exists($scanPath)) {
            $this->error("Path '{$scanPath}' does not exist on disk '{$disk}'.");
            return self::FAILURE;
        }

        $files = $diskInstance->allFiles($scanPath);

        if (empty($files)) {
            $this->info('No files found at the specified path.');
            return self::SUCCESS;
        }

        $this->info("Found " . count($files) . " files to import.");

        // Filter out media library internal files and already-imported files
        $existingPaths = Media::pluck('file_name')->toArray();
        $toImport = array_filter($files, function ($file) use ($existingPaths) {
            $basename = basename($file);
            return !in_array($basename, $existingPaths) && !Str::startsWith($basename, '.');
        });

        $this->info(count($toImport) . " new files to import.");

        if (empty($toImport)) {
            $this->info('All files already imported.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->newLine();
            $this->info('Dry run — files that would be imported:');
            foreach ($toImport as $file) {
                $this->line("  - {$file}");
            }
            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($toImport));
        $bar->start();

        $imported = 0;
        $errors = 0;

        foreach ($toImport as $filePath) {
            try {
                $fullPath = Storage::disk($disk)->path($filePath);
                $mimeType = mime_content_type($fullPath);
                $fileSize = filesize($fullPath);
                $originalName = pathinfo($filePath, PATHINFO_FILENAME);

                $media = Media::create([
                    'model_type' => null,
                    'model_id' => null,
                    'collection_name' => $collection,
                    'name' => $originalName,
                    'file_name' => basename($filePath),
                    'mime_type' => $mimeType,
                    'disk' => $disk,
                    'size' => $fileSize,
                    'manipulations' => json_encode([]),
                    'custom_properties' => json_encode([
                        'imported_from' => $filePath,
                        'imported_at' => now()->toISOString(),
                    ]),
                    'generated_conversions' => json_encode([]),
                    'responsive_images' => json_encode([]),
                ]);

                $imported++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to import '{$filePath}': {$e->getMessage()}");
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Imported {$imported} files.");
        if ($errors > 0) {
            $this->warn("{$errors} files failed to import.");
        }

        return self::SUCCESS;
    }
}

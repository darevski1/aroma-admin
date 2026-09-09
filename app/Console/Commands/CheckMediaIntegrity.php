<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CheckMediaIntegrity extends Command
{
    protected $signature = 'media:check-integrity';

    protected $description = 'Check for missing files and broken media references';

    public function handle(): int
    {
        $mediaItems = Media::all();
        $missingFiles = 0;
        $missingConversions = 0;
        $issues = [];

        $bar = $this->output->createProgressBar($mediaItems->count());
        $bar->start();

        foreach ($mediaItems as $media) {
            // Check original file
            $disk = Storage::disk($media->disk);
            $originalPath = $media->getPath();

            if (!$disk->exists($media->file_name)) {
                $issues[] = [
                    'ID' => $media->id,
                    'Type' => 'Missing Original',
                    'File' => $media->file_name,
                    'Collection' => $media->collection_name,
                    'Model' => $media->model_type . '#' . $media->model_id,
                ];
                $missingFiles++;
            }

            // Check conversions
            foreach ($media->getGeneratedConversions() as $conversionName => $generated) {
                if ($generated) {
                    $conversionPath = $media->getPath($conversionName);
                    $conversionFileName = $media->file_name;
                    // Check if the conversion file exists
                    $conversionDir = $media->id . '/conversions/';
                    $extension = pathinfo($conversionFileName, PATHINFO_EXTENSION);
                    $baseName = pathinfo($conversionFileName, PATHINFO_FILENAME);
                    $conversionFile = $conversionDir . $baseName . '-' . $conversionName . '.' . $extension;

                    if (!$disk->exists($conversionFile)) {
                        $issues[] = [
                            'ID' => $media->id,
                            'Type' => "Missing Conversion: {$conversionName}",
                            'File' => $conversionFile,
                            'Collection' => $media->collection_name,
                            'Model' => $media->model_type . '#' . $media->model_id,
                        ];
                        $missingConversions++;
                    }
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if (empty($issues)) {
            $this->info("All {$mediaItems->count()} media items are intact.");
            return self::SUCCESS;
        }

        $this->warn("Found " . count($issues) . " issues:");
        $this->table(['ID', 'Type', 'File', 'Collection', 'Model'], $issues);

        $this->newLine();
        $this->info("Summary: {$missingFiles} missing originals, {$missingConversions} missing conversions.");

        return self::FAILURE;
    }
}

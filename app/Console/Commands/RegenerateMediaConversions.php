<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class RegenerateMediaConversions extends Command
{
    protected $signature = 'media:regenerate
                            {--collection= : The collection to regenerate}
                            {--model= : The model class to regenerate for}
                            {--id= : Specific media ID to regenerate}';

    protected $description = 'Regenerate all media conversions and responsive images';

    public function handle(): int
    {
        $query = Media::query();

        if ($this->option('collection')) {
            $query->where('collection_name', $this->option('collection'));
        }

        if ($this->option('model')) {
            $query->where('model_type', $this->option('model'));
        }

        if ($this->option('id')) {
            $query->where('id', $this->option('id'));
        }

        $mediaItems = $query->get();

        if ($mediaItems->isEmpty()) {
            $this->warn('No media items found matching the criteria.');
            return self::SUCCESS;
        }

        $this->info("Regenerating conversions for {$mediaItems->count()} media items...");

        $bar = $this->output->createProgressBar($mediaItems->count());
        $bar->start();

        $errors = 0;

        foreach ($mediaItems as $media) {
            try {
                $media->generateConversions();
                $media->generateResponsiveImages();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed to regenerate media #{$media->id}: {$e->getMessage()}");
                $errors++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Done! Regenerated conversions for {$mediaItems->count()} items.");
        if ($errors > 0) {
            $this->warn("{$errors} items failed. Check logs for details.");
        }

        return self::SUCCESS;
    }
}

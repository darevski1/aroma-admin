<?php

namespace App\Services\SiteImages;

use App\Models\SiteImageOverride;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/** Where a Media Library file is referenced, so deleting it never silently breaks a page. */
class MediaUsage
{
    /** table => [label, columns that may embed the file's URL] */
    private const TEXT_COLUMNS = [
        'pages' => ['Page', ['content', 'settings', 'featured_image']],
        'posts' => ['Post', ['content', 'excerpt']],
        'homepage_sections' => ['Homepage section', ['content']],
    ];

    /** @return array<int,array{type:string,label:string}> */
    public function for(Media $media): array
    {
        $uses = [];

        SiteImageOverride::where('media_id', $media->id)->orWhere('mobile_media_id', $media->id)->get()
            ->each(function ($o) use (&$uses) {
                $uses[] = ['type' => 'site-image', 'label' => 'Site image on ' . ($o->isGlobal() ? 'all pages' : $o->scope_path) . ' (replaces ' . basename($o->src_key) . ')'];
            });

        if ($media->model_type && $media->model_id) {
            $uses[] = ['type' => 'model', 'label' => class_basename($media->model_type) . ' #' . $media->model_id . ' (' . $media->collection_name . ')'];
        }

        foreach (['cenobots_clients' => ['logo_media_id', 'photo_media_id']] as $table => $cols) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col) && DB::table($table)->where($col, $media->id)->exists()) {
                    $uses[] = ['type' => 'record', 'label' => 'CenoBots client (' . str_replace('_media_id', '', $col) . ')'];
                }
            }
        }

        // Embedded by URL in stored content. Match on the "{id}/{file}" tail of the storage URL.
        $needle = '/' . $media->id . '/' . rawurlencode($media->file_name);
        $needleRaw = '/' . $media->id . '/' . $media->file_name;
        $needleJson = str_replace('/', '\\\\/', $needleRaw);
        foreach (self::TEXT_COLUMNS as $table => [$label, $columns]) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            foreach ($columns as $col) {
                if (!Schema::hasColumn($table, $col)) {
                    continue;
                }
                $count = DB::table($table)->where(fn ($q) => $q->where($col, 'like', '%' . $needle . '%')->orWhere($col, 'like', '%' . $needleRaw . '%')->orWhere($col, 'like', '%' . $needleJson . '%'))->count();
                if ($count) {
                    $uses[] = ['type' => 'content', 'label' => "{$label} {$col} ({$count})"];
                }
            }
        }

        return $uses;
    }
}

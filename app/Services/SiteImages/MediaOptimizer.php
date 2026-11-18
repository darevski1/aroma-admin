<?php

namespace App\Services\SiteImages;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Creates Media Library records for uploaded images: de-duplicates by content hash,
 * downsizes oversized rasters, builds WebP srcset variants and sanitises SVGs.
 */
class MediaOptimizer
{
    public const MAX_WIDTH = 2400;
    public const VARIANT_WIDTHS = [480, 960, 1600];
    public const ALLOWED = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/avif', 'image/svg+xml'];

    /** Store an uploaded image once; an identical file already in the library is reused. */
    public function ingest(UploadedFile $file, string $collection = 'default'): Media
    {
        $hash = sha1_file($file->getRealPath());
        $existing = Media::query()->where('custom_properties->hash', $hash)->first();
        if ($existing) {
            return $existing;
        }

        $mime = $file->getMimeType();
        $disk = config('media-library.disk_name', 'public');
        $fileName = $this->safeName($file->getClientOriginalName());

        $media = Media::create([
            'model_type' => null,
            'model_id' => null,
            'collection_name' => $collection,
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $mime,
            'disk' => $disk,
            'conversions_disk' => config('media-library.conversions_disk_name') ?? $disk,
            'size' => $file->getSize(),
            'manipulations' => [],
            'custom_properties' => ['hash' => $hash],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);
        $file->storeAs((string) $media->id, $fileName, $disk);
        $this->optimize($media);

        return $media;
    }

    /** Swap the file behind an existing Media row (keeps its id, so every usage follows). */
    public function replaceFile(Media $media, UploadedFile $file): Media
    {
        $disk = Storage::disk($media->disk);
        $disk->deleteDirectory($media->id . '/responsive');
        $disk->deleteDirectory($media->id . '/conversions');
        if ($disk->exists($media->id . '/' . $media->file_name)) {
            $disk->delete($media->id . '/' . $media->file_name);
        }
        $fileName = $this->safeName($file->getClientOriginalName());
        $props = $media->custom_properties ?? [];
        unset($props['variants'], $props['width'], $props['height']);
        $props['hash'] = sha1_file($file->getRealPath());
        $media->forceFill([
            'name' => pathinfo($fileName, PATHINFO_FILENAME),
            'file_name' => $fileName,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'custom_properties' => $props,
        ])->save();
        $file->storeAs((string) $media->id, $fileName, $media->disk);
        $this->optimize($media);
        SiteImages::bump();

        return $media->refresh();
    }

    public function optimize(Media $media): void
    {
        $disk = Storage::disk($media->disk);
        $path = $media->id . '/' . $media->file_name;
        $props = $media->custom_properties ?? [];

        if ($media->mime_type === 'image/svg+xml') {
            $disk->put($path, $this->sanitizeSvg((string) $disk->get($path)));
            $media->forceFill(['size' => $disk->size($path)])->save();

            return;
        }

        $abs = $disk->path($path);
        $info = @getimagesize($abs);
        if (!$info || !function_exists('imagecreatefromstring')) {
            return;
        }
        [$w, $h] = $info;
        $props['width'] = $w;
        $props['height'] = $h;

        $isRaster = in_array($media->mime_type, ['image/jpeg', 'image/png', 'image/webp'], true);
        if ($isRaster && ($img = @imagecreatefromstring((string) file_get_contents($abs)))) {
            if ($w > self::MAX_WIDTH) {
                $nh = (int) round($h * self::MAX_WIDTH / $w);
                $img = $this->resized($img, self::MAX_WIDTH, $nh, $media->mime_type !== 'image/jpeg');
                $this->write($img, $abs, $media->mime_type);
                [$w, $h] = [self::MAX_WIDTH, $nh];
                $props['width'] = $w;
                $props['height'] = $h;
                $media->size = filesize($abs);
            }
            if (function_exists('imagewebp')) {
                $variants = [];
                foreach (self::VARIANT_WIDTHS as $vw) {
                    if ($vw >= $w) {
                        continue;
                    }
                    $copy = $this->resized($img, $vw, (int) round($h * $vw / $w), true);
                    $rel = $media->id . '/responsive/' . $vw . '.webp';
                    $disk->makeDirectory($media->id . '/responsive');
                    imagewebp($copy, $disk->path($rel), 82);
                    $variants[] = ['w' => $vw, 'path' => $rel];
                }
                $props['variants'] = $variants;
            }
        }

        $media->forceFill(['custom_properties' => $props])->save();
    }

    private function resized(\GdImage $src, int $w, int $h, bool $alpha)
    {
        $dst = imagecreatetruecolor($w, $h);
        if ($alpha) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, imagesx($src), imagesy($src));

        return $dst;
    }

    private function write(\GdImage $img, string $abs, string $mime): void
    {
        match ($mime) {
            'image/png' => imagepng($img, $abs, 6),
            'image/webp' => imagewebp($img, $abs, 85),
            default => imagejpeg($img, $abs, 85),
        };
    }

    /** Remove scripting from SVGs so an uploaded file can never run code in the browser. */
    public function sanitizeSvg(string $svg): string
    {
        $svg = preg_replace('~<\?xml.*?\?>|<!DOCTYPE.*?>~is', '', $svg);
        $svg = preg_replace('~<(script|foreignObject|iframe|embed|object)\b.*?</\1\s*>~is', '', $svg);
        $svg = preg_replace('~<(script|foreignObject|iframe|embed|object)\b[^>]*/?>~is', '', $svg);
        $svg = preg_replace('~\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)~i', '', $svg);
        $svg = preg_replace('~(href|xlink:href)\s*=\s*(["\'])\s*(javascript|data):[^"\']*\2~i', '$1=$2$2', $svg);

        return trim($svg);
    }

    private function safeName(string $name): string
    {
        $base = preg_replace('~[^A-Za-z0-9._-]+~', '-', pathinfo($name, PATHINFO_FILENAME)) ?: 'image';
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        return trim($base, '-.') . ($ext ? '.' . $ext : '');
    }

    // ── URLs used by the frontend ───────────────────────────────

    public static function publicUrl(Media $media): string
    {
        return self::relative($media->getUrl());
    }

    public static function srcset(Media $media): ?string
    {
        $variants = $media->custom_properties['variants'] ?? [];
        if (!$variants) {
            return null;
        }
        $parts = [];
        foreach ($variants as $v) {
            $parts[] = self::relative(Storage::disk($media->disk)->url($v['path'])) . ' ' . $v['w'] . 'w';
        }
        $w = $media->custom_properties['width'] ?? null;
        if ($w) {
            $parts[] = self::publicUrl($media) . ' ' . $w . 'w';
        }

        return count($parts) > 1 ? implode(', ', $parts) : null;
    }

    private static function relative(string $url): string
    {
        return preg_match('~^https?://[^/]+(/.*)$~i', $url, $m) ? $m[1] : $url;
    }
}

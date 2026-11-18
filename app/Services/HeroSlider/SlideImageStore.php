<?php

namespace App\Services\HeroSlider;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores hero slide images on the "public" disk.
 *
 * Images larger than the target size are scaled down and, when GD can, saved
 * as WebP so visitors download far less. If optimising fails the original
 * upload is stored unchanged.
 */
class SlideImageStore
{
    public const DIR = 'hero-sliders';

    /** Largest [width, height] kept for each variant. */
    private const MAX_SIZE = ['desktop' => [2400, 2400], 'mobile' => [1200, 1800]];

    private const QUALITY = 82;

    /** @return array{path: string, width: ?int, height: ?int} */
    public function store(UploadedFile $file, string $variant): array
    {
        $optimised = $this->optimise($file, ...(self::MAX_SIZE[$variant] ?? self::MAX_SIZE['desktop']));
        if ($optimised) {
            return $optimised;
        }

        $path = $file->store(self::DIR, 'public');
        [$w, $h] = @getimagesize($file->getRealPath()) ?: [null, null];

        return ['path' => $path, 'width' => $w, 'height' => $h];
    }

    /** Copy a stored image (used when a slide is duplicated). */
    public function copy(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }
        $new = self::DIR . '/' . Str::random(40) . '.' . pathinfo($path, PATHINFO_EXTENSION);
        Storage::disk('public')->copy($path, $new);

        return $new;
    }

    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, self::DIR . '/')) {
            Storage::disk('public')->delete($path);
        }
    }

    private function optimise(UploadedFile $file, int $maxWidth, int $maxHeight): ?array
    {
        if (! function_exists('imagewebp') || ! function_exists('imagecreatefromstring')) {
            return null;
        }

        try {
            $info = @getimagesize($file->getRealPath());
            if (! $info) {
                return null;
            }
            [$w, $h] = $info;
            // Keep the original if it is already small and in an efficient format.
            $scale = min(1, $maxWidth / $w, $maxHeight / $h);
            if ($scale === 1 && in_array($info['mime'], ['image/webp', 'image/avif'], true)) {
                return null;
            }

            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));

            // Decoded bitmaps take ~5 bytes per pixel and the WebP encoder about
            // as much again. Running out of memory is a fatal error that
            // try/catch cannot stop, so check first.
            $needed = $w * $h * 5 + $nw * $nh * 10 + (int) $file->getSize() + 16 * 1024 * 1024;
            if (! $this->ensureMemory($needed)) {
                return null;
            }

            $src = @imagecreatefromstring(file_get_contents($file->getRealPath()));
            if (! $src) {
                return null;
            }

            $dst = imagecreatetruecolor($nw, $nh);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            imagedestroy($src);

            ob_start();
            $ok = imagewebp($dst, null, self::QUALITY);
            $bytes = ob_get_clean();
            imagedestroy($dst);

            if (! $ok || ! $bytes) {
                return null;
            }

            $path = self::DIR . '/' . Str::random(40) . '.webp';
            Storage::disk('public')->put($path, $bytes);

            return ['path' => $path, 'width' => $nw, 'height' => $nh];
        } catch (\Throwable) {
            return null;
        }
    }

    /** Make sure $needed more bytes fit under memory_limit, raising it up to 512 MB if allowed. */
    private function ensureMemory(int $needed): bool
    {
        $limit = $this->bytes((string) ini_get('memory_limit'));
        if ($limit <= 0) {
            return true; // unlimited
        }
        $want = memory_get_usage(true) + $needed;
        if ($want <= $limit) {
            return true;
        }
        if ($want > 512 * 1024 * 1024) {
            return false;
        }

        return @ini_set('memory_limit', (string) $want) !== false
            && $this->bytes((string) ini_get('memory_limit')) >= $want;
    }

    private function bytes(string $value): int
    {
        $value = trim($value);
        $n = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $n * 1024 ** 3,
            'm' => $n * 1024 ** 2,
            'k' => $n * 1024,
            default => $n,
        };
    }
}

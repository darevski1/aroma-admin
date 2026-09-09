<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaHelper
{
    /**
     * Get an optimized image tag using Spatie Media Library conversions.
     */
    public static function image(
        Media|string|null $media,
        string $conversion = 'medium',
        array $attributes = [],
        bool $responsive = false
    ): string {
        if (is_string($media) || is_null($media)) {
            if (empty($media)) {
                return '';
            }
            // If it's a plain URL string, return a basic img tag
            $attrs = array_merge([
                'src' => $media,
                'alt' => $attributes['alt'] ?? '',
                'loading' => $attributes['loading'] ?? 'lazy',
            ], $attributes);
            unset($attrs['class']);
            $class = $attributes['class'] ?? '';
            return '<img ' . self::buildAttributes($attrs) . ' class="' . e($class) . '">';
        }

        if ($responsive) {
            return $media->responsiveImage($conversion, [], [], $attributes);
        }

        $src = $media->getUrl($conversion);
        $alt = $attributes['alt'] ?? $media->getCustomProperty('alt_text', $media->name);
        $width = $attributes['width'] ?? null;
        $height = $attributes['height'] ?? null;
        $loading = $attributes['loading'] ?? 'lazy';
        $class = $attributes['class'] ?? '';
        $sizes = $attributes['sizes'] ?? null;

        $imgAttrs = [
            'src' => $src,
            'alt' => $alt,
            'loading' => $loading,
        ];

        if ($width) $imgAttrs['width'] = $width;
        if ($height) $imgAttrs['height'] = $height;
        if ($class) $imgAttrs['class'] = $class;

        // Add srcset for responsive images if available
        $responsiveImages = $media->getGeneratedResponsiveImages($conversion);
        if ($responsiveImages && count($responsiveImages) > 0) {
            $srcsetParts = [$src . ' ' . ($media->getWidth() ?? 800) . 'w'];
            foreach ($responsiveImages as $responsiveImage) {
                $srcsetParts[] = $media->getTemporaryUrl() . ' ' . $responsiveImage['width'] . 'w';
            }
            // Use the responsive image URLs properly
            $srcset = self::buildSrcSet($media, $conversion);
            if ($srcset) {
                $imgAttrs['srcset'] = $srcset;
                $imgAttrs['sizes'] = $sizes ?: '(max-width: 768px) 100vw, 50vw';
            }
        }

        return '<img ' . self::buildAttributes($imgAttrs) . '>';
    }

    /**
     * Build srcset attribute for responsive images.
     */
    public static function buildSrcSet(Media $media, string $conversion = 'medium'): ?string
    {
        $responsiveImages = $media->getGeneratedResponsiveImages($conversion);
        if (!$responsiveImages || count($responsiveImages) === 0) {
            return null;
        }

        $parts = [];
        foreach ($responsiveImages as $responsiveImage) {
            $width = $responsiveImage['width'];
            $url = $media->getTemporaryUrl() ?? $media->getUrl($conversion);
            // The URL pattern for responsive images
            $path = $media->getPath($conversion);
            $responsivePath = str_replace(
                $media->file_name . '.' . $media->extension,
                $media->file_name . '-' . $width . 'w.' . $media->extension,
                $path
            );

            $parts[] = $url . ' ' . $width . 'w';
        }

        return implode(', ', $parts);
    }

    /**
     * Get a media URL safely, returning empty string if no media.
     */
    public static function url(?Media $media, string $conversion = ''): string
    {
        if (!$media) {
            return '';
        }

        return $conversion ? $media->getUrl($conversion) : $media->getUrl();
    }

    /**
     * Get a responsive image HTML string for a collection.
     */
    public static function responsiveImage(
        Media $media,
        string $conversion = 'medium',
        array $attributes = []
    ): string {
        return $media->responsiveImage($conversion, [], [], $attributes);
    }

    /**
     * Get the first media item from a model's collection.
     */
    public static function firstMedia($model, string $collection, string $conversion = 'medium'): ?Media
    {
        if (!method_exists($model, 'getFirstMedia')) {
            return null;
        }

        $media = $model->getFirstMedia($collection);
        if ($media && $conversion) {
            return $media;
        }

        return $media;
    }

    /**
     * Build an HTML attributes string from an array.
     */
    protected static function buildAttributes(array $attributes): string
    {
        $parts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $parts[] = e($key);
            } elseif ($value !== false && $value !== null) {
                $parts[] = e($key) . '="' . e($value) . '"';
            }
        }

        return implode(' ', $parts);
    }
}

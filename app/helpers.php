<?php

use App\Support\MediaLibrary\MediaHelper;

if (!function_exists('media_image')) {
    /**
     * Render an optimized image tag from a Media model or URL.
     *
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media|string|null $media
     * @param string $conversion
     * @param array $attributes
     * @param bool $responsive
     * @return string
     */
    function media_image($media, string $conversion = 'medium', array $attributes = [], bool $responsive = false): string
    {
        return MediaHelper::image($media, $conversion, $attributes, $responsive);
    }
}

if (!function_exists('media_url')) {
    /**
     * Get the URL for a media item or its conversion.
     */
    function media_url($media, string $conversion = ''): string
    {
        return MediaHelper::url($media, $conversion);
    }
}

if (!function_exists('media_first')) {
    /**
     * Get the first media item from a model's collection.
     */
    function media_first($model, string $collection): ?\Spatie\MediaLibrary\MediaCollections\Models\Media
    {
        if (!method_exists($model, 'getFirstMedia')) {
            return null;
        }

        return $model->getFirstMedia($collection);
    }
}

<?php

use Spatie\ImageOptimizer\Optimizers\Avifenc;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Avif;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Pdf;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Svg;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Video;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Webp;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Spatie\MediaLibrary\Downloaders\DefaultDownloader;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Models\Observers\MediaObserver;
use Spatie\MediaLibrary\ResponsiveImages\Jobs\GenerateResponsiveImagesJob;
use Spatie\MediaLibrary\ResponsiveImages\TinyPlaceholderGenerator\Blurred;
use Spatie\MediaLibrary\ResponsiveImages\WidthCalculator\FileSizeOptimizedWidthCalculator;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Disk
    |--------------------------------------------------------------------------
    */
    'disk_name' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Conversions Disk
    |--------------------------------------------------------------------------
    */
    'conversions_disk_name' => env('MEDIA_CONVERSIONS_DISK', null),

    /*
    |--------------------------------------------------------------------------
    | Max File Size
    |--------------------------------------------------------------------------
    */
    'max_file_size' => 1024 * 1024 * 10, // 10MB

    /*
    |--------------------------------------------------------------------------
    | Disallowed Extensions
    |--------------------------------------------------------------------------
    */
    'disallowed_extensions' => [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8',
        'phtml', 'phps', 'cgi', 'pl', 'asp', 'aspx', 'jsp',
        'sh', 'bash', 'exe', 'bat', 'cmd', 'com', 'msi',
        'scr', 'jar', 'vbs', 'vbe', 'js', 'wsh', 'wsf',
        'reg', 'inf', 'ini', 'config', 'env', 'htaccess',
        'htpasswd', 'sql', 'db', 'sqlite', 'log', 'bak',
    ],

    'allowed_extensions' => null,

    /*
    |--------------------------------------------------------------------------
    | Queue Configuration
    |--------------------------------------------------------------------------
    */
    'queue_connection_name' => env('QUEUE_CONNECTION', 'database'),
    'queue_name' => env('MEDIA_QUEUE', 'media'),
    'queue_conversions_by_default' => true,
    'queue_conversions_after_database_commit' => true,

    /*
    |--------------------------------------------------------------------------
    | Model & Observer
    |--------------------------------------------------------------------------
    */
    'media_model' => Media::class,
    'media_observer' => MediaObserver::class,

    'use_default_collection_serialization' => false,

    /*
    |--------------------------------------------------------------------------
    | File Naming & Paths
    |--------------------------------------------------------------------------
    */
    'file_namer' => \App\Support\MediaLibrary\SecureFileNamer::class,
    'path_generator' => \App\Support\MediaLibrary\OrganizedPathGenerator::class,
    'file_remover_class' => \Spatie\MediaLibrary\Support\FileRemover\DefaultFileRemover::class,
    'url_generator' => \Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator::class,

    'custom_path_generators' => [],

    'moves_media_on_update' => false,
    'version_urls' => false,

    /*
    |--------------------------------------------------------------------------
    | Image Optimizers
    |--------------------------------------------------------------------------
    */
    'image_optimizers' => [
        Jpegoptim::class => [
            '-m85',
            '--force',
            '--strip-all',
            '--all-progressive',
        ],
        Pngquant::class => [
            '--force',
        ],
        Optipng::class => [
            '-i0',
            '-o2',
            '-quiet',
        ],
        Svgo::class => [
            '--disable=cleanupIDs',
        ],
        Gifsicle::class => [
            '-b',
            '-O3',
        ],
        Cwebp::class => [
            '-m 6',
            '-pass 10',
            '-mt',
            '-q 90',
        ],
        Avifenc::class => [
            '-a cq-level=23',
            '-j all',
            '--min 0',
            '--max 63',
            '--minalpha 0',
            '--maxalpha 63',
            '-a end-usage=q',
            '-a tune=ssim',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Generators
    |--------------------------------------------------------------------------
    */
    'image_generators' => [
        Image::class,
        Webp::class,
        Avif::class,
        Pdf::class,
        Svg::class,
        Video::class,
    ],

    'temporary_directory_path' => null,

    'image_driver' => env('IMAGE_DRIVER', 'gd'),

    'ffmpeg_path' => env('FFMPEG_PATH', '/usr/bin/ffmpeg'),
    'ffprobe_path' => env('FFPROBE_PATH', '/usr/bin/ffprobe'),
    'ffmpeg_timeout' => env('FFMPEG_TIMEOUT', 900),
    'ffmpeg_threads' => env('FFMPEG_THREADS', 0),

    /*
    |--------------------------------------------------------------------------
    | Jobs
    |--------------------------------------------------------------------------
    */
    'jobs' => [
        'perform_conversions' => PerformConversionsJob::class,
        'generate_responsive_images' => GenerateResponsiveImagesJob::class,
    ],

    'media_downloader' => DefaultDownloader::class,
    'media_downloader_ssl' => env('MEDIA_DOWNLOADER_SSL', true),

    'temporary_url_default_lifetime' => env('MEDIA_TEMPORARY_URL_DEFAULT_LIFETIME', 5),

    'remote' => [
        'extra_headers' => [
            'CacheControl' => 'max-age=604800',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Responsive Images
    |--------------------------------------------------------------------------
    */
    'responsive_images' => [
        'width_calculator' => FileSizeOptimizedWidthCalculator::class,
        'use_tiny_placeholders' => true,
        'tiny_placeholder_generator' => Blurred::class,
    ],

    'enable_vapor_uploads' => env('ENABLE_MEDIA_LIBRARY_VAPOR_UPLOADS', false),

    'default_loading_attribute_value' => null,

    'prefix' => env('MEDIA_PREFIX', ''),

    'force_lazy_loading' => env('FORCE_MEDIA_LIBRARY_LAZY_LOADING', true),
];

<?php

namespace App\Support\MediaLibrary;

use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Support\FileNamer\FileNamer;

// FileNamer is an abstract CLASS in spatie/laravel-medialibrary,
// so it must be extended, not implemented.
class SecureFileNamer extends FileNamer
{
    public function originalFileName(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }

    public function conversionFileName(string $file, Conversion $conversion): string
    {
        $baseName = pathinfo($file, PATHINFO_FILENAME);

        return $baseName . '-' . $conversion->getConversionName();
    }

    public function responsiveFileName(string $file): string
    {
        return pathinfo($file, PATHINFO_FILENAME);
    }
}

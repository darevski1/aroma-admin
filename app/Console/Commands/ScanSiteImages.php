<?php

namespace App\Console\Commands;

use App\Services\SiteImages\SiteScanner;
use Illuminate\Console\Command;

class ScanSiteImages extends Command
{
    protected $signature = 'images:scan';

    protected $description = 'Render every public page and record the images it shows (feeds Admin → Site Images)';

    public function handle(SiteScanner $scanner): int
    {
        $result = $scanner->run(fn ($path, $r) => $this->line(sprintf('%-60s %s', $path, is_int($r) ? "$r images" : "FAILED: $r")));
        $this->info(count($result) . ' pages scanned, ' . array_sum(array_filter($result, 'is_int')) . ' image slots.');

        return self::SUCCESS;
    }
}

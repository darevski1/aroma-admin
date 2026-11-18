<?php

namespace App\Services\SiteImages;

use App\Models\Page;
use Illuminate\Support\Facades\Http;

/** Renders every public page and records the images it shows (the inventory behind Admin → Site Images). */
class SiteScanner
{
    public function __construct(private SiteImages $images)
    {
    }

    /** @return string[] public paths */
    public function paths(): array
    {
        $paths = ['/'];
        foreach (Page::query()->where('published', true)->get() as $page) {
            $url = $page->public_url;
            $paths[] = SiteImages::pathOf(\Illuminate\Http\Request::create($url));
        }
        foreach (config('cenobots.models', []) as $model) {
            $paths[] = '/modeli/' . $model['id'];
        }
        $paths[] = '/modeli';
        $paths[] = '/services/cenobots';

        return array_values(array_unique($paths));
    }

    /** @return array<string,int|string> path => image count, or an error message */
    public function run(?callable $progress = null): array
    {
        $result = [];
        foreach ($this->paths() as $path) {
            try {
                $response = Http::timeout(30)->withoutVerifying()->withHeaders(['User-Agent' => 'SiteImageScanner'])->get(url($path));
                if (!$response->successful()) {
                    $result[$path] = 'HTTP ' . $response->status();
                    continue;
                }
                $result[$path] = count($this->images->record($path, $response->body(), prune: true));
            } catch (\Throwable $e) {
                $result[$path] = $e->getMessage();
            }
            $progress && $progress($path, $result[$path]);
        }

        return $result;
    }
}

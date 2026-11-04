<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Public sitemap: static routes, CenoBots models and published pages.
     * Pages that are noindex, or whose canonical points elsewhere, are left out.
     */
    public function index(): Response
    {
        $paths = [
            '/',
            '/services/cenobots',
            '/modeli',
            '/za-nas',
            '/uslugi',
            '/uslugi/mrezni-instalacii-i-odrzuvanje-na-mrezi',
            '/uslugi/odrzuvanje-na-kompjuterski-sistemi',
            '/uslugi/video-nadzor-i-kontrola-na-pristap',
            '/uslugi/servis-i-prodazba-na-printeri',
            '/portfolio',
            '/kontakt',
            '/pages',
        ];

        $urls = [];
        foreach ($paths as $path) {
            $urls[url($path)] = null;
        }

        foreach (config('cenobots.models', []) as $model) {
            $urls[url('/modeli/' . $model['id'])] = null;
        }

        $pages = Page::where('published', true)
            ->where('layout', '!=', 'welcome')
            ->get();

        foreach ($pages as $page) {
            if ($page->setting('robots_index', true) === false) {
                continue;
            }

            $canonical = trim((string) $page->setting('canonical_url', ''));
            if ($canonical !== '' && $canonical !== $page->getUrl()) {
                continue;
            }

            $urls[$page->getUrl()] = $page->updated_at?->toAtomString();
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $loc => $lastmod) {
            $xml .= "  <url>\n    <loc>" . e($loc) . "</loc>\n";
            if ($lastmod) {
                $xml .= '    <lastmod>' . e($lastmod) . "</lastmod>\n";
            }
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * robots.txt generated per request so the Sitemap line always uses APP_URL.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /home',
            'Disallow: /profile',
            'Disallow: /users',
            'Disallow: /navigation',
            'Disallow: /posts',
            'Disallow: /health',
            'Disallow: /pages/create',
            'Disallow: /pages/manual-blades',
            'Disallow: /pages/all/json',
            'Disallow: /pages/*/edit',
            'Disallow: /pages/*/editor',
            'Disallow: /pages/*/save-content',
            'Disallow: /pages/*/visual-save',
            'Disallow: /pages/*/update-content',
            'Disallow: /pages/*/editor-media',
            'Disallow: /pages/*/toggle-publish',
            'Disallow: /*?editor',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines) . "\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}

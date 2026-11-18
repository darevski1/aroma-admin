<?php

namespace App\Http\Middleware;

use App\Services\SiteImages\SiteImages;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Applies the admin's image replacements (Admin → Site Images) to public HTML pages and
 * remembers which images each page shows, so the admin can list every image.
 * Admin screens are never touched.
 */
class ApplySiteImages
{
    public function __construct(private SiteImages $images)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!$this->applies($request, $response)) {
            return $response;
        }

        try {
            $html = $response->getContent();
            $path = SiteImages::pathOf($request);

            // Skip recording for the editor iframe and for links with query strings (they show the same images).
            if (!$request->boolean('editor') && $request->query() === []) {
                $this->images->record($path, $html);
            }

            $overrides = $this->images->overridesFor($path);
            if ($overrides) {
                $response->setContent($this->images->apply($html, $overrides));
            }
        } catch (\Throwable $e) {
            report($e); // never take a public page down because of image management
        }

        return $response;
    }

    private function applies(Request $request, $response): bool
    {
        if (!$request->isMethod('GET') || !$response instanceof Response || $response->getStatusCode() !== 200) {
            return false;
        }
        if (!str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }
        $route = $request->route();
        if (!$route) {
            return false;
        }

        return in_array('page.active', $route->gatherMiddleware(), true)
            || in_array($route->getName(), ['pages.public', 'pages.show', 'pages.listing'], true);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Page;
use App\Services\HeroSlider\HeroSliderRenderer;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Renders hero slider shortcodes ([2026-1]) on public HTML pages, so a slider
 * can be placed in any page content without touching templates.
 *
 * Admin screens and the visual editor (?editor=1) are skipped: there the
 * shortcode must stay visible as plain text so it can be copied and edited.
 */
class RenderHeroSliderShortcodes
{
    public function __construct(private HeroSliderRenderer $renderer)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $this->renderer->reset();
        $response = $next($request);

        if (! $this->applies($request, $response)) {
            return $response;
        }

        $html = $response->getContent();
        if (! is_string($html)) {
            return $response;
        }

        $new = $this->renderer->replaceShortcodes($html);
        if ($page = $this->currentPage($request)) {
            $new = $this->renderer->placeAssigned($new, $page);
        }
        if ($new !== $html) {
            $response->setContent($new);
        }

        return $response;
    }

    /** The Page record the current public URL belongs to, if any. */
    private function currentPage(Request $request): ?Page
    {
        $route = $request->route();
        if (! $route) {
            return null;
        }
        if ($request->path() === '/') {
            return Page::where('layout', 'welcome')->first();
        }
        if (is_string($slug = $route->parameter('slug'))) {
            return Page::where('slug', $slug)->first();
        }

        return match ($route->getName()) {
            'cennobots.models', 'cennobots.models.section' => Page::where('layout', 'pages.cennobots.models.index')->first(),
            'cennobots.model', 'cennobots.model.section' => Page::where('layout', 'pages.cennobots.models.show')->first(),
            null => null,
            default => Page::where('route_name', $route->getName())->first(),
        };
    }

    private function applies(Request $request, $response): bool
    {
        if (! $request->isMethod('GET') || ! $response instanceof Response || $response->getStatusCode() !== 200) {
            return false;
        }
        if (! str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            return false;
        }
        // Visual editor (admins only): keep the code as editable text.
        if ($request->boolean('editor') && $request->user()?->isAdmin()) {
            return false;
        }
        if ($request->is('admin', 'admin/*')) {
            return false;
        }
        $middleware = $request->route()?->gatherMiddleware() ?? [];

        return ! in_array('admin', $middleware, true) && ! in_array('auth', $middleware, true);
    }
}

<?php

namespace App\Http\Middleware;

use App\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePageIsActive
{
    /**
     * Return a 404 for public routes whose registered page is inactive.
     * Administrators may still preview them through the page editor (?editor=1).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $page = $request->is('/')
            ? Page::where('layout', 'welcome')->first()
            : ($request->route()?->getName() ? Page::where('route_name', $request->route()->getName())->first() : null);

        if ($page && !$page->published) {
            $isAdminPreview = $request->boolean('editor') && auth()->check() && auth()->user()->isAdmin();
            abort_unless($isAdminPreview, 404);
        }

        return $next($request);
    }
}

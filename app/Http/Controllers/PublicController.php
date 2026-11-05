<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\PageTemplateConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PublicController extends Controller
{
    /**
     * Resolve the Page model for a given route name.
     */
    private function getPageByRoute(string $routeName): ?Page
    {
        return Page::where('route_name', $routeName)->first();
    }

    // ─── Public pages listing ────────────────────────────────────

    /**
     * Show a listing of all published manually-created pages.
     */
    public function pagesListing()
    {
        $pages = Page::where('published', true)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        return view('pages.listing', compact('pages'));
    }

    /**
     * Render a specific page by slug, resolving its Blade template from the layout field.
     */
    public function showPage(string $slug)
    {
        $query = Page::where('slug', $slug);

        // Unpublished pages may only be previewed by authenticated administrators.
        if (request()->boolean('editor')) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
        } else {
            $query->where('published', true);
        }

        $page = $query
            ->firstOrFail();

        if ($page->layout === 'welcome') {
            return redirect('/');
        }

        $layout = $page->layout;

        // These templates are fed by config('cenobots.models'), not by a Page record.
        if ($layout === 'pages.cennobots.models.index') {
            return $this->cennobotsModels();
        }

        // The per-model template needs a specific model, so send visitors to the catalogue.
        if ($layout === 'pages.cennobots.models.show') {
            // The visual editor edits the shared per-model template through the first model.
            if (request()->boolean('editor') && ($first = collect(config('cenobots.models'))->first())) {
                return redirect()->route('cennobots.model', ['model' => $first['id'], 'editor' => 1]);
            }

            return redirect()->route('cennobots.models');
        }

        // If the layout maps to a real Blade view, render it
        // (registered template OR any manually created Blade page added via pages/create)
        if ($layout && (PageTemplateConfig::isTemplatePage($layout) || view()->exists($layout))) {
            return view($layout, compact('page'));
        }

        // If the page has a named route, redirect there
        if ($page->route_name && Route::has($page->route_name)) {
            return redirect()->route($page->route_name);
        }

        // Fallback to the dynamic renderer
        return view('pages.dynamic', compact('page'));
    }

    // ─── Specific page routes (existing) ────────────────────────

    // cennobots page
    public function cennobots()
    {
        $page = $this->getPageByRoute('cennobots');
        $projects = \App\Models\CenobotsClient::forCarousel();
        return view('pages.cennobots.index', compact('page', 'projects'));
    }

    /**
     * All CenoBots models in one catalogue page (/modeli).
     */
    public function cennobotsModels()
    {
        return view('pages.cennobots.models.index', [
            'models' => config('cenobots.models'),
            // Lets the visual editor store its edits for the catalogue page.
            'page' => Page::where('layout', 'pages.cennobots.models.index')->first(),
        ]);
    }

    /**
     * One page per CenoBots model (/modeli/{model}), carrying only that model's
     * own copy, image and specifications, plus a compare grid of the others.
     *
     * Every model uses the same template, so the catalogue stays consistent
     * and adding a model is a config-only change.
     */
    public function cennobotsModel(string $model)
    {
        $models = collect(config('cenobots.models'))->keyBy('id');

        abort_unless($models->has($model), 404);

        return view('pages.cennobots.models.show', [
            'model' => $models->get($model),
            // Every other model, for the "compare with other models" section.
            'others' => $models->where('id', '!=', $model)->values(),
            'page' => Page::where('layout', 'pages.cennobots.models.show')->first(),
        ]);
    }

    // About us
    public function about()
    {
        $page = $this->getPageByRoute('za-nas');
        return view('pages.about.index', compact('page'));
    }

    // computerNetwork
    public function computerNetwork()
    {
        $page = $this->getPageByRoute('mrezni-instalacii-i-odrzuvanje-na-mrezi');
        return view('pages.computer_network.index', compact('page'));
    }

    public function computerWorld()
    {
        $page = $this->getPageByRoute('odrzuvanje-na-kompjuterski-sistemi');
        return view('pages.computerWorld.index', compact('page'));
    }

    public function accessControl()
    {
        $page = $this->getPageByRoute('video-nadzor-i-kontrola-na-pristap');
        return view('pages.accessControl.index', compact('page'));
    }

    public function printerWorld()
    {
        $page = $this->getPageByRoute('servis-i-prodazba-na-printeri');
        return view('pages.printerWorld.index', compact('page'));
    }

    public function uslugi()
    {
        $page = $this->getPageByRoute('uslugi');
        return view('pages.uslugi.index', compact('page'));
    }

    public function serviceDetail(string $service)
    {
        abort_unless(in_array($service, ['software', 'networks', 'servers', 'cctv', 'printing'], true), 404);

        return view('pages.uslugi.' . $service);
    }

    public function portfolio()
    {
        $page = $this->getPageByRoute('portfolio');
        return view('pages.portfolio.index', compact('page'));
    }

    public function kontakt()
    {
        $page = $this->getPageByRoute('kontakt');
        return view('pages.kontakt.index', compact('page'));
    }
}

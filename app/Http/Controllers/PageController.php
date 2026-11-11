<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\PageTemplateConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Display a listing of all pages.
     */
    public function index()
    {
        $this->syncManualBladePages();

        $pages = Page::withCount('navigations')->orderByDesc('id')->get();

        return view('admin_pages.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        $this->syncManualBladePages();

        $manualPages = collect($this->detectManualBladePages())
            ->reject(fn ($entry) => $entry['view'] === 'welcome')
            ->values()
            ->all();

        return view('admin_pages.pages.create', compact('manualPages'));
    }

    /**
     * Store a newly created page.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:pages,slug',
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:2048',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'featured_image'   => 'nullable|string|max:500',
            'layout'           => 'nullable|string|max:255',
            'route_name'       => 'nullable|string|max:255',
            'position'         => 'nullable|integer|min:0',
            'published'        => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: $this->uniquePageSlug($validated['title']);
        $validated['published'] = $request->boolean('published');
        $validated['layout'] = $validated['layout'] ?: 'pages.show';

        $page = Page::create($validated);

        // Handle featured image upload
        if ($request->hasFile('featured_image_file')) {
            $page->addMediaFromRequest('featured_image_file')
                ->toMediaCollection('featured_image');
        }

        // Handle attachment uploads
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $file) {
                $page->addMedia($file)->toMediaCollection('attachments');
            }
        }

        return redirect()->route('pages.index')->with('success', 'Page created successfully.');
    }

    /**
     * ─── Add Existing Manual Blade Page ─────────────────────────────
     * JSON list of manually created Blade pages detected in
     * resources/views/pages (used by the "Add" button on pages.create).
     */
    public function manualBlades()
    {
        return response()->json([
            'success' => true,
            'pages'   => $this->detectManualBladePages(),
        ]);
    }

    /**
     * Register an existing manually created Blade page in the pages
     * management system (no copy of the Blade file is made), optionally
     * renaming the actual Blade file/folder on disk first.
     */
    public function addExisting(Request $request)
    {
        // A previously removed page is brought back rather than re-created, so
        // its own reserved slug must not fail the uniqueness check.
        $trashed = Page::onlyTrashed()->where('layout', (string) $request->input('view'))->latest('deleted_at')->first();

        $validated = $request->validate([
            'view'       => ['required', 'string'],
            'new_name'   => ['nullable', 'string', 'max:255'],
            'title'      => ['required', 'string', 'max:255'],
            'slug'       => ['nullable', 'string', 'max:255', Rule::unique('pages', 'slug')->ignore($trashed?->id)],
            'route_name' => ['nullable', 'string', 'max:255'],
            'published'  => ['nullable'],
        ]);

        // Re-detect server side – never trust a client supplied path.
        $entry = collect($this->detectManualBladePages())
            ->firstWhere('view', $validated['view']);

        if (!$entry) {
            return back()->with('error', 'The selected Blade page could not be found in resources/views/pages.');
        }

        if ($entry['added']) {
            return back()->with('error', "\"{$entry['page_title']}\" is already in the pages management system.");
        }

        // ── Optional rename of the real Blade file / folder on disk ──
        $finalView   = $entry['view'];
        $finalPath   = $entry['path'];
        $renamed     = false;
        $newNameRaw  = trim($validated['new_name'] ?? '');

        if ($entry['kind'] !== 'registered' && $newNameRaw !== '' && $newNameRaw !== $entry['name']) {
            $newName = $this->sanitizeBladeName($newNameRaw);

            if ($newName === '' || $newName === $entry['name']) {
                return back()->with('error', 'The new Blade file name is invalid. Use letters, numbers, dashes and underscores only.');
            }

            $base = $this->manualPagesPath();

            if ($entry['kind'] === 'folder') {
                // Folder based template: about/index.blade.php → rename the folder
                $oldPath     = $base . '/' . dirname($entry['path']);
                $newPath     = $base . '/' . $newName;
                $newRelative = $newName . '/index.blade.php';
            } else {
                // Single file: about.blade.php → akcija.blade.php
                $dir         = dirname($entry['path']);
                $prefix      = $dir !== '.' ? $dir . '/' : '';
                $oldPath     = $base . '/' . $entry['path'];
                $newPath     = $base . '/' . $prefix . $newName . '.blade.php';
                $newRelative = $prefix . $newName . '.blade.php';
            }

            if (file_exists($newPath)) {
                return back()->with('error', "\"{$newName}\" already exists in resources/views/pages. Choose a different name.");
            }

            if (!@rename($oldPath, $newPath)) {
                return back()->with('error', 'Could not rename the Blade file. Please check folder permissions.');
            }

            $finalView = 'pages.' . str_replace('/', '.', substr($newRelative, 0, -strlen('.blade.php')));
            $finalPath = $newRelative;
            $renamed   = true;
        }

        // Registered system views can still have multiple Page records by
        // slug; they all point to the same template and never copy its file.
        // For directory-scanned views, retain the one-record-per-layout rule.
        if ($entry['kind'] === 'registered') {
            $slug = $validated['slug'] ?: ($trashed->slug ?? Str::slug($validated['title'], '-'));
            $slug = $slug !== '' ? $slug : 'welcome-page';
            $original = $slug;
            $i = 2;
            while ($this->manualSlugTaken($slug, $trashed)) {
                $slug = $original . '-' . $i++;
            }

            $this->persistManualPage($trashed, [
                'title' => $validated['title'],
                'slug' => $slug,
                'layout' => $finalView,
                'route_name' => $validated['route_name'] ?? $trashed?->route_name,
                'published' => $request->boolean('published'),
            ]);

            return redirect()->route('pages.index')->with('success', 'Welcome Page registered successfully without duplicating its Blade template.');
        }

        // Duplicate guard on the final layout name
        if (Page::where('layout', $finalView)->exists()) {
            if ($renamed) {
                @rename($this->manualPagesPath() . '/' . $finalPath, $this->manualPagesPath() . '/' . $entry['path']);
            }

            return back()->with('error', "A page registered to \"{$finalView}\" already exists in the pages management system.");
        }

        // Build a unique slug (auto-generated from title when empty)
        $slug = $validated['slug'] ?: ($trashed->slug ?? Str::slug($validated['title'], '-'));
        if ($slug === '') {
            $slug = Str::slug(str_replace(['.', '/'], '-', substr($finalView, strlen('pages.'))), '-');
        }
        $original = $slug;
        $i = 2;
        while ($this->manualSlugTaken($slug, $trashed)) {
            $slug = $original . '-' . $i++;
        }

        try {
            $this->persistManualPage($trashed, [
                'title'      => $validated['title'],
                'slug'       => $slug,
                'layout'     => $finalView,
                'route_name' => $validated['route_name'] ?? $trashed?->route_name,
                'published'  => $request->boolean('published'),
            ]);
        } catch (\Throwable $e) {
            // Roll the file rename back so disk and database stay consistent
            if ($renamed) {
                @rename($this->manualPagesPath() . '/' . $finalPath, $this->manualPagesPath() . '/' . $entry['path']);
            }

            return back()->with('error', 'Could not add the page: ' . $e->getMessage());
        }

        $message = $renamed
            ? "Existing Blade page added successfully (file renamed to resources/views/pages/{$finalPath})."
            : 'Existing Blade page added successfully – no duplicate copy of the file was created.';

        return redirect()->route('pages.index')->with('success', $message);
    }

    /**
     * Add several selected pages at once. Each one comes back with the title,
     * slug and content it had before it was removed.
     */
    public function addExistingSelected(Request $request)
    {
        $validated = $request->validate([
            'views'   => ['required', 'array', 'min:1'],
            'views.*' => ['required', 'string'],
        ]);

        $added = 0;

        foreach ($this->detectManualBladePages() as $entry) {
            if ($entry['trashed'] && in_array($entry['view'], $validated['views'], true)) {
                Page::onlyTrashed()->where('layout', $entry['view'])->latest('deleted_at')->first()?->restore();
                $added++;
            }
        }

        if ($added === 0) {
            return back()->with('error', 'None of the selected pages could be added.');
        }

        return redirect()->route('pages.index')->with('success', "{$added} existing page(s) added successfully.");
    }

    /**
     * Remove one page from the pages management system. Its Blade file is left
     * untouched and the page can be selected again later.
     */
    public function removeExisting(Request $request)
    {
        $validated = $request->validate([
            'view' => ['required', 'string'],
        ]);

        $entry = collect($this->detectManualBladePages())->firstWhere('view', $validated['view']);

        if (!$entry || !$entry['added']) {
            return back()->with('error', 'The selected page is not currently added.');
        }

        DB::transaction(fn () => $this->removeManualPage($entry['view']));

        return redirect()->route('pages.create')
            ->with('manual_pages_status', "\"{$entry['page_title']}\" was removed.");
    }

    /**
     * Remove every added Blade page so the selection can start from scratch.
     * Pages created with the form on this screen are not Blade pages and stay.
     */
    public function removeAllExisting()
    {
        $entries = collect($this->detectManualBladePages())->where('added', true);

        DB::transaction(fn () => $entries->each(fn ($entry) => $this->removeManualPage($entry['view'])));

        return redirect()->route('pages.create')
            ->with('manual_pages_status', "{$entries->count()} page(s) removed. Select only the pages you want.");
    }

    /** Soft-delete the records of a view, unlinking navigation as destroy() does. */
    private function removeManualPage(string $view): void
    {
        Page::where('layout', $view)->get()->each(function (Page $page) {
            $page->navigations()->update(['page_id' => null]);
            $page->delete();
        });
    }

    /** Whether a slug belongs to any page other than the one being brought back. */
    private function manualSlugTaken(string $slug, ?Page $except): bool
    {
        return Page::withTrashed()
            ->where('slug', $slug)
            ->when($except, fn ($query) => $query->whereKeyNot($except->id))
            ->exists();
    }

    /** Bring a removed page back with the given values, or create it if it never existed. */
    private function persistManualPage(?Page $trashed, array $attributes): Page
    {
        if (!$trashed) {
            return Page::create($attributes);
        }

        $trashed->restore();
        $trashed->update($attributes);

        return $trashed;
    }

    /** The live record of a view, or the most recently removed one when there is none. */
    private function manualPageRecord(string $view): ?Page
    {
        return Page::where('layout', $view)->first()
            ?? Page::onlyTrashed()->where('layout', $view)->latest('deleted_at')->first();
    }

    /**
     * Base path of the manually created pages folder.
     */
    private function manualPagesPath(): string
    {
        return resource_path('views/pages');
    }

    /**
     * Detect every manually created Blade page in resources/views/pages.
     *
     * Covers both conventions used by this project:
     *   - single files:      za-nas.blade.php          → pages.za-nas
     *   - folder templates:  about/index.blade.php     → pages.about.index
     */
    private function detectManualBladePages(): array
    {
        $base = $this->manualPagesPath();

        // System views that power the CMS itself – never offered as selectable pages.
        $systemViews = ['pages.dynamic', 'pages.listing'];

        $found = [];

        foreach (is_dir($base) ? File::allFiles($base) : [] as $file) {
            $relative = str_replace(['\\', '/'], '/', $file->getRelativePathname());

            if (!str_ends_with($relative, '.blade.php')) {
                continue;
            }

            $view = 'pages.' . str_replace('/', '.', substr($relative, 0, -strlen('.blade.php')));

            if (in_array($view, $systemViews, true) || $this->isPartialView($relative)) {
                continue;
            }

            $isFolderTemplate = str_ends_with($relative, '/index.blade.php');
            $existing = $this->manualPageRecord($view);

            $found[] = [
                'view'       => $view,
                'path'       => $relative,
                'name'       => $isFolderTemplate ? basename(dirname($relative)) : basename($relative, '.blade.php'),
                'kind'       => $isFolderTemplate ? 'folder' : 'file',
                'added'      => $existing !== null && !$existing->trashed(),
                'trashed'    => $existing?->trashed() ?? false,
                'page_id'    => $existing->id ?? null,
                'page_title' => $existing->title ?? null,
                'page_slug'  => $existing->slug ?? null,
                'page_route' => $existing->route_name ?? null,
            ];
        }

        foreach ($found as &$entry) {
            $entry['display_name'] = $entry['name'];
        }
        unset($entry);

        // The public homepage is a real, independently routed Blade page, but
        // its view lives at resources/views/welcome.blade.php rather than in
        // the pages/ directory. Expose that existing view in the same picker.
        if (view()->exists('welcome')) {
            $existing = $this->manualPageRecord('welcome');
            $found[] = [
                'view' => 'welcome',
                'path' => '../welcome.blade.php',
                'name' => 'Welcome Page',
                'display_name' => 'Welcome Page',
                'kind' => 'registered',
                'added' => $existing !== null && !$existing->trashed(),
                'trashed' => $existing?->trashed() ?? false,
                'page_id' => $existing->id ?? null,
                'page_title' => $existing->title ?? null,
                'page_slug' => $existing->slug ?? null,
                'page_route' => $existing->route_name ?? null,
            ];
        }

        usort($found, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $found;
    }

    /**
     * Keep Blade pages visible in the CMS without requiring a separate manual
     * registration step. Existing records are matched by layout and are never
     * overwritten; the manual add flow remains available for custom metadata.
     */
    public function syncManualBladePages(): void
    {
        $nextPosition = (int) Page::max('position');

        foreach ($this->detectManualBladePages() as $entry) {
            // Pages removed on purpose stay removed until they are selected again.
            if ($entry['added'] || $entry['trashed']) {
                continue;
            }

            $routeName = $entry['view'] === 'welcome' ? 'welcome' : $this->inferRouteName($entry['view']);
            $route = $routeName ? collect(Route::getRoutes())->first(fn ($candidate) => $candidate->getName() === $routeName) : null;
            $routeUri = $route?->uri();
            $routeSlug = $routeUri && !str_contains($routeUri, '{')
                ? Str::slug(basename($routeUri))
                : null;
            $slug = $entry['view'] === 'welcome'
                ? 'welcome-page'
                : ($routeSlug ?: $this->manualBladeSlug($entry));

            if ($slug === '') {
                $slug = Str::slug($entry['name']);
            }

            if (Page::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $this->manualBladeSlug($entry);
            }

            $originalSlug = $slug;
            $suffix = 2;
            while (Page::withTrashed()->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $suffix++;
            }

            Page::create([
                'title' => $entry['view'] === 'welcome'
                    ? 'Welcome Page'
                    : Str::headline(str_replace(['-', '_'], ' ', $entry['name'])),
                'slug' => $slug,
                'layout' => $entry['view'],
                'route_name' => $entry['view'] === 'welcome' ? 'welcome' : $routeName,
                'published' => true,
                'position' => ++$nextPosition,
            ]);
        }
    }

    /** Partials (`_name`, `partials/`) and backup copies are building blocks, not pages. */
    private function isPartialView(string $relativePath): bool
    {
        foreach (explode('/', $relativePath) as $segment) {
            if (str_starts_with($segment, '_') || $segment === 'partials' || str_contains($segment, ' copy')) {
                return true;
            }
        }

        return false;
    }

    /** Derive a unique, URL-safe slug (soft-deleted pages keep their slug reserved). */
    private function uniquePageSlug(string $title): string
    {
        $base = Str::slug($title, '-') ?: 'page';
        $slug = $base;
        $suffix = 2;
        while (Page::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    /** Derive a stable, URL-safe slug from a discovered Blade view path. */
    private function manualBladeSlug(array $entry): string
    {
        $path = preg_replace('/\.blade\.php$/', '', $entry['path']);
        $parts = explode('/', str_replace('\\', '/', $path));
        if (end($parts) === 'index') {
            array_pop($parts);
        }

        return Str::slug(implode('-', $parts)) ?: Str::slug($entry['name']);
    }

    /**
     * Infer the dedicated public route for a Blade template when its name is
     * an exact match or a conventional kebab-case form of the template name.
     */
    private function inferRouteName(string $view): ?string
    {
        $suffix = substr($view, strlen('pages.'));
        $path = str_replace('.', '/', $suffix);
        $baseName = basename($path);
        if ($baseName === 'index') {
            $baseName = basename(dirname($path));
        }
        $routeSlugs = array_unique([
            Str::kebab(str_replace('/', '-', $path)),
            Str::kebab($baseName),
        ]);
        $candidateRouteNames = array_unique(array_merge(
            $routeSlugs,
            array_map(fn ($slug) => 'uslugi.' . $slug, $routeSlugs)
        ));

        foreach ($candidateRouteNames as $candidate) {
            if (Route::has($candidate)) {
                return $candidate;
            }
        }

        foreach (Route::getRoutes() as $route) {
            $name = $route->getName();
            $uri = trim($route->uri(), '/');
            if ($name && !str_contains($uri, '{') && in_array($uri, $routeSlugs, true)) {
                return $name;
            }
        }

        return null;
    }

    /**
     * Sanitize a user provided Blade file/folder name.
     */
    private function sanitizeBladeName(string $name): string
    {
        $name = str_replace(' ', '-', trim($name));
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name);

        return preg_replace('/-{2,}/', '-', trim($name ?? '', '-')) ?? '';
    }

    /**
     * Show the form for editing an existing page.
     */
    public function edit(Page $page)
    {
        $page->load('navigations.parent', 'media');

        // Build the preview URL based on whether the page has a named route
        $previewUrl = $page->layout === 'welcome'
            ? url('/')
            : ($page->route_name && Route::has($page->route_name)
                ? route($page->route_name)
                : url('p/' . $page->slug));

        return view('admin_pages.pages.edit', compact('page', 'previewUrl'));
    }

    /**
     * Open the Full Page Editor for a page.
     */
    public function editor(Page $page)
    {
        // Build the URL to load in the iframe
        $editorUrl = $this->buildPageUrl($page, ['editor' => '1']);
        $previewUrl = $this->buildPageUrl($page);

        return view('admin_pages.pages.editor', compact('page', 'editorUrl', 'previewUrl'));
    }

    /**
     * Save content edited via the Full Page Editor.
     */
    public function saveContent(Request $request, Page $page)
    {
        $data = $request->validate([
            'content' => 'nullable|string|max:2000000',
            'edits' => 'nullable|array|max:2000',
            'edits.*.k' => 'required|string|max:5000',
            'edits.*.n' => 'required|integer|min:0',
            'edits.*.html' => 'present|nullable|string|max:20000',
        ]);

        // Pages rendered from a Blade template keep their markup untouched: only the
        // edited text is stored and re-applied on render. Stored-HTML pages (Home,
        // dynamic pages) keep saving the whole content region.
        $rendersTemplate = $page->layout !== 'welcome'
            && $page->layout && view()->exists($page->layout) && $page->layout !== 'pages.dynamic';

        if ($rendersTemplate) {
            $service = app(\App\Services\InlineEdits::class);
            $settings = $page->settings ?? [];
            $settings['inline_edits'] = $service->merge($settings['inline_edits'] ?? [], $data['edits'] ?? []);
            $page->forceFill(['settings' => $settings])->save();

            return response()->json(['success' => true, 'message' => 'Page content saved successfully.']);
        }

        abort_unless(isset($data['content']), 422, 'No content to save.');
        $page->forceFill(['content' => $data['content']])->save();
        abort_unless($page->fresh()->content === $data['content'], 500, 'The page content could not be verified after saving.');

        return response()->json([
            'success' => true,
            'message' => 'Page content saved successfully.',
        ]);
    }

    /**
     * Update the specified page.
     */
    public function update(Request $request, Page $page)
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'slug'             => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content'          => 'nullable|string',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:2048',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'featured_image'   => 'nullable|string|max:500',
            'layout'           => 'nullable|string|max:255',
            'route_name'       => 'nullable|string|max:255',
            'position'         => 'nullable|integer|min:0',
            'published'        => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title'], '-');
        $validated['published'] = $request->boolean('published');
        $validated['layout'] = $validated['layout'] ?: 'pages.show';

        $settings = $page->settings ?? [];
        foreach (['canonical_url', 'og_title', 'og_description', 'og_image'] as $seoKey) {
            if ($request->has($seoKey)) $settings[$seoKey] = $request->input($seoKey);
        }
        if ($request->has('robots_index')) $settings['robots_index'] = $request->boolean('robots_index');
        $validated['settings'] = $settings;
        $validated['position'] = $request->input('position', $page->position);

        if ($request->has('featured_image_file')) {
            unset($validated['featured_image']);
        }

        $page->update($validated);

        // Handle featured image upload
        if ($request->hasFile('featured_image_file')) {
            $page->clearMediaCollection('featured_image');
            $page->addMediaFromRequest('featured_image_file')
                ->toMediaCollection('featured_image');
        }

        // Handle featured image removal
        if ($request->input('remove_featured_image')) {
            $page->clearMediaCollection('featured_image');
            $page->update(['featured_image' => null]);
        }

        // Handle attachment additions
        if ($request->hasFile('attachment_files')) {
            foreach ($request->file('attachment_files') as $file) {
                $page->addMedia($file)->toMediaCollection('attachments');
            }
        }

        // Handle attachment removals
        if ($request->input('remove_attachment_media_id')) {
            foreach ($request->input('remove_attachment_media_id') as $mediaId) {
                $media = $page->getMedia('attachments')->where('id', $mediaId)->first();
                if ($media) {
                    $media->delete();
                }
            }
        }

        return redirect()->route('pages.index')->with('success', 'Page updated successfully.');
    }

    /**
     * Toggle the published status of a page.
     */
    public function togglePublish(Request $request, Page $page)
    {
        $page->update(['published' => !$page->published]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'published' => $page->published,
                'message' => $page->published ? 'Page published successfully.' : 'Page unpublished successfully.',
            ]);
        }

        $status = $page->published ? 'published' : 'unpublished';
        return redirect()->route('pages.index')->with('success', "Page {$status} successfully.");
    }

    /** Upload a file into the reusable media library, without creating a duplicate on page assignment. */
    public function uploadEditorMedia(Request $request, Page $page)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimetypes:image/jpeg,image/png,image/webp,image/avif,image/gif,image/svg+xml'],
        ]);

        $file = $data['file'];
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::query()
            ->where('collection_name', 'editor-library')
            ->where('file_name', $file->getClientOriginalName())
            ->where('size', $file->getSize())
            ->where('mime_type', $file->getMimeType())
            ->first();

        if (!$media) {
            $media = $page->addMedia($file)->toMediaCollection('editor-library');
        }

        return response()->json([
            'success' => true,
            'media' => ['id' => $media->id, 'url' => $media->getUrl(), 'name' => $media->name],
        ]);
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page)
    {
        // Unlink from navigation before deleting
        $page->navigations()->update(['page_id' => null]);

        $page->delete();

        return redirect()->route('pages.index')->with('success', 'Page deleted successfully.');
    }

    /**
     * Render a dynamic page by slug (public-facing).
     */
    public function show(string $slug)
    {
        $query = Page::where('slug', $slug);

        // When loaded in the Full Page Editor iframe (?editor=1), allow unpublished pages
        if (request()->boolean('editor')) {
            abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);
        } else {
            $query->where('published', true);
        }

        $page = $query->firstOrFail();

        if ($page->layout === 'welcome') {
            return redirect('/');
        }

        if ($page->content) {
            return view('pages.dynamic', compact('page'));
        }

        if ($page->route_name && Route::has($page->route_name)) {
            return redirect()->route($page->route_name);
        }

        return view('pages.dynamic', compact('page'));
    }

    /**
     * Return all pages as JSON (for the navigation builder page-linker).
     */
    public function allJson()
    {
        $pages = Page::where('published', true)
            ->select('id', 'title', 'slug', 'route_name', 'published')
            ->orderBy('title')
            ->get();

        return response()->json($pages);
    }

    /**
     * Show the content editing form for a template-based page.
     */
    public function editContent(Page $page)
    {
        $layout = $page->layout;

        // Every page uses this editor. Pages without registered template fields
        // (stored-HTML pages such as Home) get the HTML content area instead.
        $sections  = PageTemplateConfig::isTemplatePage((string) $layout) ? PageTemplateConfig::getFields($layout) : [];
        $previewUrl = $this->buildPageUrl($page);

        return view('admin_pages.pages.edit-content', compact('page', 'sections', 'previewUrl'));
    }

    /**
     * Save content settings for a template-based page.
     */
    public function updateContent(Request $request, Page $page)
    {
        $layout = $page->layout;

        if (!PageTemplateConfig::isTemplatePage((string) $layout)) {
            return back()->with('error', 'This page does not have an editable template.');
        }

        $sections = PageTemplateConfig::getFields($layout);
        $settings = $page->settings ?? [];

        foreach ($sections as $section) {
            if (isset($section['type']) && $section['type'] === 'repeatable') {
                $repeat = $section['repeat'] ?? 1;
                for ($i = 0; $i < $repeat; $i++) {
                    foreach ($section['fields'] as $field) {
                        $key = str_replace('{n}', $i + 1, $field['key']);
                        $settings[$key] = $this->resolveFieldValue($request, $field, $key, $settings[$key] ?? null);
                    }
                }
            } else {
                foreach ($section['fields'] as $field) {
                    $key = $field['key'];
                    $settings[$key] = $this->resolveFieldValue($request, $field, $key, $settings[$key] ?? null);
                }
            }
        }

        $page->update(['settings' => $settings]);

        return redirect()->route('pages.edit-content', $page->id)
            ->with('success', 'Page content updated successfully.');
    }

    /**
     * Save content from the visual editor (AJAX).
     */
    public function visualSave(Request $request, Page $page)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:255',
            'settings' => 'nullable|array',
            'content' => 'nullable|string|max:2000000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|url|max:2048',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'robots_index' => 'nullable|boolean',
        ]);

        $settings = $page->settings ?? [];
        foreach (['canonical_url', 'og_title', 'og_description', 'og_image', 'robots_index'] as $seoKey) {
            if (array_key_exists($seoKey, $data)) {
                $settings[$seoKey] = $data[$seoKey];
            }
        }
        if (array_key_exists('robots_index', $data)) {
            $settings['robots_index'] = (bool) $data['robots_index'];
        }
        $settings = array_replace($settings, $data['settings'] ?? []);

        $page->update([
            'title'    => $data['title'],
            'content' => array_key_exists('content', $data) ? $data['content'] : $page->content,
            'settings' => $settings,
            'meta_title' => array_key_exists('meta_title', $data) ? $data['meta_title'] : $page->meta_title,
            'meta_description' => array_key_exists('meta_description', $data) ? $data['meta_description'] : $page->meta_description,
        ]);

        $page->refresh();
        $persistedSettings = $page->settings ?? [];
        abort_unless($page->title === $data['title'] && $persistedSettings == $settings, 500, 'The page changes could not be verified after saving.');

        return response()->json([
            'success' => true,
            'message' => 'Page saved successfully.',
        ]);
    }

    /**
     * Resolve a single field value from the request, handling file uploads for image fields.
     */
    private function resolveFieldValue(Request $request, array $field, string $key, mixed $current): mixed
    {
        if (($field['type'] ?? '') === 'image' && $request->hasFile($key)) {
            $file = $request->file($key);

            if ($file->isValid()) {
                $page = $request->route('page');
                $media = $page->addMedia($file)->toMediaCollection('page-content');

                return $media->getUrl();
            }
        }

        return $request->input($key, $current);
    }

    /**
     * Build a URL for a page, optionally with extra query parameters.
     * Uses the /pages/{slug} canonical URL.
     */
    private function buildPageUrl(Page $page, array $params = []): string
    {
        if ($page->layout === 'welcome') {
            $url = url('/');
            return empty($params) ? $url : $url . '?' . http_build_query($params);
        }

        $url = url('pages/' . $page->slug);
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\PageTemplateConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Display a listing of all pages.
     */
    public function index()
    {
        $pages = Page::withCount('navigations')->with('subNavigations')->orderByDesc('id')->get();

        return view('admin_pages.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        return view('admin_pages.pages.create');
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
            'featured_image'   => 'nullable|string|max:500',
            'layout'           => 'nullable|string|max:255',
            'route_name'       => 'nullable|string|max:255',
            'published'        => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title'], '-');
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
        $validated = $request->validate([
            'view'       => ['required', 'string'],
            'new_name'   => ['nullable', 'string', 'max:255'],
            'title'      => ['required', 'string', 'max:255'],
            'slug'       => ['nullable', 'string', 'max:255', 'unique:pages,slug'],
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

        if ($newNameRaw !== '' && $newNameRaw !== $entry['name']) {
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

        // Duplicate guard on the final layout name
        if (Page::where('layout', $finalView)->exists()) {
            if ($renamed) {
                @rename($this->manualPagesPath() . '/' . $finalPath, $this->manualPagesPath() . '/' . $entry['path']);
            }

            return back()->with('error', "A page registered to \"{$finalView}\" already exists in the pages management system.");
        }

        // Build a unique slug (auto-generated from title when empty)
        $slug = $validated['slug'] ?: Str::slug($validated['title'], '-');
        if ($slug === '') {
            $slug = Str::slug(str_replace(['.', '/'], '-', substr($finalView, strlen('pages.'))), '-');
        }
        $original = $slug;
        $i = 2;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $i++;
        }

        try {
            Page::create([
                'title'      => $validated['title'],
                'slug'       => $slug,
                'layout'     => $finalView,
                'route_name' => $validated['route_name'] ?? null,
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

        if (!is_dir($base)) {
            return [];
        }

        // System views that power the CMS itself – never offered as selectable pages.
        $systemViews = ['pages.dynamic', 'pages.listing'];

        $found = [];

        foreach (File::allFiles($base) as $file) {
            $relative = str_replace(['\\', '/'], '/', $file->getRelativePathname());

            if (!str_ends_with($relative, '.blade.php')) {
                continue;
            }

            $view = 'pages.' . str_replace('/', '.', substr($relative, 0, -strlen('.blade.php')));

            if (in_array($view, $systemViews, true)) {
                continue;
            }

            $isFolderTemplate = str_ends_with($relative, '/index.blade.php');
            $existing = Page::where('layout', $view)->first();

            $found[] = [
                'view'       => $view,
                'path'       => $relative,
                'name'       => $isFolderTemplate ? basename(dirname($relative)) : basename($relative, '.blade.php'),
                'kind'       => $isFolderTemplate ? 'folder' : 'file',
                'added'      => $existing !== null,
                'page_id'    => $existing->id ?? null,
                'page_title' => $existing->title ?? null,
            ];
        }

        usort($found, fn ($a, $b) => strcmp($a['name'], $b['name']));

        return $found;
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
        $page->load('navigations', 'subNavigations.navigation');

        // Build the preview URL based on whether the page has a named route
        $previewUrl = $page->route_name && Route::has($page->route_name)
            ? route($page->route_name)
            : url('p/' . $page->slug);

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
        $request->validate([
            'content' => 'required|string',
        ]);

        $page->update([
            'content' => $request->input('content'),
        ]);

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
            'featured_image'   => 'nullable|string|max:500',
            'layout'           => 'nullable|string|max:255',
            'route_name'       => 'nullable|string|max:255',
            'published'        => 'nullable',
        ]);

        $validated['slug'] = $validated['slug'] ?: Str::slug($validated['title'], '-');
        $validated['published'] = $request->boolean('published');
        $validated['layout'] = $validated['layout'] ?: 'pages.show';

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
    public function togglePublish(Page $page)
    {
        $page->update(['published' => !$page->published]);

        $status = $page->published ? 'published' : 'unpublished';
        return redirect()->route('pages.index')->with('success', "Page {$status} successfully.");
    }

    /**
     * Remove the specified page.
     */
    public function destroy(Page $page)
    {
        // Unlink from navigation before deleting
        $page->navigations()->update(['page_id' => null]);
        $page->subNavigations()->update(['page_id' => null]);

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
        if (!request('editor')) {
            $query->where('published', true);
        }

        $page = $query->firstOrFail();

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
        $pages = Page::select('id', 'title', 'slug', 'route_name', 'published')
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

        if (!PageTemplateConfig::isTemplatePage($layout)) {
            return redirect()->route('pages.edit', $page->id)
                ->with('error', 'This page does not have an editable template.');
        }

        $sections  = PageTemplateConfig::getFields($layout);
        $previewUrl = $this->buildPageUrl($page);

        return view('admin_pages.pages.edit-content', compact('page', 'sections', 'previewUrl'));
    }

    /**
     * Save content settings for a template-based page.
     */
    public function updateContent(Request $request, Page $page)
    {
        $layout = $page->layout;

        if (!PageTemplateConfig::isTemplatePage($layout)) {
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
        ]);

        $page->update([
            'title'    => $data['title'],
            'settings' => $data['settings'] ?? $page->settings,
        ]);

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
                // Store in public storage: page-images/{page_id}/filename
                $path = $file->store('page-images/' . $request->route('page')->id, 'public');

                return Storage::url($path);
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
        $url = url('pages/' . $page->slug);
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }
}

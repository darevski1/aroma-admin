<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomepageSectionController extends Controller
{
    /**
     * Slugs already mapped to dedicated homepage partials in welcome.blade.php.
     * New sections may not use them, otherwise their content would never render.
     */
    private const RESERVED_SLUGS = [
        'hero',
        'sticky-banner',
        'brand-marquee',
        'services',
        'cenobots',
        'printers',
        'clients',
        'garmin',
        'cta',
    ];

    /**
     * Display all homepage sections.
     */
    public function index()
    {
        $sections = HomepageSection::orderBy('sort_order')->get();

        return view('admin_pages.homepage-sections.index', compact('sections'));
    }

    /**
     * Show the form for creating a new homepage section.
     */
    public function create()
    {
        return view('admin_pages.homepage-sections.create');
    }

    /**
     * Store a newly created homepage section with its content.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'slug'           => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                'unique:homepage_sections,slug',
                'not_in:' . implode(',', self::RESERVED_SLUGS),
            ],
            'eyebrow'        => 'nullable|string|max:255',
            'heading'        => 'nullable|string|max:255',
            'body'           => 'nullable|string|max:5000',
            'image_file'     => 'nullable|image|max:10240',
            'button1_label'  => 'nullable|string|max:255|required_with:button1_url',
            'button1_url'    => 'nullable|string|max:2000|required_with:button1_label',
            'button2_label'  => 'nullable|string|max:255|required_with:button2_url',
            'button2_url'    => 'nullable|string|max:2000|required_with:button2_label',
            'background'     => 'nullable|in:white,light,dark,accent',
            'published'      => 'nullable|boolean',
        ], [
            'slug.not_in' => 'This slug is reserved by a built-in homepage section. Please choose another.',
            'slug.regex'  => 'The slug may only contain lowercase letters, numbers and dashes.',
        ]);

        // ── Resolve slug (auto-generated from name when empty) ──
        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $slug = $this->uniqueSlug($slug);

        // ── Build the section content structure ──
        $content = array_filter([
            'eyebrow'    => $validated['eyebrow'] ?? null,
            'heading'    => $validated['heading'] ?? null,
            'body'       => $validated['body'] ?? null,
            'background' => $validated['background'] ?? 'light',
        ], fn ($value) => $value !== null && $value !== '');

        if (!empty($validated['button1_label'])) {
            $content['buttons'][] = [
                'label' => $validated['button1_label'],
                'url'   => $validated['button1_url'],
                'style' => 'primary',
            ];
        }

        if (!empty($validated['button2_label'])) {
            $content['buttons'][] = [
                'label' => $validated['button2_label'],
                'url'   => $validated['button2_url'],
                'style' => 'secondary',
            ];
        }

        // ── Optional image upload (stored on the public disk) ──
        if ($request->hasFile('image_file') && $request->file('image_file')->isValid()) {
            $path = $request->file('image_file')->store('homepage-sections', 'public');
            $content['image'] = Storage::url($path);
        }

        HomepageSection::create([
            'name'         => $validated['name'],
            'slug'         => $slug,
            'content'      => $content,
            'is_published' => $request->boolean('published'),
            'sort_order'   => (int) HomepageSection::max('sort_order') + 1,
        ]);

        return redirect()
            ->route('homepage-sections.index')
            ->with('success', "Section \"{$validated['name']}\" created successfully. Use Publish/Unpublish and drag & drop to manage it.");
    }

    /**
     * Generate a unique, non-reserved slug.
     */
    private function uniqueSlug(string $slug): string
    {
        $base = in_array($slug, self::RESERVED_SLUGS, true) ? $slug . '-section' : $slug;
        $candidate = $base;
        $suffix = 2;

        while (HomepageSection::where('slug', $candidate)->exists()) {
            $candidate = $base . '-' . $suffix++;
        }

        return $candidate;
    }

    /**
     * Toggle publish/unpublish a section.
     */
    public function togglePublish(HomepageSection $section)
    {
        $section->update(['is_published' => !$section->is_published]);

        return response()->json([
            'success' => true,
            'is_published' => $section->is_published,
            'message' => $section->is_published
                ? "\"{$section->name}\" is now published."
                : "\"{$section->name}\" is now unpublished.",
        ]);
    }

    /**
     * Update the sort order of a section.
     */
    public function updateOrder(Request $request, HomepageSection $section)
    {
        $request->validate([
            'sort_order' => 'required|integer|min:0',
        ]);

        $section->update(['sort_order' => $request->input('sort_order')]);

        return response()->json([
            'success' => true,
            'sort_order' => $section->sort_order,
        ]);
    }

    /**
     * Bulk reorder all sections.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'required|integer|exists:homepage_sections,id',
        ]);

        foreach ($request->input('order') as $index => $id) {
            HomepageSection::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}

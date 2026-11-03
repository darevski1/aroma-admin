<?php

namespace App\Http\Controllers;

use App\Models\Navigation;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NavigationController extends Controller
{
    private const SLUG_FORMAT = '/^(\/|[A-Za-z0-9][A-Za-z0-9\-_\/]*)$/';

    public function viewNavigation()
    {
        // Make sure every Blade page (including welcome) exists as a Page before choosing one.
        app(PageController::class)->syncManualBladePages();

        $pages = Page::orderBy('title')->get(['id', 'title', 'slug', 'route_name', 'published']);

        return view('admin_pages.navigation.index', [
            'pages' => $pages,
            'items' => $this->flatItems(),
        ]);
    }

    public function tree()
    {
        return response()->json(['items' => $this->flatItems()]);
    }

    /** Create one item; it is stored immediately and appended after its siblings. */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255', 'required_without:page_id'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:' . self::SLUG_FORMAT, Rule::unique('navigations', 'slug')],
            'page_id' => ['nullable', 'integer', $this->activePageRule()],
            'url' => ['nullable', 'string', 'max:2048', $this->urlRule()],
            'parent_id' => ['nullable', 'integer', 'exists:navigations,id'],
            'published' => ['nullable', 'boolean'],
        ]);

        $page = !empty($data['page_id']) ? Page::find($data['page_id']) : null;
        $title = trim((string) ($data['title'] ?? '')) ?: $page->title;

        $item = Navigation::create([
            'parent_id' => $data['parent_id'] ?? null,
            'title' => $title,
            'slug' => ($data['slug'] ?? null) ?: $this->uniqueSlug($page?->slug ?? $title),
            'page_id' => $page?->id,
            'url' => $page ? null : ($data['url'] ?? null),
            'published' => $data['published'] ?? true,
            'position' => ((int) Navigation::where('parent_id', $data['parent_id'] ?? null)->max('position')) + 1,
        ]);

        return response()->json(['message' => 'Navigation item created.', 'item' => $this->present($item->load('page'))], 201);
    }

    /** Persist the whole menu (structure, order and every field) in one transaction. */
    public function saveMenu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'items' => ['present', 'array'],
            'items.*.id' => ['required', 'integer', 'distinct', 'exists:navigations,id'],
            'items.*.parent_id' => ['nullable', 'integer'],
            'items.*.position' => ['required', 'integer', 'min:0'],
            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.slug' => ['required', 'string', 'max:255', 'regex:' . self::SLUG_FORMAT],
            'items.*.page_id' => ['nullable', 'integer', 'exists:pages,id,deleted_at,NULL'],
            'items.*.url' => ['nullable', 'string', 'max:2048', $this->urlRule()],
            'items.*.published' => ['required', 'boolean'],
            'deleted' => ['nullable', 'array'],
            'deleted.*' => ['integer'],
        ]);

        $validator->after(function ($validator) use ($request) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }
            foreach ($this->structuralErrors($request->input('items', []), $request->input('deleted', [])) as $key => $message) {
                $validator->errors()->add($key, $message);
            }
        });

        $data = $validator->validate();
        $deleted = array_map('intval', $data['deleted'] ?? []);

        DB::transaction(function () use ($data, $deleted) {
            $items = collect($data['items']);
            $current = Navigation::whereIn('id', $items->pluck('id'))->get()->keyBy('id');

            // Free the slugs first so two items can swap slugs without hitting the unique index.
            foreach ($items as $row) {
                if ($current[$row['id']]->slug !== $row['slug']) {
                    Navigation::whereKey($row['id'])->update(['slug' => '__tmp_' . $row['id']]);
                }
            }

            foreach ($items as $row) {
                $page = $row['page_id'] ?? null;
                $unchanged = $page && (int) $current[$row['id']]->page_id === (int) $page;
                if ($page && !$unchanged && !Page::where('published', true)->whereKey($page)->exists()) {
                    abort(422, 'Only active pages can be linked to navigation items.');
                }

                Navigation::whereKey($row['id'])->update([
                    'parent_id' => $row['parent_id'] ?? null,
                    'position' => $row['position'],
                    'title' => $row['title'],
                    'slug' => $row['slug'],
                    'page_id' => $page,
                    'url' => $page ? null : ($row['url'] ?? null),
                    'published' => $row['published'],
                    'updated_at' => now(),
                ]);
            }

            if ($deleted !== []) {
                Navigation::whereIn('id', $deleted)->delete();
            }
        });

        return response()->json(['message' => 'Menu saved.', 'items' => $this->flatItems()]);
    }

    /**
     * Cross-field checks: parents exist, no cycles, slugs unique across the whole table.
     *
     * @return array<string, string>
     */
    private function structuralErrors(array $items, array $deleted): array
    {
        $errors = [];
        $payload = collect($items)->keyBy('id');
        $deleted = array_map('intval', $deleted);

        // Final parent map: stored rows overlaid with the submitted ones.
        $parents = Navigation::pluck('parent_id', 'id')->all();
        foreach ($payload as $id => $row) {
            $parents[$id] = $row['parent_id'] ?? null;
        }
        foreach ($deleted as $id) {
            unset($parents[$id]);
        }

        foreach ($payload as $id => $row) {
            $parent = $row['parent_id'] ?? null;
            if ($parent !== null && !array_key_exists((int) $parent, $parents)) {
                $errors["items.$id.parent_id"] = "\"{$row['title']}\" has a parent that does not exist or is being deleted.";
                continue;
            }

            $seen = [];
            for ($node = $id; $node !== null; $node = $parents[$node] ?? null) {
                if (isset($seen[$node])) {
                    $errors["items.$id.parent_id"] = "\"{$row['title']}\" cannot be nested inside itself or one of its children.";
                    break;
                }
                $seen[$node] = true;
            }
        }

        $slugs = [];
        foreach ($payload as $id => $row) {
            $key = Str::lower($row['slug']);
            if (isset($slugs[$key])) {
                $errors["items.$id.slug"] = "The slug \"{$row['slug']}\" is used by more than one item.";
            }
            $slugs[$key] = true;
        }

        $taken = Navigation::whereNotIn('id', $payload->keys()->all())->pluck('slug')->map(fn ($slug) => Str::lower($slug))->flip();
        foreach ($payload as $id => $row) {
            if ($taken->has(Str::lower($row['slug']))) {
                $errors["items.$id.slug"] = "The slug \"{$row['slug']}\" is already used by another navigation item.";
            }
        }

        return $errors;
    }

    private function activePageRule()
    {
        return Rule::exists('pages', 'id')->where('published', true)->whereNull('deleted_at');
    }

    /** Accepts http(s) URLs, site-relative paths, anchors, mailto: and tel: links. */
    private function urlRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            if ($value === null || $value === '') {
                return;
            }
            $value = (string) $value;
            $valid = str_starts_with($value, '/') && !str_starts_with($value, '//')
                || str_starts_with($value, '#')
                || (preg_match('#^https?://#i', $value) && filter_var($value, FILTER_VALIDATE_URL))
                || preg_match('/^(mailto:[^\s@]+@[^\s@]+|tel:\+?[0-9 ()\-]+)$/i', $value);

            if (!$valid) {
                $fail('The URL must start with http://, https://, /, #, mailto: or tel:.');
            }
        };
    }

    private function uniqueSlug(string $source): string
    {
        $base = Str::slug($source, '-') ?: 'menu';
        $slug = $base;
        $suffix = 2;
        while (Navigation::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $suffix++;
        }

        return $slug;
    }

    private function flatItems()
    {
        return Navigation::with('page')->orderBy('position')->orderBy('id')->get()
            ->map(fn (Navigation $item) => $this->present($item))->values();
    }

    private function present(Navigation $item): array
    {
        return [
            'id' => $item->id,
            'parent_id' => $item->parent_id,
            'title' => $item->title,
            'slug' => $item->slug,
            'page_id' => $item->page_id,
            'page_title' => $item->page?->title,
            'page_active' => $item->page ? (bool) $item->page->published : null,
            'url' => $item->url,
            'published' => (bool) $item->published,
            'position' => (int) $item->position,
        ];
    }
}

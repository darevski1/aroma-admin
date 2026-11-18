<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteImageOverride;
use App\Models\SiteImageSlot;
use App\Services\SiteImages\MediaOptimizer;
use App\Services\SiteImages\SiteImages;
use App\Services\SiteImages\SiteScanner;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Admin → Site Images: every image shown on the public site, grouped by page, each
 * replaceable from the Media Library without touching templates.
 */
class SiteImageController extends Controller
{
    public const POSITIONS = ['center', 'top', 'bottom', 'left', 'right', 'left top', 'right top', 'left bottom', 'right bottom'];
    public const FITS = ['cover', 'contain', 'fill', 'none', 'scale-down'];

    public function index(Request $request)
    {
        $slots = SiteImageSlot::query()
            ->when($request->filled('path'), fn ($q) => $q->where('path', $request->input('path')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $q->where(fn ($w) => $w->where('src_key', 'like', $term)->orWhere('alt', 'like', $term)->orWhere('context', 'like', $term));
            })
            ->orderByRaw("path = '/' desc")->orderBy('path')->orderBy('id')
            ->get();

        $overrides = SiteImageOverride::with(['media', 'mobileMedia'])->get();
        $byPage = $overrides->where('scope_path', '!=', SiteImageOverride::GLOBAL)->keyBy(fn ($o) => $o->scope_path . "\0" . $o->src_key);
        $global = $overrides->where('scope_path', SiteImageOverride::GLOBAL)->keyBy('src_key');
        $usage = SiteImageSlot::query()->selectRaw('src_key, count(*) c')->groupBy('src_key')->pluck('c', 'src_key');

        $slots = $slots->map(function (SiteImageSlot $s) use ($byPage, $global, $usage) {
            $ovr = $byPage[$s->path . "\0" . $s->src_key] ?? $global[$s->src_key] ?? null;
            $s->override = $ovr;
            $s->shared_count = (int) ($usage[$s->src_key] ?? 1);
            $s->effective = $ovr && $ovr->media ? MediaOptimizer::publicUrl($ovr->media) : $s->src_key;

            return $s;
        });

        if ($request->input('state') === 'replaced') {
            $slots = $slots->filter(fn ($s) => $s->override);
        } elseif ($request->input('state') === 'original') {
            $slots = $slots->reject(fn ($s) => $s->override);
        } elseif ($request->input('state') === 'shared') {
            $slots = $slots->filter(fn ($s) => $s->shared_count > 1);
        }

        $pages = SiteImageSlot::query()->select('path', 'page_title')->distinct()->orderBy('path')->get()->unique('path');
        $stats = [
            'pages' => $pages->count(),
            'images' => SiteImageSlot::count(),
            'unique' => SiteImageSlot::distinct('src_key')->count('src_key'),
            'replaced' => $overrides->count(),
        ];

        return view('admin_pages.site-images.index', [
            'groups' => $slots->groupBy('path'),
            'pages' => $pages,
            'stats' => $stats,
            'positions' => self::POSITIONS,
            'fits' => self::FITS,
        ]);
    }

    public function scan(SiteScanner $scanner)
    {
        $result = $scanner->run();
        $failed = collect($result)->reject(fn ($r) => is_int($r));
        SiteImages::bump();

        return redirect()->route('site-images.index')->with(
            $failed->isEmpty() ? 'success' : 'error',
            count($result) . ' pages scanned, ' . array_sum(array_filter($result, 'is_int')) . ' image slots found.'
            . ($failed->isNotEmpty() ? ' Could not read: ' . $failed->map(fn ($m, $p) => "$p ($m)")->implode(', ') : '')
        );
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', Rule::in(['page', 'global'])],
            'path' => ['required', 'string', 'max:255', 'exists:site_image_slots,path'],
            'src_key' => ['required', 'string', 'max:500', 'exists:site_image_slots,src_key'],
            'media_id' => ['nullable', 'integer', 'exists:media,id'],
            'mobile_media_id' => ['nullable', 'integer', 'exists:media,id'],
            'alt' => ['nullable', 'string', 'max:500'],
            'title' => ['nullable', 'string', 'max:255'],
            'object_position' => ['nullable', Rule::in(self::POSITIONS)],
            'object_fit' => ['nullable', Rule::in(self::FITS)],
            'hidden' => ['nullable', 'boolean'],
        ]);

        foreach (['media_id', 'mobile_media_id'] as $field) {
            if (!empty($data[$field])) {
                $mime = (string) Media::find($data[$field])?->mime_type;
                abort_unless(in_array($mime, MediaOptimizer::ALLOWED, true), 422, 'The selected file is not an image.');
            }
        }

        $scope = $data['scope'] === 'global' ? SiteImageOverride::GLOBAL : $data['path'];
        $override = SiteImageOverride::updateOrCreate(
            ['scope_path' => $scope, 'src_key' => $data['src_key']],
            [
                'media_id' => $data['media_id'] ?? null,
                'mobile_media_id' => $data['mobile_media_id'] ?? null,
                'alt' => $data['alt'] ?? null,
                'title' => $data['title'] ?? null,
                'object_position' => $data['object_position'] ?? null,
                'object_fit' => $data['object_fit'] ?? null,
                'hidden' => (bool) ($data['hidden'] ?? false),
            ]
        );

        // A page-specific override wins over a global one; switching scope must not leave a stale twin behind.
        if ($scope === SiteImageOverride::GLOBAL) {
            SiteImageOverride::where('src_key', $data['src_key'])->where('scope_path', $data['path'])->delete();
        }
        SiteImages::bump();

        return response()->json(['success' => true, 'message' => 'Image saved. It is live on the website now.', 'id' => $override->id]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'scope' => ['required', Rule::in(['page', 'global'])],
            'path' => ['required', 'string', 'max:255'],
            'src_key' => ['required', 'string', 'max:500'],
        ]);
        SiteImageOverride::where('scope_path', $data['scope'] === 'global' ? SiteImageOverride::GLOBAL : $data['path'])
            ->where('src_key', $data['src_key'])->delete();
        SiteImages::bump();

        return response()->json(['success' => true, 'message' => 'The original image is back.']);
    }
}

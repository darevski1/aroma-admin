<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use App\Models\HeroSlider;
use App\Services\HeroSlider\SlideImageStore;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/** Admin → Hero Sliders → slides of one slider. */
class HeroSlideController extends Controller
{
    /** Accepted upload types; sizes are in KB. */
    private const IMAGE_RULES = ['image', 'mimes:jpg,jpeg,png,webp,avif'];
    private const DESKTOP_MAX_KB = 8192;
    private const MOBILE_MAX_KB = 5120;

    public function __construct(private SlideImageStore $images)
    {
    }

    public function create(HeroSlider $heroSlider)
    {
        return view('admin_pages.hero-sliders.slide-form', [
            'slider' => $heroSlider,
            'slide' => new HeroSlide([
                'is_active' => true, 'image_position' => 'center center',
                'text_alignment' => 'left', 'overlay_opacity' => 45,
            ]),
        ]);
    }

    public function store(Request $request, HeroSlider $heroSlider)
    {
        $data = $this->validated($request, null);
        $data['position'] = ($heroSlider->slides()->max('position') ?? 0) + 1;

        $heroSlider->slides()->create($data);

        return redirect()->route('hero-sliders.edit', $heroSlider)->with('success', 'Slide added.');
    }

    public function edit(HeroSlider $heroSlider, HeroSlide $slide)
    {
        return view('admin_pages.hero-sliders.slide-form', ['slider' => $heroSlider, 'slide' => $slide]);
    }

    public function update(Request $request, HeroSlider $heroSlider, HeroSlide $slide)
    {
        $slide->update($this->validated($request, $slide));

        return redirect()->route('hero-sliders.edit', $heroSlider)->with('success', 'Slide updated.');
    }

    public function destroy(HeroSlider $heroSlider, HeroSlide $slide)
    {
        $this->images->delete($slide->desktop_image);
        $this->images->delete($slide->mobile_image);
        $slide->delete();

        return redirect()->route('hero-sliders.edit', $heroSlider)->with('success', 'Slide deleted.');
    }

    public function toggle(HeroSlider $heroSlider, HeroSlide $slide)
    {
        $slide->update(['is_active' => ! $slide->is_active]);

        return response()->json(['success' => true, 'is_active' => $slide->is_active]);
    }

    public function duplicate(HeroSlider $heroSlider, HeroSlide $slide)
    {
        // Make room right after the original.
        $heroSlider->slides()->where('position', '>', $slide->position)->increment('position');

        $copy = $slide->replicate();
        $copy->position = $slide->position + 1;
        $copy->title = $slide->title ? $slide->title . ' (copy)' : null;
        $copy->desktop_image = $this->images->copy($slide->desktop_image);
        $copy->mobile_image = $this->images->copy($slide->mobile_image);
        $copy->save();

        return redirect()->route('hero-sliders.edit', $heroSlider)->with('success', 'Slide duplicated.');
    }

    public function reorder(Request $request, HeroSlider $heroSlider)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => ['integer', Rule::exists('hero_slides', 'id')->where('hero_slider_id', $heroSlider->id)],
        ]);

        foreach (array_values($data['order']) as $i => $id) {
            HeroSlide::whereKey($id)->update(['position' => $i + 1]);
        }

        return response()->json(['success' => true]);
    }

    private function validated(Request $request, ?HeroSlide $slide): array
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'button_text' => 'nullable|required_with:button_url|string|max:100',
            // Absolute http(s) links, site paths, #anchors, mailto: and tel: only.
            'button_url' => ['nullable', 'required_with:button_text', 'string', 'max:2048', 'regex:~^(https?://|/|#|mailto:|tel:)~i'],
            'image_alt' => 'nullable|string|max:255',
            'image_position' => ['required', Rule::in(array_keys(HeroSlide::IMAGE_POSITIONS))],
            'text_alignment' => ['required', Rule::in(array_keys(HeroSlide::TEXT_ALIGNMENTS))],
            'overlay_opacity' => 'required|integer|min:0|max:90',
            'desktop_image' => [$slide?->desktop_image ? 'nullable' : 'required', ...self::IMAGE_RULES, 'max:' . self::DESKTOP_MAX_KB, 'dimensions:min_width=800,min_height=300'],
            'mobile_image' => ['nullable', ...self::IMAGE_RULES, 'max:' . self::MOBILE_MAX_KB, 'dimensions:min_width=320,min_height=320'],
        ], [
            'button_url.regex' => 'The button URL must start with http://, https://, /, #, mailto: or tel:.',
            'desktop_image.max' => 'The desktop image may not be larger than 8 MB.',
            'mobile_image.max' => 'The mobile image may not be larger than 5 MB.',
            'desktop_image.dimensions' => 'The desktop image must be at least 800×300 px.',
            'mobile_image.dimensions' => 'The mobile image must be at least 320×320 px.',
        ]);

        foreach (['is_active', 'button_new_tab', 'link_whole_slide'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }
        // A whole-slide link needs a destination.
        if (empty($data['button_url'])) {
            $data['link_whole_slide'] = false;
        }

        unset($data['desktop_image'], $data['mobile_image']);

        if ($request->hasFile('desktop_image')) {
            $stored = $this->images->store($request->file('desktop_image'), 'desktop');
            $this->images->delete($slide?->desktop_image);
            $data += ['desktop_image' => $stored['path'], 'desktop_width' => $stored['width'], 'desktop_height' => $stored['height']];
        }

        if ($request->hasFile('mobile_image')) {
            $stored = $this->images->store($request->file('mobile_image'), 'mobile');
            $this->images->delete($slide?->mobile_image);
            $data += ['mobile_image' => $stored['path'], 'mobile_width' => $stored['width'], 'mobile_height' => $stored['height']];
        } elseif ($slide && $request->boolean('remove_mobile_image')) {
            $this->images->delete($slide->mobile_image);
            $data += ['mobile_image' => null, 'mobile_width' => null, 'mobile_height' => null];
        }

        return $data;
    }
}

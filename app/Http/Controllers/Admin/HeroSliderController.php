<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use App\Services\HeroSlider\SlideImageStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/** Admin → Hero Sliders: the sliders themselves (slides: {@see HeroSlideController}). */
class HeroSliderController extends Controller
{
    public function index()
    {
        $sliders = HeroSlider::withCount([
            'slides',
            'slides as active_slides_count' => fn ($q) => $q->where('is_active', true),
        ])->latest('id')->get();

        return view('admin_pages.hero-sliders.index', compact('sliders'));
    }

    public function create()
    {
        return view('admin_pages.hero-sliders.form', ['slider' => new HeroSlider([
            'is_active' => true, 'autoplay' => true, 'interval_ms' => 6000, 'transition' => 'fade',
            'transition_ms' => 700, 'show_arrows' => true, 'show_dots' => true, 'pause_on_hover' => true,
            'full_width' => true, 'height_desktop' => 80, 'height_mobile' => 70,
        ])]);
    }

    public function store(Request $request)
    {
        $slider = HeroSlider::create($this->validated($request));

        return redirect()->route('hero-sliders.edit', $slider)
            ->with('success', "Slider created. Its shortcode is {$slider->tag} — now add slides.");
    }

    public function edit(HeroSlider $heroSlider)
    {
        $heroSlider->load('slides');

        return view('admin_pages.hero-sliders.form', ['slider' => $heroSlider]);
    }

    public function update(Request $request, HeroSlider $heroSlider)
    {
        $heroSlider->update($this->validated($request));

        return redirect()->route('hero-sliders.edit', $heroSlider)->with('success', 'Slider settings saved.');
    }

    public function destroy(HeroSlider $heroSlider, SlideImageStore $images)
    {
        foreach ($heroSlider->slides as $slide) {
            $images->delete($slide->desktop_image);
            $images->delete($slide->mobile_image);
        }
        $heroSlider->delete();

        return redirect()->route('hero-sliders.index')->with('success', 'Slider deleted.');
    }

    public function toggle(HeroSlider $heroSlider)
    {
        $heroSlider->update(['is_active' => ! $heroSlider->is_active]);

        return response()->json(['success' => true, 'is_active' => $heroSlider->is_active]);
    }

    /** Copy a slider with all its slides (and image files) under a new shortcode. */
    public function duplicate(HeroSlider $heroSlider, SlideImageStore $images)
    {
        $copy = DB::transaction(function () use ($heroSlider, $images) {
            $copy = $heroSlider->replicate(['shortcode']);
            $copy->name = $heroSlider->name . ' (copy)';
            $copy->is_active = false;
            $copy->save();

            foreach ($heroSlider->slides as $slide) {
                $new = $slide->replicate();
                $new->hero_slider_id = $copy->id;
                $new->desktop_image = $images->copy($slide->desktop_image);
                $new->mobile_image = $images->copy($slide->mobile_image);
                $new->save();
            }

            return $copy;
        });

        return redirect()->route('hero-sliders.edit', $copy)
            ->with('success', "Slider duplicated as {$copy->tag}. The copy starts disabled.");
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'page_id' => 'nullable|integer|exists:pages,id',
            'placement' => ['sometimes', Rule::in(array_keys(HeroSlider::PLACEMENTS))],
            'interval_ms' => 'required|integer|min:1500|max:60000',
            'transition' => ['required', Rule::in(array_keys(HeroSlider::TRANSITIONS))],
            'transition_ms' => 'required|integer|min:100|max:3000',
            'height_desktop' => 'required|integer|min:20|max:100',
            'height_mobile' => 'required|integer|min:20|max:100',
        ]);

        foreach (['is_active', 'autoplay', 'show_arrows', 'show_dots', 'pause_on_hover', 'full_width'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        return $data;
    }
}

<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\HeroSlider;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Admin → Hero Sliders and the [YYYY-N] shortcode on public pages. */
class HeroSlidersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function admin(): User
    {
        $user = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $user->email_verified_at = now();

        return $user;
    }

    private function slider(array $attrs = []): HeroSlider
    {
        return HeroSlider::create($attrs + ['name' => 'Models hero', 'is_active' => true]);
    }

    private function slide(HeroSlider $slider, array $attrs = []): HeroSlide
    {
        return $slider->slides()->create($attrs + [
            'title' => 'Slide title', 'desktop_image' => 'hero-sliders/a.webp', 'is_active' => true, 'position' => 1,
        ]);
    }

    /** Put the shortcode on /modeli through the visual editor's stored text edits. */
    private function placeOnModelsPage(string $tag, string $layout = 'pages.cennobots.models.index', string $text = 'Палета'): void
    {
        Page::updateOrCreate(['layout' => $layout], [
            'title' => 'Модели', 'slug' => 'modeli-' . md5($layout), 'published' => true,
            'settings' => ['inline_edits' => [['k' => $text, 'n' => 0, 'html' => e($tag)]]],
        ]);
    }

    public function test_slider_gets_a_unique_shortcode(): void
    {
        $a = $this->slider();
        $b = $this->slider(['name' => 'Other']);

        $this->assertSame(now()->format('Y') . '-' . $a->id, $a->shortcode);
        $this->assertSame('[' . $a->shortcode . ']', $a->tag);
        $this->assertNotSame($a->shortcode, $b->shortcode);
    }

    public function test_admin_creates_slider_and_uploads_desktop_and_mobile_images(): void
    {
        $this->actingAs($this->admin())
            ->post(route('hero-sliders.store'), [
                'name' => 'Hero', 'is_active' => '1', 'autoplay' => '1', 'interval_ms' => 5000, 'transition' => 'slide',
                'transition_ms' => 600, 'show_arrows' => '1', 'show_dots' => '1', 'height_desktop' => 75, 'height_mobile' => 65,
            ])->assertRedirect();

        $slider = HeroSlider::firstOrFail();
        $this->assertSame('slide', $slider->transition);

        $this->actingAs($this->admin())->get(route('hero-sliders.edit', $slider))
            ->assertOk()->assertSee($slider->tag)->assertSee('hs-copy', false);

        $this->actingAs($this->admin())
            ->post(route('hero-sliders.slides.store', $slider), [
                'title' => 'Big title', 'subtitle' => 'Eyebrow', 'description' => 'Text',
                'button_text' => 'Contact', 'button_url' => '/kontakt',
                'image_position' => 'center top', 'text_alignment' => 'center', 'overlay_opacity' => 40, 'is_active' => '1',
                'desktop_image' => UploadedFile::fake()->image('desk.jpg', 3000, 1200),
                'mobile_image' => UploadedFile::fake()->image('mob.png', 900, 1200),
            ])->assertRedirect(route('hero-sliders.edit', $slider))->assertSessionHasNoErrors();

        $slide = $slider->slides()->firstOrFail();
        Storage::disk('public')->assertExists($slide->desktop_image);
        Storage::disk('public')->assertExists($slide->mobile_image);
        $this->assertSame(2400, $slide->desktop_width, 'large desktop images are scaled down');
        $this->assertStringEndsWith('.webp', $slide->desktop_image);
    }

    public function test_uploads_are_validated(): void
    {
        $slider = $this->slider();
        $base = ['image_position' => 'center center', 'text_alignment' => 'left', 'overlay_opacity' => 40];

        $this->actingAs($this->admin())
            ->post(route('hero-sliders.slides.store', $slider), $base + [
                'desktop_image' => UploadedFile::fake()->create('evil.pdf', 100, 'application/pdf'),
            ])->assertSessionHasErrors('desktop_image');

        $this->actingAs($this->admin())
            ->post(route('hero-sliders.slides.store', $slider), $base + [
                'desktop_image' => UploadedFile::fake()->image('big.jpg', 2000, 1000)->size(9000),
            ])->assertSessionHasErrors('desktop_image');

        $this->actingAs($this->admin())
            ->post(route('hero-sliders.slides.store', $slider), $base + [
                'desktop_image' => UploadedFile::fake()->image('ok.jpg', 1600, 800),
                'button_text' => 'Go', 'button_url' => 'javascript:alert(1)',
            ])->assertSessionHasErrors('button_url');

        $this->assertSame(0, $slider->slides()->count());
    }

    public function test_slides_can_be_reordered_toggled_duplicated_and_deleted(): void
    {
        $slider = $this->slider();
        $a = $this->slide($slider, ['title' => 'A', 'position' => 1]);
        $b = $this->slide($slider, ['title' => 'B', 'position' => 2]);
        $admin = $this->admin();

        $this->actingAs($admin)->postJson(route('hero-sliders.slides.reorder', $slider), ['order' => [$b->id, $a->id]])->assertOk();
        $this->assertSame(['B', 'A'], $slider->slides()->pluck('title')->all());

        $this->actingAs($admin)->patchJson(route('hero-sliders.slides.toggle', [$slider, $a]))->assertJson(['is_active' => false]);

        $this->actingAs($admin)->post(route('hero-sliders.slides.duplicate', [$slider, $b]))->assertRedirect();
        $this->assertSame(['B', 'B (copy)', 'A'], $slider->slides()->pluck('title')->all());

        $this->actingAs($admin)->delete(route('hero-sliders.slides.destroy', [$slider, $a]))->assertRedirect();
        $this->assertSame(2, $slider->slides()->count());

        // A slide of another slider cannot be reached through this slider's URL.
        $other = $this->slider(['name' => 'Other']);
        $this->actingAs($admin)->get(route('hero-sliders.slides.edit', [$other, $b]))->assertNotFound();
    }

    public function test_duplicating_a_slider_copies_slides_under_a_new_disabled_shortcode(): void
    {
        $slider = $this->slider();
        $this->slide($slider);

        $this->actingAs($this->admin())->post(route('hero-sliders.duplicate', $slider))->assertRedirect();

        $copy = HeroSlider::latest('id')->first();
        $this->assertNotSame($slider->shortcode, $copy->shortcode);
        $this->assertFalse($copy->is_active);
        $this->assertSame(1, $copy->slides()->count());
    }

    public function test_shortcode_renders_the_same_slider_on_models_page_and_model_page(): void
    {
        $slider = $this->slider();
        $this->slide($slider, ['title' => 'Prvi slajd', 'button_text' => 'Kontakt', 'button_url' => '/kontakt', 'position' => 1]);
        $this->slide($slider, ['title' => 'Vtor slajd', 'position' => 2, 'mobile_image' => 'hero-sliders/m.webp']);

        $this->placeOnModelsPage($slider->tag);
        $model = collect(config('cenobots.models'))->first();
        $this->placeOnModelsPage($slider->tag, 'pages.cennobots.models.show', 'Карактеристики');

        foreach (['/modeli', '/modeli/' . $model['id']] as $url) {
            $html = $this->get($url)->assertOk()
                ->assertSee('data-hero-slider', false)
                ->assertSeeInOrder(['Prvi slajd', 'Vtor slajd'])
                ->assertSee('hero-slider.css', false)
                ->assertSee('hero-slider.js', false)
                ->assertSee('media="(max-width: 767px)"', false)
                ->assertDontSee($slider->tag, false)
                ->getContent();

            $this->assertSame(1, substr_count($html, 'hero-slider.js'), 'assets are included once');
            $this->assertStringNotContainsString('<p class="eyebrow"><section', $html, 'the wrapping paragraph is replaced');
        }
    }

    public function test_disabled_slider_and_slides_are_not_shown(): void
    {
        $slider = $this->slider();
        $this->slide($slider, ['title' => 'Visible one']);
        $this->slide($slider, ['title' => 'Hidden one', 'is_active' => false, 'position' => 2]);
        $this->placeOnModelsPage($slider->tag);

        $this->get('/modeli')->assertSee('Visible one')->assertDontSee('Hidden one');

        $slider->update(['is_active' => false]);
        $this->get('/modeli')->assertOk()
            ->assertDontSee('data-hero-slider', false)
            ->assertDontSee('Visible one')
            ->assertDontSee($slider->tag, false);

        $slider->update(['is_active' => true]);
        $this->get('/modeli')->assertSee('Visible one');
    }

    public function test_shortcodes_are_left_alone_in_admin_editor_and_unknown_codes(): void
    {
        $slider = $this->slider();
        $this->slide($slider);

        // Admin screens keep the code visible for copying.
        $this->actingAs($this->admin())->get(route('hero-sliders.index'))
            ->assertSee($slider->tag, false)->assertDontSee('data-hero-slider', false);

        // The visual editor shows the raw code to admins only.
        $this->app['auth']->forgetGuards();
        $this->placeOnModelsPage($slider->tag);
        $this->get('/modeli?editor=1')->assertSee('data-hero-slider', false);

        // An unknown code in page text stays as typed.
        $this->placeOnModelsPage('[1999-777]');
        $this->get('/modeli')->assertSee('[1999-777]', false);
    }

    public function test_blade_component_and_directive_render_the_slider(): void
    {
        $slider = $this->slider();
        $this->slide($slider, ['title' => 'From component']);

        $html = \Illuminate\Support\Facades\Blade::render('<x-hero-slider code="' . $slider->tag . '" />');
        $this->assertStringContainsString('From component', $html);

        $this->assertSame('', trim(\Illuminate\Support\Facades\Blade::render("@heroSlider('1999-1')")));
    }
}

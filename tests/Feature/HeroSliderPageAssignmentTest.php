<?php

namespace Tests\Feature;

use App\Models\HeroSlide;
use App\Models\HeroSlider;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroSliderPageAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function slider(?Page $page, string $placement = 'top', bool $slide = true): HeroSlider
    {
        $slider = HeroSlider::create(['name' => 'T', 'page_id' => $page?->id, 'placement' => $placement]);
        if ($slide) {
            HeroSlide::create(['hero_slider_id' => $slider->id, 'title' => 'Assigned hero', 'desktop_image' => 'x.jpg', 'is_active' => true]);
        }

        return $slider;
    }

    public function test_assigned_slider_shows_only_on_its_page(): void
    {
        $kontakt = Page::create(['title' => 'K', 'slug' => 'kontakt', 'layout' => 'pages.kontakt.index', 'route_name' => 'kontakt', 'published' => true]);
        $this->slider($kontakt);

        $this->get('/kontakt')->assertOk()->assertSee('Assigned hero', false);
        $this->get('/uslugi')->assertOk()->assertDontSee('Assigned hero', false);
    }

    public function test_welcome_page_assignment_and_inactive_slider(): void
    {
        $welcome = Page::create(['title' => 'W', 'slug' => 'welcome-page', 'layout' => 'welcome', 'published' => true]);
        $slider = $this->slider($welcome, 'bottom');

        $this->get('/')->assertOk()->assertSee('Assigned hero', false);

        $slider->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertDontSee('Assigned hero', false);
    }

    public function test_admin_can_assign_a_page_from_the_form(): void
    {
        $this->actingAs(new User(['name' => 'A', 'email' => 'a@example.test', 'user_role' => 2]));
        $page = Page::create(['title' => 'K', 'slug' => 'kontakt', 'layout' => 'pages.kontakt.index', 'published' => true]);
        $slider = $this->slider(null);

        $this->get(route('hero-sliders.edit', $slider))->assertOk()->assertSee('Show on page');
        $this->put(route('hero-sliders.update', $slider), [
            'name' => 'T', 'page_id' => $page->id, 'placement' => 'bottom', 'interval_ms' => 6000,
            'transition' => 'fade', 'transition_ms' => 700, 'height_desktop' => 80, 'height_mobile' => 70,
        ])->assertRedirect();

        $this->assertSame($page->id, $slider->fresh()->page_id);
        $this->assertSame('bottom', $slider->fresh()->placement);
    }
}

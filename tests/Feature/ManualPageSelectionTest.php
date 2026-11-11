<?php

namespace Tests\Feature;

use App\Models\Navigation;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualPageSelectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]));
        $this->get(route('pages.create'))->assertOk();
    }

    private function entry(string $view): array
    {
        return collect($this->getJson(route('pages.manual-blades'))->json('pages'))->firstWhere('view', $view);
    }

    public function test_remove_all_clears_every_blade_page_and_they_stay_removed(): void
    {
        $custom = Page::factory()->create(['layout' => 'pages.show', 'slug' => 'custom-form-page']);
        $this->assertGreaterThan(1, Page::count());

        $this->post(route('pages.remove-all-existing'))->assertRedirect(route('pages.create'));

        $this->assertSame([$custom->id], Page::pluck('id')->all());

        // None of the screens that auto-register Blade pages may bring them back.
        $this->get(route('pages.create'))->assertOk();
        $this->get(route('pages.index'))->assertOk();
        $this->assertSame([$custom->id], Page::pluck('id')->all());

        $pages = collect($this->getJson(route('pages.manual-blades'))->json('pages'));
        $this->assertTrue($pages->contains('view', 'welcome'));
        $this->assertFalse($pages->contains('added', true));
    }

    public function test_only_the_chosen_page_returns_after_remove_all(): void
    {
        $this->post(route('pages.remove-all-existing'));
        $welcome = $this->entry('welcome');

        $this->post(route('pages.add-existing'), [
            'view' => 'welcome',
            'title' => $welcome['page_title'],
            'slug' => $welcome['page_slug'],
            'route_name' => $welcome['page_route'],
            'published' => '1',
        ])->assertRedirect(route('pages.index'));

        $this->get(route('pages.index'))->assertOk();

        $this->assertSame(['welcome'], Page::pluck('layout')->all());
        $this->assertSame('welcome', Page::first()->route_name);
        $this->assertSame(1, Page::withTrashed()->where('layout', 'welcome')->count());
        $this->assertTrue($this->entry('welcome')['added']);
    }

    public function test_several_selected_pages_are_added_together(): void
    {
        $this->post(route('pages.remove-all-existing'));

        $this->post(route('pages.add-existing-selected'), [
            'views' => ['welcome', 'pages.kontakt.index'],
        ])->assertRedirect(route('pages.index'));

        $this->assertEqualsCanonicalizing(['welcome', 'pages.kontakt.index'], Page::pluck('layout')->all());
    }

    public function test_removing_one_page_leaves_the_others_and_unlinks_its_navigation(): void
    {
        $page = Page::where('layout', 'welcome')->firstOrFail();
        $navigation = Navigation::create(['title' => 'Home', 'slug' => 'home', 'page_id' => $page->id, 'published' => true, 'position' => 0]);
        $others = Page::count() - 1;

        $this->post(route('pages.remove-existing'), ['view' => 'welcome'])->assertRedirect(route('pages.create'));

        $this->assertSoftDeleted($page);
        $this->assertSame($others, Page::count());
        $this->assertNull($navigation->fresh()->page_id);
        $this->assertFalse($this->entry('welcome')['added']);
    }

    public function test_homepage_keeps_rendering_after_its_page_is_removed(): void
    {
        $this->post(route('pages.remove-existing'), ['view' => 'welcome']);

        $this->get('/')->assertOk();
    }

    public function test_public_routes_keep_rendering_after_remove_all(): void
    {
        $this->post(route('pages.remove-all-existing'));

        $uris = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->filter(fn ($route) => in_array('GET', $route->methods(), true)
                && in_array('page.active', $route->gatherMiddleware(), true)
                && !str_contains($route->uri(), '{'))
            ->map(fn ($route) => '/' . ltrim($route->uri(), '/'));

        $this->assertNotEmpty($uris);

        foreach ($uris as $uri) {
            $this->assertLessThan(500, $this->get($uri)->getStatusCode(), "{$uri} failed after remove all");
        }
    }

    public function test_a_page_that_is_not_added_cannot_be_removed(): void
    {
        $this->post(route('pages.remove-existing'), ['view' => 'welcome']);

        $this->post(route('pages.remove-existing'), ['view' => 'welcome'])->assertSessionHas('error');
        $this->post(route('pages.remove-existing'), ['view' => 'pages.does-not-exist'])->assertSessionHas('error');
    }
}

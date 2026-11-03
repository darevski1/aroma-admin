<?php

namespace Tests\Feature;

use App\Livewire\Navbar;
use App\Models\Navigation;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNavigationTest extends TestCase
{
    use RefreshDatabase;

    private function actAsNavigationAdmin(): void
    {
        $this->actingAs(new User(['name' => 'Admin', 'email' => 'navigation-admin@example.test', 'user_role' => 2]));
    }

    private function item(array $attributes = []): Navigation
    {
        static $n = 0;
        $n++;

        return Navigation::create($attributes + ['title' => "Item $n", 'slug' => "item-$n", 'published' => true, 'position' => 0]);
    }

    private function payload(Navigation $item, array $overrides = []): array
    {
        return $overrides + [
            'id' => $item->id, 'parent_id' => $item->parent_id, 'position' => $item->position,
            'title' => $item->title, 'slug' => $item->slug, 'page_id' => $item->page_id,
            'url' => $item->url, 'published' => $item->published,
        ];
    }

    // ── Frontend ──────────────────────────────────────────────

    public function test_frontend_renders_active_items_in_order_with_nested_dropdowns(): void
    {
        $page = Page::factory()->create(['slug' => 'about-custom', 'published' => true]);
        $company = $this->item(['title' => 'Zeta menu', 'slug' => 'company', 'position' => 2, 'page_id' => $page->id]);
        $this->item(['title' => 'Earlier', 'slug' => 'earlier', 'position' => 1]);
        $child = $this->item(['title' => 'Only child', 'slug' => 'only-child', 'parent_id' => $company->id]);
        $grand = $this->item(['title' => 'Grandchild', 'slug' => 'grandchild', 'parent_id' => $child->id]);
        $this->item(['title' => 'Great grandchild', 'slug' => 'great', 'parent_id' => $grand->id]);
        $this->item(['title' => 'Hidden child', 'slug' => 'hidden-child', 'parent_id' => $company->id, 'published' => false]);
        $this->item(['title' => 'Hidden root', 'slug' => 'hidden-root', 'published' => false]);

        $html = app(Navbar::class)->render()->render();

        $this->assertStringContainsString('href="' . url('pages/about-custom') . '"', $html);
        foreach (['Only child', 'Grandchild', 'Great grandchild'] as $title) {
            $this->assertStringContainsString($title, $html);
        }
        $this->assertStringNotContainsString('Hidden child', $html);
        $this->assertStringNotContainsString('Hidden root', $html);
        $this->assertLessThan(strpos($html, 'Zeta menu'), strpos($html, 'Earlier'));
    }

    public function test_children_of_inactive_or_page_inactive_items_are_hidden(): void
    {
        $draft = Page::factory()->create(['published' => false]);
        $linked = $this->item(['title' => 'Draft link', 'page_id' => $draft->id]);
        $this->item(['title' => 'Under draft', 'parent_id' => $linked->id]);
        $off = $this->item(['title' => 'Switched off', 'published' => false]);
        $this->item(['title' => 'Under off', 'parent_id' => $off->id]);

        $html = app(Navbar::class)->render()->render();

        foreach (['Draft link', 'Under draft', 'Switched off', 'Under off'] as $title) {
            $this->assertStringNotContainsString($title, $html);
        }
    }

    public function test_inactive_page_routes_return_404_and_active_ones_render(): void
    {
        Page::factory()->create(['layout' => 'welcome', 'slug' => 'welcome-page', 'published' => false]);
        Page::factory()->create(['layout' => 'pages.about.index', 'route_name' => 'za-nas', 'slug' => 'about', 'published' => false]);

        $this->get('/')->assertNotFound();
        $this->get('/za-nas')->assertNotFound();

        Page::where('layout', 'welcome')->update(['published' => true]);
        $this->get('/')->assertOk();
    }

    // ── Creating items ────────────────────────────────────────

    public function test_admin_creates_items_with_page_or_custom_url_and_nesting(): void
    {
        $this->actAsNavigationAdmin();
        $page = Page::factory()->create(['title' => 'Active page', 'slug' => 'active-page', 'published' => true]);

        $parent = $this->postJson(route('navigation.store'), ['page_id' => $page->id])->assertCreated()->json('item');
        $this->assertSame('Active page', $parent['title']);
        $this->assertSame('active-page', $parent['slug']);

        $child = $this->postJson(route('navigation.store'), ['title' => 'Docs', 'url' => 'https://example.test/docs', 'parent_id' => $parent['id']])
            ->assertCreated()->json('item');
        $this->assertSame($parent['id'], $child['parent_id']);
        $this->assertDatabaseHas('navigations', ['id' => $child['id'], 'url' => 'https://example.test/docs', 'page_id' => null]);
    }

    public function test_store_validates_slug_url_and_inactive_pages(): void
    {
        $this->actAsNavigationAdmin();
        $draft = Page::factory()->create(['published' => false]);
        $this->item(['slug' => 'taken']);

        $this->postJson(route('navigation.store'), ['title' => 'A', 'slug' => 'taken'])->assertJsonValidationErrors('slug');
        $this->postJson(route('navigation.store'), ['title' => 'A', 'url' => 'javascript:alert(1)'])->assertJsonValidationErrors('url');
        $this->postJson(route('navigation.store'), ['title' => 'A', 'url' => 'not a url'])->assertJsonValidationErrors('url');
        $this->postJson(route('navigation.store'), ['page_id' => $draft->id])->assertJsonValidationErrors('page_id');
        $this->postJson(route('navigation.store'), [])->assertJsonValidationErrors('title');

        foreach (['/contact', '#top', 'mailto:a@b.test', 'tel:+38970123456', 'http://example.test/x'] as $url) {
            $this->postJson(route('navigation.store'), ['title' => "ok $url", 'url' => $url])->assertCreated();
        }
    }

    // ── Save Menu ─────────────────────────────────────────────

    public function test_save_menu_persists_nesting_order_fields_and_deletes(): void
    {
        $this->actAsNavigationAdmin();
        $page = Page::factory()->create(['published' => true]);
        $a = $this->item(['position' => 0]);
        $b = $this->item(['position' => 1]);
        $c = $this->item(['position' => 2, 'parent_id' => $a->id]);
        $gone = $this->item(['position' => 3]);

        $this->putJson(route('navigation.save'), [
            'items' => [
                $this->payload($b, ['position' => 0, 'title' => 'B renamed', 'published' => false]),
                $this->payload($a, ['position' => 1, 'page_id' => $page->id, 'url' => null]),
                $this->payload($c, ['parent_id' => $b->id, 'position' => 0, 'slug' => 'c-new']),
            ],
            'deleted' => [$gone->id],
        ])->assertOk();

        $this->assertDatabaseHas('navigations', ['id' => $b->id, 'title' => 'B renamed', 'published' => 0, 'position' => 0, 'parent_id' => null]);
        $this->assertDatabaseHas('navigations', ['id' => $a->id, 'position' => 1, 'page_id' => $page->id]);
        $this->assertDatabaseHas('navigations', ['id' => $c->id, 'parent_id' => $b->id, 'slug' => 'c-new']);
        $this->assertDatabaseMissing('navigations', ['id' => $gone->id]);
    }

    public function test_save_menu_allows_swapping_slugs_but_rejects_duplicates(): void
    {
        $this->actAsNavigationAdmin();
        $a = $this->item(['slug' => 'one']);
        $b = $this->item(['slug' => 'two']);

        $this->putJson(route('navigation.save'), ['items' => [
            $this->payload($a, ['slug' => 'two']), $this->payload($b, ['slug' => 'one']),
        ]])->assertOk();
        $this->assertDatabaseHas('navigations', ['id' => $a->id, 'slug' => 'two']);

        $this->putJson(route('navigation.save'), ['items' => [
            $this->payload($a, ['slug' => 'same']), $this->payload($b, ['slug' => 'same']),
        ]])->assertUnprocessable();
    }

    public function test_save_menu_rejects_cycles_bad_urls_and_unknown_inactive_pages(): void
    {
        $this->actAsNavigationAdmin();
        $draft = Page::factory()->create(['published' => false]);
        $a = $this->item();
        $b = $this->item(['parent_id' => $a->id]);

        $this->putJson(route('navigation.save'), ['items' => [
            $this->payload($a, ['parent_id' => $b->id]), $this->payload($b),
        ]])->assertUnprocessable();
        $this->putJson(route('navigation.save'), ['items' => [$this->payload($a, ['parent_id' => $a->id])]])->assertUnprocessable();
        $this->putJson(route('navigation.save'), ['items' => [$this->payload($a, ['url' => 'javascript:alert(1)'])]])->assertJsonValidationErrors('items.0.url');
        $this->putJson(route('navigation.save'), ['items' => [$this->payload($a, ['page_id' => $draft->id])]])->assertUnprocessable();

        $this->assertDatabaseHas('navigations', ['id' => $b->id, 'parent_id' => $a->id]);
    }

    public function test_item_keeps_existing_link_when_its_page_becomes_inactive(): void
    {
        $this->actAsNavigationAdmin();
        $page = Page::factory()->create(['published' => true]);
        $item = $this->item(['page_id' => $page->id]);
        $page->update(['published' => false]);

        $this->putJson(route('navigation.save'), ['items' => [$this->payload($item, ['title' => 'Renamed'])]])->assertOk();

        $this->assertDatabaseHas('navigations', ['id' => $item->id, 'title' => 'Renamed', 'page_id' => $page->id]);
    }

    public function test_tree_endpoint_and_admin_page_load(): void
    {
        $this->actAsNavigationAdmin();
        $parent = $this->item();
        $this->item(['parent_id' => $parent->id]);

        $this->getJson(route('navigation.tree'))->assertOk()->assertJsonCount(2, 'items');
        $this->get(route('navigation'))->assertOk()->assertSee('Save Menu');
    }

    // ── Pages ─────────────────────────────────────────────────

    public function test_deleted_pages_are_soft_deleted_and_unlinked(): void
    {
        $this->actAsNavigationAdmin();
        $page = Page::factory()->create(['published' => true]);
        $item = $this->item(['page_id' => $page->id]);

        $this->delete(route('pages.destroy', $page->id))->assertRedirect(route('pages.index'));

        $this->assertSoftDeleted('pages', ['id' => $page->id]);
        $this->assertNull($item->fresh()->page_id);
    }
}

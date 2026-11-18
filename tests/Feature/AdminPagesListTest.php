<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(new User(['name' => 'Admin', 'email' => 'a@example.test', 'user_role' => 2]));
    }

    public function test_every_routed_frontend_page_is_listed_once_with_real_url_and_editor_link(): void
    {
        $this->get(route('pages.index'))->assertOk();
        $this->get(route('pages.index'))->assertOk(); // idempotent sync

        foreach (['pages.about.index', 'pages.kontakt.index', 'pages.cennobots.index', 'welcome', 'pages.cennobots.models.index'] as $layout) {
            $this->assertSame(1, Page::where('layout', $layout)->count(), $layout);
        }

        $page = Page::where('layout', 'pages.kontakt.index')->first();
        $this->assertSame('Static', $page->page_type);
        $this->assertSame(url('/kontakt'), $page->public_url);
        $this->get(route('pages.index'))
            ->assertSee(route('pages.editor', $page->id), false)
            ->assertSee(route('pages.seo', $page->id), false)
            ->assertSee('Edit SEO');
    }

    public function test_removed_pages_can_be_listed_and_restored_without_duplicating(): void
    {
        $this->get(route('pages.index'));
        $page = Page::where('layout', 'pages.kontakt.index')->first();
        $page->delete();

        $this->get(route('pages.index', ['removed' => 1]))->assertOk()->assertSee('Restore');
        $this->post(route('pages.restore', $page->id))->assertRedirect(route('pages.index'));

        $this->assertNull($page->fresh()->deleted_at);
        $this->assertSame(1, Page::withTrashed()->where('layout', 'pages.kontakt.index')->count());
    }
}

<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Navigation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AutomaticBladePageRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $admin = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $this->actingAs($admin);
    }

    public function test_pages_index_registers_untracked_blade_pages_automatically(): void
    {
        Route::get('/test', fn () => 'test')->name('test');

        $this->get(route('pages.index'))->assertOk();

        $page = Page::where('layout', 'pages.test.index')->first();
        $this->assertNotNull($page);
        $this->assertSame('test', $page->slug);
        $this->assertSame('test', $page->route_name);
        $this->assertTrue($page->published);
    }

    public function test_sync_does_not_duplicate_or_overwrite_existing_manual_pages(): void
    {
        Route::get('/test', fn () => 'test')->name('test');
        $page = Page::factory()->create([
            'title' => 'My custom page title',
            'slug' => 'custom-existing-slug',
            'layout' => 'pages.test.index',
            'published' => false,
        ]);

        $this->get(route('pages.index'))->assertOk();

        $this->assertSame(1, Page::where('layout', 'pages.test.index')->count());
        $this->assertSame('My custom page title', $page->fresh()->title);
        $this->assertSame('custom-existing-slug', $page->fresh()->slug);
        $this->assertFalse($page->fresh()->published);
    }

    public function test_manual_registration_option_still_works(): void
    {
        Route::get('/test', fn () => 'test')->name('test');

        $response = $this->post(route('pages.add-existing'), [
            'view' => 'pages.test.index',
            'title' => 'Manually Registered Page',
            'slug' => 'manual-test-page',
            'published' => '1',
        ]);

        $response->assertRedirect(route('pages.index'));
        $this->assertDatabaseHas('pages', [
            'layout' => 'pages.test.index',
            'title' => 'Manually Registered Page',
            'slug' => 'manual-test-page',
        ]);
    }

    public function test_welcome_page_is_a_picker_option_and_registers_once_without_copying_its_view(): void
    {
        $this->get(route('pages.create'))->assertOk();

        $picker = $this->getJson(route('pages.manual-blades'));
        $picker->assertOk()->assertJsonFragment([
            'view' => 'welcome',
            'path' => '../welcome.blade.php',
            'display_name' => 'Welcome Page',
        ]);

        $this->assertSame(1, Page::where('layout', 'welcome')->count());
        $welcomePage = Page::where('layout', 'welcome')->firstOrFail();
        $this->assertTrue($welcomePage->published, 'The homepage registers as active; /pages/welcome-page redirects to / and the active toggle controls it.');
        $this->assertSame('welcome', $welcomePage->route_name);
        $this->assertTrue(view()->exists('welcome'));
    }

    public function test_welcome_page_can_be_linked_from_navigation_and_resolves_to_existing_homepage(): void
    {
        $this->get(route('pages.create'))->assertOk();
        $welcomePage = Page::where('layout', 'welcome')->firstOrFail();
        $navigation = Navigation::create([
            'title' => 'Home',
            'slug' => 'home',
            'page_id' => $welcomePage->id,
            'published' => true,
            'position' => 0,
        ]);

        $this->assertSame(url('/'), $navigation->getLinkUrl());
        $this->get('/')->assertOk();
    }
}

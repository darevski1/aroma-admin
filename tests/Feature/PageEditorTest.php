<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $this->actingAs($this->admin);
    }

    public function test_visual_editor_persists_page_content_settings_and_seo(): void
    {
        $page = Page::factory()->create(['content' => '<main><p>Before</p></main>', 'settings' => ['existing' => 'kept']]);

        $response = $this->postJson(route('pages.visual-save', $page), [
            'title' => 'Updated title',
            'content' => '<main><p>After</p></main>',
            'settings' => ['hero_heading' => 'New heading'],
            'meta_title' => 'SEO title',
            'meta_description' => 'SEO description',
            'canonical_url' => 'https://example.test/canonical',
            'og_title' => 'Social title',
            'og_description' => 'Social description',
            'og_image' => 'https://example.test/share.png',
            'robots_index' => false,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $page->refresh();
        $this->assertSame('<main><p>After</p></main>', $page->content);
        $this->assertSame('Updated title', $page->title);
        $this->assertSame('kept', $page->settings['existing']);
        $this->assertSame('New heading', $page->settings['hero_heading']);
        $this->assertSame('https://example.test/canonical', $page->settings['canonical_url']);
        $this->assertFalse($page->settings['robots_index']);
        $this->assertSame('SEO title', $page->meta_title);
        $this->assertSame('SEO description', $page->meta_description);
    }

    public function test_unpublished_dynamic_pages_are_hidden_but_admin_editor_can_preview(): void
    {
        $page = Page::factory()->create(['published' => false, 'content' => '<main><h1>Draft content</h1></main>']);

        $this->get(route('pages.public', $page->slug))->assertNotFound();
        $this->get(route('pages.public', ['slug' => $page->slug, 'editor' => 1]))->assertOk()->assertSee('Draft content');
    }

}

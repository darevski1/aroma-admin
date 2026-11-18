<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use App\Services\InlineEdits;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSeoAndStyleEditsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): void
    {
        $this->actingAs(new User(['name' => 'A', 'email' => 'a@example.test', 'user_role' => 2]));
    }

    public function test_pages_list_has_only_edit_page_and_edit_seo_actions(): void
    {
        $this->admin();
        $page = Page::create(['title' => 'K', 'slug' => 'kontakt', 'layout' => 'pages.kontakt.index', 'route_name' => 'kontakt', 'published' => true]);

        $this->get(route('pages.index'))->assertOk()
            ->assertSee(route('pages.editor', $page->id), false)
            ->assertSee(route('pages.seo', $page->id), false)
            ->assertDontSee('"' . route('pages.edit-content', $page->id) . '"', false)
            ->assertDontSee('"' . route('pages.edit', $page->id) . '"', false);
    }

    public function test_seo_is_saved_separately_and_rendered_on_the_frontend(): void
    {
        $this->admin();
        $page = Page::create([
            'title' => 'K', 'slug' => 'kontakt', 'layout' => 'pages.kontakt.index', 'route_name' => 'kontakt', 'published' => true,
            'content' => 'keep me', 'settings' => ['inline_edits' => [['k' => 'x', 'n' => 0, 'html' => 'y']]],
        ]);

        $this->get(route('pages.seo', $page))->assertOk()->assertSee('Robots follow');
        $this->patch(route('pages.seo.update', $page), [
            'meta_title' => 'My SEO title', 'meta_description' => 'My description', 'meta_keywords' => 'a, b',
            'canonical_url' => 'https://example.com/k', 'og_title' => 'OG T', 'og_description' => 'OG D',
            'og_image' => 'https://example.com/i.jpg', 'robots_index' => 'noindex', 'robots_follow' => 'nofollow',
        ])->assertRedirect(route('pages.seo', $page));

        $page = $page->fresh();
        $this->assertSame('keep me', $page->content);
        $this->assertSame('y', $page->settings['inline_edits'][0]['html']);
        $this->assertFalse($page->settings['robots_index']);

        $this->get('/kontakt')->assertOk()
            ->assertSee('My SEO title</title>', false)
            ->assertSee('content="a, b"', false)
            ->assertSee('property="og:title" content="OG T"', false)
            ->assertSee('noindex,nofollow', false);
    }

    public function test_seo_validation_rejects_bad_input(): void
    {
        $this->admin();
        $page = Page::create(['title' => 'K', 'slug' => 'kontakt', 'layout' => 'pages.kontakt.index', 'published' => true]);

        $this->patch(route('pages.seo.update', $page), ['canonical_url' => 'nope', 'og_image' => 'javascript:alert(1)', 'robots_index' => 'x', 'robots_follow' => 'follow'])
            ->assertSessionHasErrors(['canonical_url', 'og_image', 'robots_index']);
    }

    public function test_style_edits_are_applied_per_element_and_sanitized(): void
    {
        $svc = new InlineEdits();
        $html = '<section class="a" style="background-image:url(/x.jpg)"><h2>Title</h2><img src="/a.png" style="border:0"></section><section><h2>Other</h2></section>';

        $edits = $svc->merge([], [
            ['k' => 'sty:0|section', 'n' => 0, 'html' => json_encode(['bg' => '#112233', 'color' => 'red;x'])],
            ['k' => 'sty:0.1|img', 'n' => 0, 'html' => json_encode(['w' => 300, 'lock' => true])],
            ['k' => 'sty:0.0|h2', 'n' => 0, 'html' => json_encode(['color' => '#ff0000'])],
            ['k' => 'sty:bad', 'n' => 0, 'html' => '{}'],
        ]);
        $this->assertCount(3, $edits);

        $out = $svc->apply($html, $edits);
        $this->assertStringContainsString('background-image:url(/x.jpg); background-color: #112233', preg_replace('/;\s*/', '; ', str_replace('background-image: url(/x.jpg)', 'background-image:url(/x.jpg)', $out)) ?: $out);
        $this->assertStringContainsString('width: 300px; max-width: 100%; height: auto', $out);
        $this->assertStringContainsString('<h2 style="color: #ff0000;">Title</h2>', $out);
        $this->assertStringNotContainsString('red', $out);
        // the second section is untouched
        $this->assertStringContainsString('<section><h2>Other</h2></section>', $out);

        // reset (empty) removes the stored entry
        $this->assertCount(2, $svc->merge($edits, [['k' => 'sty:0.0|h2', 'n' => 0, 'html' => '{}']]));
    }

    public function test_selected_text_colour_survives_text_sanitizing_but_other_styles_do_not(): void
    {
        $svc = new InlineEdits();
        $clean = $svc->sanitize('<span style="color: rgb(255, 0, 0); position:fixed">a</span><span style="color:#00ff00">b</span><span style="background:url(x)">c</span>');
        $this->assertStringContainsString('<span>a</span>', $clean); // mixed declarations are dropped
        $this->assertStringContainsString('<span style="color:#00ff00">b</span>', $clean);
        $this->assertStringContainsString('<span>c</span>', $clean);
    }
}

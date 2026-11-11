<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomeEditorSaveTest extends TestCase
{
    use RefreshDatabase;

    public function test_text_saved_in_the_editor_is_shown_on_the_homepage(): void
    {
        $this->actingAs(new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]));
        $page = Page::create(['title' => 'Welcome Page', 'slug' => 'welcome-page', 'layout' => 'welcome', 'route_name' => 'welcome', 'published' => true]);

        $this->postJson(route('pages.save-content', $page), ['content' => '<section><h1>Edited headline xyz</h1></section>'])
            ->assertOk()->assertJson(['success' => true]);

        $this->get('/')->assertOk()->assertSee('Edited headline xyz', false);
        $this->get('/?editor=1')->assertOk()->assertSee('Edited headline xyz', false);
    }
}

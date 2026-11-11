<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CenobotsModelPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_registered_model_pages_do_not_crash(): void
    {
        Page::create(['title' => 'Models', 'slug' => 'cennobots-models', 'layout' => 'pages.cennobots.models.index', 'published' => true]);
        Page::create(['title' => 'Model', 'slug' => 'cennobots-models-show', 'layout' => 'pages.cennobots.models.show', 'published' => true]);

        $this->get('/pages/cennobots-models')->assertOk();
        $this->get('/pages/cennobots-models-show')->assertRedirect(route('cennobots.models'));
        $this->get('/modeli')->assertOk();
    }
}

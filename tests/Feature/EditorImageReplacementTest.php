<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class EditorImageReplacementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake(config('media-library.disk_name', 'public'));
        $this->actingAs(new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]));
    }

    public function test_editor_page_contains_the_replace_image_modal(): void
    {
        $page = Page::create(['title' => 'Welcome Page', 'slug' => 'welcome-page', 'layout' => 'welcome', 'route_name' => 'welcome', 'published' => true]);

        $this->get(route('pages.editor', $page))->assertOk()->assertSee('editorImageModal', false)->assertSee('eimAlt', false);
    }

    public function test_uploaded_image_is_in_the_library_immediately_and_metadata_saves(): void
    {
        $upload = $this->postJson(route('media.picker.upload'), [
            'file' => UploadedFile::fake()->image('hero.png', 40, 40),
            'collection' => 'editor-library',
        ])->assertOk();

        $id = $upload->json('id');

        $this->getJson(route('media.picker', ['mime_type' => 'image/']))
            ->assertOk()->assertJsonFragment(['id' => $id]);

        $this->patchJson(route('media.update', $id), [
            'name' => 'Hero banner',
            'custom_properties' => ['alt_text' => 'A hero', 'title' => 'Hero title', 'caption' => 'Long text', 'copyright' => 'ITPlus'],
        ])->assertOk()->assertJson(['success' => true]);

        $media = Media::findOrFail($id);
        $this->assertSame('Hero banner', $media->name);
        $this->assertSame('A hero', $media->getCustomProperty('alt_text'));
        $this->assertSame('Hero title', $media->getCustomProperty('title'));
        $this->assertSame('Long text', $media->getCustomProperty('caption'));
        $this->assertSame('ITPlus', $media->getCustomProperty('copyright'));

        $this->getJson(route('media.picker'))->assertJsonFragment(['alt_text' => 'A hero']);
    }

    public function test_media_library_detail_form_posting_flat_fields_with_put_saves(): void
    {
        $id = $this->postJson(route('media.picker.upload'), ['file' => UploadedFile::fake()->image('a.png')])->json('id');

        $this->put(route('media.update', $id), ['alt_text' => 'Flat alt', 'title' => 'Flat title', 'caption' => 'c'], ['Accept' => 'application/json'])
            ->assertOk()->assertJson(['success' => true]);

        $media = Media::findOrFail($id);
        $this->assertSame('Flat alt', $media->getCustomProperty('alt_text'));
        $this->assertSame('Flat title', $media->getCustomProperty('title'));
    }

    public function test_replaced_image_markup_saves_and_renders_on_the_page(): void
    {
        $page = Page::create(['title' => 'Welcome Page', 'slug' => 'welcome-page', 'layout' => 'welcome', 'route_name' => 'welcome', 'published' => true]);
        $html = '<section><img src="/storage/9/new.png" alt="A hero" title="Hero title"></section>';

        $this->postJson(route('pages.save-content', $page), ['content' => $html])->assertOk();

        $this->get('/')->assertOk()->assertSee('src="/storage/9/new.png" alt="A hero" title="Hero title"', false);
    }
}

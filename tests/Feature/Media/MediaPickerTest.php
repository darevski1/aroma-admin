<?php

namespace Tests\Feature\Media;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaPickerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['user_role' => 2]);
        $this->actingAs($this->admin);
    }

    /** @test */
    public function media_picker_api_returns_media_items(): void
    {
        $page = Page::factory()->create();

        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('photo1.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('photo2.jpg', 800, 600)
        )->toMediaCollection('attachments');

        $response = $this->getJson(route('media.picker'));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => ['id', 'name', 'file_name', 'mime_type', 'size', 'url', 'thumb_url', 'collection_name'],
            ],
            'current_page',
            'last_page',
            'total',
        ]);
    }

    /** @test */
    public function media_picker_can_upload_new_files(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('picker-upload.jpg', 800, 600);

        $response = $this->postJson(route('media.picker.upload'), [
            'file' => $file,
            'collection' => 'featured_image',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id', 'name', 'file_name', 'mime_type', 'size', 'url', 'thumb_url', 'collection_name',
        ]);

        $this->assertDatabaseHas('media', [
            'collection_name' => 'featured_image',
        ]);
    }

    /** @test */
    public function media_item_json_endpoint_returns_full_data(): void
    {
        $page = Page::factory()->create();
        $media = $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('detail.jpg', 800, 600)
        )->usingName('My Photo')
          ->withCustomProperties(['alt_text' => 'A beautiful photo'])
          ->toMediaCollection('featured_image');

        $response = $this->getJson(route('media.json', $media->id));
        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'My Photo',
            'collection_name' => 'featured_image',
            'custom_properties' => [
                'alt_text' => 'A beautiful photo',
            ],
        ]);
    }

    /** @test */
    public function page_featured_image_can_be_set_via_picker(): void
    {
        Storage::fake('public');

        $page = Page::factory()->create();

        $file = UploadedFile::fake()->image('featured.jpg', 1200, 800);

        $response = $this->post(route('pages.store'), [
            'title' => 'Test Page with Media',
            'featured_image_file' => $file,
        ]);

        $response->assertRedirect();

        $page = Page::where('title', 'Test Page with Media')->first();
        $this->assertNotNull($page);
        $this->assertCount(1, $page->getMedia('featured_image'));
    }

    /** @test */
    public function user_avatar_can_be_set_via_picker(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $response = $this->post(route('users.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_role' => 1,
            'active' => 1,
            'avatar_file' => $file,
        ]);

        $response->assertRedirect();

        $newUser = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertCount(1, $newUser->getMedia('avatar'));
    }

    /** @test */
    public function removing_avatar_clears_collection(): void
    {
        $user = User::factory()->create();
        $user->addMediaFromUploadedFile(
            UploadedFile::fake()->image('avatar.jpg', 400, 400)
        )->toMediaCollection('avatar');

        $this->assertCount(1, $user->getMedia('avatar'));

        $response = $this->patch(route('users.update', $user->id), [
            'name' => $user->name,
            'email' => $user->email,
            'user_role' => $user->user_role,
            'active' => 1,
            'remove_avatar' => 1,
        ]);

        $response->assertRedirect();

        $user->refresh();
        $this->assertCount(0, $user->getMedia('avatar'));
    }
}

<?php

namespace Tests\Feature\Media;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'user_role' => 2,
        ]);

        $this->actingAs($this->admin);
    }

    /** @test */
    public function authenticated_admin_can_access_media_library(): void
    {
        $response = $this->get(route('media.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin_pages.media.index');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_media_library(): void
    {
        auth()->logout();
        $response = $this->get(route('media.index'));
        $response->assertRedirect('/login');
    }

    /** @test */
    public function non_admin_user_cannot_access_media_library(): void
    {
        $regularUser = User::factory()->create(['user_role' => 1]);
        $this->actingAs($regularUser);
        $response = $this->get(route('media.index'));
        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_upload_image_via_media_library(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test-photo.jpg', 800, 600)->size(500);

        $response = $this->postJson(route('media.store'), [
            'file' => $file,
            'collection' => 'attachments',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('media', [
            'collection_name' => 'attachments',
            'mime_type' => 'image/jpeg',
        ]);
    }

    /** @test */
    public function admin_can_upload_pdf_via_media_library(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 500, 'application/pdf');

        $response = $this->postJson(route('media.store'), [
            'file' => $file,
            'collection' => 'attachments',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('media', [
            'collection_name' => 'attachments',
            'mime_type' => 'application/pdf',
        ]);
    }

    /** @test */
    public function upload_rejects_invalid_mime_type(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('malicious.php', 100, 'application/x-php');

        $response = $this->postJson(route('media.store'), [
            'file' => $file,
            'collection' => 'attachments',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function upload_rejects_oversized_files(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('huge.jpg', 800, 600)->size(11000); // 11MB > 10MB limit

        $response = $this->postJson(route('media.store'), [
            'file' => $file,
            'collection' => 'attachments',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function admin_can_delete_media(): void
    {
        $page = Page::factory()->create();
        $media = $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('test.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $response = $this->deleteJson(route('media.destroy', $media->id));
        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    /** @test */
    public function admin_can_bulk_delete_media(): void
    {
        $page = Page::factory()->create();
        $media1 = $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('test1.jpg', 800, 600)
        )->toMediaCollection('featured_image');
        $media2 = $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('test2.jpg', 800, 600)
        )->toMediaCollection('attachments');

        $response = $this->postJson(route('media.bulk-delete'), [
            'ids' => [$media1->id, $media2->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('media', ['id' => $media1->id]);
        $this->assertDatabaseMissing('media', ['id' => $media2->id]);
    }

    /** @test */
    public function admin_can_update_media_metadata(): void
    {
        $page = Page::factory()->create();
        $media = $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('test.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $response = $this->patchJson(route('media.update', $media->id), [
            'name' => 'updated-name',
            'custom_properties' => [
                'alt_text' => 'A test image',
                'title' => 'Test Title',
                'caption' => 'A caption for the image',
                'copyright' => '© 2026 Test',
            ],
        ]);

        $response->assertStatus(200);

        $media->refresh();
        $this->assertEquals('updated-name', $media->name);
        $this->assertEquals('A test image', $media->getCustomProperty('alt_text'));
        $this->assertEquals('Test Title', $media->getCustomProperty('title'));
    }

    /** @test */
    public function media_picker_returns_paginated_results(): void
    {
        $page = Page::factory()->create();
        for ($i = 0; $i < 5; $i++) {
            $page->addMediaFromUploadedFile(
                UploadedFile::fake()->image("test-{$i}.jpg", 800, 600)
            )->toMediaCollection('featured_image');
        }

        $response = $this->getJson(route('media.picker'));
        $response->assertStatus(200);
        $response->assertJsonCount(5, 'data');
    }

    /** @test */
    public function media_picker_can_filter_by_collection(): void
    {
        $page = Page::factory()->create();
        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('featured.jpg', 800, 600)
        )->toMediaCollection('featured_image');
        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('gallery.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $response = $this->getJson(route('media.picker', ['collection' => 'featured_image']));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function media_picker_can_search(): void
    {
        $page = Page::factory()->create();
        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('sunflower.jpg', 800, 600)
        )->toMediaCollection('featured_image');
        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('mountain.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $response = $this->getJson(route('media.picker', ['search' => 'sun']));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
    }
}

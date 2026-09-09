<?php

namespace Tests\Feature\Media;

use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaCollectionsTest extends TestCase
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
    public function page_has_featured_image_collection(): void
    {
        $page = Page::factory()->create();
        $collections = $page->getMediaCollections()->map(fn ($c) => $c->name);

        $this->assertTrue($collections->contains('featured_image'));
        $this->assertTrue($collections->contains('attachments'));
    }

    /** @test */
    public function post_has_all_required_collections(): void
    {
        $post = Post::factory()->create();
        $collections = $post->getMediaCollections()->map(fn ($c) => $c->name);

        $this->assertTrue($collections->contains('featured_image'));
        $this->assertTrue($collections->contains('attachments'));
    }

    /** @test */
    public function user_has_avatar_collection(): void
    {
        $user = User::factory()->create();
        $collections = $user->getMediaCollections()->map(fn ($c) => $c->name);

        $this->assertTrue($collections->contains('avatar'));
    }

    /** @test */
    public function featured_image_collection_is_single_file(): void
    {
        $page = Page::factory()->create();

        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('first.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $this->assertCount(1, $page->getMedia('featured_image'));

        // Adding a second file should replace the first
        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('second.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $this->assertCount(1, $page->getMedia('featured_image'));
    }

    /** @test */
    public function deleting_page_removes_its_media(): void
    {
        $page = Page::factory()->create();

        $page->addMediaFromUploadedFile(
            UploadedFile::fake()->image('featured.jpg', 800, 600)
        )->toMediaCollection('featured_image');

        $this->assertDatabaseCount('media', 1);

        $page->delete();

        $this->assertDatabaseCount('media', 0);
    }
}

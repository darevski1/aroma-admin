<?php

namespace Tests\Feature\HomepageSections;

use App\Models\HomepageSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomepageSectionCreateTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // The users table has a FK on user_role -> roles.id; provide the rows.
        DB::table('roles')->insert([
            ['id' => 1, 'user_role' => 'user'],
            ['id' => 2, 'user_role' => 'admin'],
        ]);

        $this->admin = User::factory()->create([
            'user_role' => 2,
        ]);

        $this->actingAs($this->admin);
    }

    #[Test]
    public function admin_can_open_the_create_section_form(): void
    {
        $response = $this->get(route('homepage-sections.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin_pages.homepage-sections.create');
    }

    #[Test]
    public function admin_can_create_a_new_section_with_content(): void
    {
        Storage::fake('public');

        $response = $this->post(route('homepage-sections.store'), [
            'name'          => 'Summer Promo',
            'eyebrow'       => 'limited',
            'heading'       => 'Летна промоција',
            'body'          => 'Искористете ги нашите специјални понуди.',
            'image_file'    => UploadedFile::fake()->image('promo.jpg'),
            'button1_label' => 'Побарај понуда',
            'button1_url'   => '/kontakt',
            'background'    => 'dark',
            'published'     => '1',
        ]);

        $response->assertRedirect(route('homepage-sections.index'));
        $response->assertSessionHas('success');

        $section = HomepageSection::where('name', 'Summer Promo')->first();

        $this->assertNotNull($section);
        $this->assertSame('summer-promo', $section->slug);
        $this->assertTrue($section->is_published);
        $this->assertSame('dark', $section->content['background']);
        $this->assertSame('Летна промоција', $section->content['heading']);
        $this->assertCount(1, $section->content['buttons']);
        $this->assertSame('primary', $section->content['buttons'][0]['style']);
        $this->assertNotEmpty($section->content['image']);

        Storage::disk('public')->assertExists(str_replace('/storage/', '', $section->content['image']));

        // New section is appended after the seeded sections.
        $maxOrder = (int) HomepageSection::whereKeyNot($section->id)->max('sort_order');
        $this->assertEquals($maxOrder + 1, $section->sort_order);
    }

    #[Test]
    public function created_section_appears_in_the_management_list(): void
    {
        $this->post(route('homepage-sections.store'), [
            'name'      => 'Promo Banner',
            'published' => '1',
        ]);

        $response = $this->get(route('homepage-sections.index'));

        $response->assertStatus(200);
        $response->assertSee('Promo Banner');
        $response->assertSee('Custom');
    }

    #[Test]
    public function slug_is_auto_generated_and_made_unique(): void
    {
        $this->post(route('homepage-sections.store'), ['name' => 'News Section', 'published' => '1']);
        $this->post(route('homepage-sections.store'), ['name' => 'News Section', 'published' => '1']);

        $slugs = HomepageSection::where('name', 'News Section')->pluck('slug')->sort()->values();

        $this->assertCount(2, $slugs);
        $this->assertEquals(['news-section', 'news-section-2'], $slugs->all());
    }

    #[Test]
    public function reserved_slugs_are_rejected(): void
    {
        $response = $this->from(route('homepage-sections.create'))
            ->post(route('homepage-sections.store'), [
                'name' => 'Fake Hero',
                'slug' => 'hero',
            ]);

        $response->assertSessionHasErrors('slug');
        $this->assertDatabaseMissing('homepage_sections', ['name' => 'Fake Hero']);
    }

    #[Test]
    public function name_is_required(): void
    {
        $response = $this->post(route('homepage-sections.store'), []);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function custom_section_content_renders_on_the_homepage(): void
    {
        HomepageSection::create([
            'name'         => 'Custom Promo',
            'slug'         => 'custom-promo',
            'is_published' => true,
            'sort_order'   => 99,
            'content'      => [
                'eyebrow' => 'limited',
                'heading' => 'Летна промоција',
                'body'    => 'Специјални понуди.',
                'buttons' => [
                    ['label' => 'Контакт', 'url' => '/kontakt', 'style' => 'primary'],
                ],
                'background' => 'light',
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Летна промоција', false);
        $response->assertSee('Специјални понуди.', false);
        $response->assertSee('/kontakt', false);
    }

    #[Test]
    public function unpublished_custom_section_does_not_render_on_the_homepage(): void
    {
        HomepageSection::create([
            'name'         => 'Hidden Promo',
            'slug'         => 'hidden-promo',
            'is_published' => false,
            'sort_order'   => 99,
            'content'      => ['heading' => 'Скриена секција'],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('Скриена секција', false);
    }

    #[Test]
    public function existing_sections_still_render_via_their_partials(): void
    {
        // Seed the built-in sections exactly like the app seeder does.
        $this->seed(\Database\Seeders\HomepageSectionSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
        // Content that only exists in the built-in partials:
        $response->assertSee('Illuminating Brilliance in Digital Experiences, Content and Creativity', false);
        $response->assertSee('Побарајте понуда', false);
    }

    #[Test]
    public function guests_cannot_create_sections(): void
    {
        auth()->logout();

        $response = $this->post(route('homepage-sections.store'), ['name' => 'Hacked']);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('homepage_sections', ['name' => 'Hacked']);
    }
}

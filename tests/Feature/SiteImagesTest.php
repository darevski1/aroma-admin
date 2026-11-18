<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Models\Page;
use App\Models\SiteImageOverride;
use App\Models\SiteImageSlot;
use App\Models\User;
use App\Services\InlineEdits;
use App\Services\SiteImages\SiteImages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Admin → Site Images: every frontend image is listed, replaceable per page or site-wide, and restorable. */
class SiteImagesTest extends TestCase
{
    use RefreshDatabase;

    private const PRINTER = '/assets/images/printer-hero.jpg';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        HomepageSection::create(['name' => 'Printers', 'slug' => 'printers', 'is_published' => true, 'sort_order' => 1]);
    }

    private function admin(): User
    {
        $user = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $user->email_verified_at = now();

        return $user;
    }

    private function upload(string $name = 'new-printer.jpg', int $w = 800, int $h = 400): array
    {
        return $this->actingAs($this->admin())
            ->post(route('media.picker.upload'), ['file' => UploadedFile::fake()->image($name, $w, $h), 'collection' => 'site-images'])
            ->assertOk()->json();
    }

    private function save(array $payload)
    {
        return $this->actingAs($this->admin())->postJson(route('site-images.save'), $payload + [
            'scope' => 'page', 'path' => '/', 'src_key' => self::PRINTER,
        ]);
    }

    public function test_homepage_printer_background_image_is_inventoried(): void
    {
        $this->get('/')->assertOk()->assertSee(self::PRINTER, false);

        $slot = SiteImageSlot::where('path', '/')->where('src_key', self::PRINTER)->first();
        $this->assertNotNull($slot);
        $this->assertSame('bg', $slot->kind);
        $this->assertStringContainsString('печатачи', $slot->context);
    }

    public function test_homepage_printer_image_can_be_replaced_and_restored(): void
    {
        $this->get('/'); // records the slot
        $media = $this->upload();

        $this->save(['media_id' => $media['id'], 'alt' => 'New printer'])->assertOk();

        $html = $this->get('/')->assertOk()->getContent();
        $this->assertStringContainsString("url('" . parse_url($media['url'], PHP_URL_PATH) . "')", $html);
        $this->assertStringNotContainsString("url('" . self::PRINTER . "')", $html);

        // Removing the override brings the untouched original back.
        $this->actingAs($this->admin())->postJson(route('site-images.reset'), ['scope' => 'page', 'path' => '/', 'src_key' => self::PRINTER])->assertOk();
        $this->assertStringContainsString(self::PRINTER, $this->get('/')->getContent());
    }

    public function test_stylesheet_background_of_the_homepage_hero_is_editable(): void
    {
        HomepageSection::create(['name' => 'Hero', 'slug' => 'hero', 'is_published' => true, 'sort_order' => 0]);
        $key = '/assets/images/logo/hero-bg-fi24CLXc.jpg';
        $this->get('/')->assertOk();
        $this->assertSame('css', SiteImageSlot::where('path', '/')->where('src_key', $key)->value('kind'));

        $media = $this->upload('hero.jpg');
        $this->save(['media_id' => $media['id'], 'src_key' => $key])->assertOk();
        $this->assertStringContainsString('.intro-header{background-image:url(\'' . parse_url($media['url'], PHP_URL_PATH) . '\') !important', $this->get('/')->getContent());
    }

    public function test_original_stays_when_nothing_is_replaced(): void
    {
        $this->assertStringContainsString("url('" . self::PRINTER . "')", $this->get('/')->getContent());
        $this->assertSame(0, SiteImageOverride::count());
    }

    public function test_page_scope_does_not_leak_to_other_pages_but_global_scope_does(): void
    {
        $this->get('/');
        $this->get('/uslugi');
        $logo = SiteImageSlot::where('path', '/')->where('kind', 'img')->where('src_key', 'like', '%logo-itplus.png')->first();
        $this->assertNotNull($logo);
        $this->assertNotNull(SiteImageSlot::where('path', '/uslugi')->where('src_key', $logo->src_key)->first(), 'navbar logo is shared by both pages');

        $media = $this->upload('brand.png');
        $url = parse_url($media['url'], PHP_URL_PATH);

        $this->save(['media_id' => $media['id'], 'path' => '/', 'src_key' => $logo->src_key])->assertOk();
        $this->assertStringContainsString($url, $this->get('/')->getContent());
        $this->assertStringNotContainsString($url, $this->get('/uslugi')->getContent());

        $this->save(['media_id' => $media['id'], 'scope' => 'global', 'path' => '/', 'src_key' => $logo->src_key])->assertOk();
        $this->assertStringContainsString($url, $this->get('/uslugi')->getContent());
        $this->assertSame(1, SiteImageOverride::count(), 'switching to global removes the page-only twin');
    }

    public function test_img_alt_title_position_and_mobile_image(): void
    {
        $this->get('/');
        $logo = SiteImageSlot::where('path', '/')->where('kind', 'img')->where('src_key', 'like', '%logo-itplus.png')->firstOrFail();
        $desktop = $this->upload('d.png');
        $mobile = $this->upload('m.png', 400, 300);

        $this->save([
            'media_id' => $desktop['id'], 'mobile_media_id' => $mobile['id'], 'alt' => 'ITPlus logo', 'title' => 'Home',
            'object_position' => 'left top', 'object_fit' => 'contain', 'src_key' => $logo->src_key,
        ])->assertOk();

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('alt="ITPlus logo"', $html);
        $this->assertStringContainsString('title="Home"', $html);
        $this->assertStringContainsString('object-position:left top', $html);
        $this->assertStringContainsString('<source media="(max-width: 767px)" srcset="' . parse_url($mobile['url'], PHP_URL_PATH) . '">', $html);
    }

    public function test_background_mobile_image_adds_media_query(): void
    {
        $this->get('/');
        $desktop = $this->upload('d.jpg');
        $mobile = $this->upload('m.jpg', 400, 300);
        $this->save(['media_id' => $desktop['id'], 'mobile_media_id' => $mobile['id']])->assertOk();

        $html = $this->get('/')->getContent();
        $this->assertMatchesRegularExpression('/@media \(max-width: 767px\)\{\[data-si="si\d+"\]\{background-image:url\("[^"]+"\) !important\}\}/', $html);
    }

    public function test_uploads_are_deduplicated_optimized_and_get_dimensions(): void
    {
        $a = $this->upload('big.jpg', 3000, 1000);
        $b = $this->upload('renamed-copy.jpg', 3000, 1000);
        $this->assertSame($a['id'], $b['id'], 'identical content is stored once, whatever it is called');
        $this->assertSame(1, \Spatie\MediaLibrary\MediaCollections\Models\Media::count());

        $json = $this->actingAs($this->admin())->getJson(route('media.json', $a['id']))->json();
        $this->assertSame(2400, $json['width']);
        $this->assertSame(800, $json['height']);
        Storage::disk('public')->assertExists($a['id'] . '/responsive/480.webp');
    }

    public function test_svg_is_sanitized_and_non_images_are_rejected(): void
    {
        $svg = UploadedFile::fake()->createWithContent('x.svg', '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><script>alert(1)</script><rect width="1" height="1"/></svg>');
        $res = $this->actingAs($this->admin())->post(route('media.picker.upload'), ['file' => $svg])->assertOk()->json();
        $stored = Storage::disk('public')->get($res['id'] . '/' . $res['file_name']);
        $this->assertStringNotContainsString('script', $stored);
        $this->assertStringNotContainsString('onload', $stored);
        $this->assertStringContainsString('<rect', $stored);

        $this->actingAs($this->admin())->postJson(route('media.picker.upload'), ['file' => UploadedFile::fake()->create('a.pdf', 10, 'application/pdf')])
            ->assertStatus(422);
    }

    public function test_deleting_an_image_in_use_needs_confirmation(): void
    {
        $this->get('/');
        $media = $this->upload();
        $this->save(['media_id' => $media['id']])->assertOk();

        $this->actingAs($this->admin())->deleteJson(route('media.destroy', $media['id']))
            ->assertStatus(409)->assertJsonPath('in_use', true);
        $this->assertDatabaseHas('media', ['id' => $media['id']]);

        $this->actingAs($this->admin())->deleteJson(route('media.destroy', $media['id']), ['force' => 1])->assertOk();
        $this->assertDatabaseMissing('media', ['id' => $media['id']]);
        $this->assertSame(0, SiteImageOverride::count());
        $this->assertStringContainsString(self::PRINTER, $this->get('/')->getContent(), 'original shows again');
    }

    public function test_unused_image_deletes_without_confirmation(): void
    {
        $media = $this->upload();
        $this->actingAs($this->admin())->deleteJson(route('media.destroy', $media['id']))->assertOk();
    }

    public function test_only_admins_can_manage_site_images(): void
    {
        $this->get('/');
        $this->get(route('site-images.index'))->assertRedirect();
        $this->postJson(route('site-images.save'), ['scope' => 'page', 'path' => '/', 'src_key' => self::PRINTER])->assertStatus(401);
        $this->actingAs($this->admin())->get(route('site-images.index'))->assertOk()->assertSee('printer-hero.jpg');
    }

    public function test_save_rejects_unknown_images_and_non_image_media(): void
    {
        $this->get('/');
        $this->save(['src_key' => '/assets/images/not-on-any-page.jpg'])->assertStatus(422);
    }

    public function test_visual_editor_background_edit_is_applied_on_template_pages(): void
    {
        $html = '<main><div style="background-image: url(\'' . self::PRINTER . '\');" class="x"></div><div style="background-image: url(\'' . self::PRINTER . '\');"></div></main>';
        $service = new InlineEdits();
        $edits = $service->merge([], [['k' => 'bg:' . self::PRINTER, 'n' => 1, 'html' => json_encode(['src' => '/storage/9/new.jpg', 'alt' => '', 'title' => ''])]]);

        $out = $service->apply($html, $edits);
        $this->assertSame(1, substr_count($out, '/storage/9/new.jpg'), 'only the second occurrence changes');
        $this->assertSame(1, substr_count($out, self::PRINTER));

        $marked = $service->apply($html, $edits, true);
        $this->assertStringContainsString('data-ie-key', $marked);
    }

    public function test_normalize_handles_relative_and_absolute_urls(): void
    {
        $this->assertSame('/assets/images/a.png', SiteImages::normalize('./../assets/images/a.png'));
        $this->assertSame('/assets/images/a.png', SiteImages::normalize('assets/images/a.png?v=3'));
        $this->assertSame('/assets/images/a.png', SiteImages::normalize(config('app.url') . '/assets/images/a.png'));
        $this->assertSame('https://cdn.example.com/a.png', SiteImages::normalize('https://cdn.example.com/a.png'));
        $this->assertNull(SiteImages::normalize('data:image/png;base64,AAAA'));
        $this->assertNull(SiteImages::normalize('linear-gradient(red, blue)'));
    }

    public function test_scripts_and_admin_pages_are_not_rewritten(): void
    {
        $service = new SiteImages();
        $html = '<script>var s = \'<img src="' . self::PRINTER . '">\';</script><img src="' . self::PRINTER . '" alt="a">';
        $out = $service->apply($html, [self::PRINTER => ['src' => '/storage/1/n.jpg', 'srcset' => null, 'mobile_src' => null, 'alt' => null, 'title' => null, 'object_position' => null, 'object_fit' => null, 'hidden' => false]]);
        $this->assertStringContainsString('<script>var s = \'<img src="' . self::PRINTER . '">\';</script>', $out);
        $this->assertStringContainsString('src="/storage/1/n.jpg"', $out);
        $this->assertStringContainsString('alt="a"', $out);
    }
}

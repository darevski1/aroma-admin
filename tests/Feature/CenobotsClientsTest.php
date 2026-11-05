<?php

namespace Tests\Feature;

use App\Models\CenobotsClient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Tests\TestCase;

/** The "Проекти" slider on /services/cenobots and its admin (cenobots_clients table = projects). */
class CenobotsClientsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
        $user->email_verified_at = now();

        return $user;
    }

    private function media(string $name = 'logo'): Media
    {
        return Media::create([
            'model_type' => null, 'model_id' => null, 'collection_name' => 'cenobots-projects',
            'name' => $name, 'file_name' => $name . '.png', 'mime_type' => 'image/png', 'disk' => 'public',
            'size' => 10, 'manipulations' => [], 'custom_properties' => [],
            'generated_conversions' => [], 'responsive_images' => [],
        ]);
    }

    public function test_existing_projects_are_kept_and_shown_in_the_slider(): void
    {
        $this->assertSame(3, CenobotsClient::count(), 'the three original projects are seeded by the migration');

        $this->get('/services/cenobots')->assertOk()
            ->assertSee('data-cb-projects', false)
            ->assertSeeInOrder(['Diamond Mall', 'Limak Residence', 'NLB Tutunska Banka Macedonia'])
            ->assertSee('Шопинг центар')
            ->assertSee('Автономно машинско перење на заеднички површини')
            ->assertSee('CenoBots автономно чистење');
    }

    public function test_slider_shows_logo_photo_order_and_hides_inactive(): void
    {
        CenobotsClient::query()->delete();
        $logo = $this->media('acme-logo');
        $photo = $this->media('acme-photo');

        CenobotsClient::create(['name' => 'Second Co', 'sort_order' => 2, 'is_active' => true, 'website_url' => 'https://second.test']);
        CenobotsClient::create([
            'name' => 'First Co', 'category' => 'Hotel', 'description' => 'Floors cleaned at night.', 'foot_text' => 'Foot line',
            'logo_media_id' => $logo->id, 'logo_alt' => 'First logo', 'photo_media_id' => $photo->id, 'sort_order' => 1, 'is_active' => true,
        ]);
        CenobotsClient::create(['name' => 'Hidden Co', 'sort_order' => 3, 'is_active' => false]);

        $this->get('/services/cenobots')->assertOk()
            ->assertSeeInOrder(['First Co', 'Second Co'])
            ->assertSee('alt="First logo"', false)
            ->assertSee($logo->getUrl(), false)
            ->assertSee($photo->getUrl(), false)
            ->assertSee('Floors cleaned at night.')
            ->assertSee('href="https://second.test"', false)
            ->assertDontSee('Hidden Co');
    }

    public function test_original_cards_are_the_fallback_when_no_project_is_active(): void
    {
        CenobotsClient::query()->update(['is_active' => false]);

        $this->get('/services/cenobots')->assertOk()
            ->assertDontSee('data-cb-projects', false)
            ->assertSee('Diamond Mall')
            ->assertSee('Limak Residence');
    }

    public function test_admin_crud_toggle_and_reorder(): void
    {
        $this->actingAs($this->admin());
        $logo = $this->media('logo');
        $photo = $this->media('photo');

        $this->post(route('cenobots-clients.store'), [
            'name' => 'Acme', 'category' => 'Retail', 'description' => 'Desc', 'foot_text' => 'Foot',
            'logo_media_id' => $logo->id, 'photo_media_id' => $photo->id,
            'website_url' => 'https://acme.test', 'is_active' => 1, 'sort_order' => 10,
        ])->assertRedirect(route('cenobots-clients.index'));
        $project = CenobotsClient::where('name', 'Acme')->firstOrFail();
        $this->assertSame($photo->id, $project->photo_media_id);
        $this->assertSame('Retail', $project->category);

        // Logo and photo are optional; the name is required.
        $this->post(route('cenobots-clients.store'), ['name' => 'Plain'])->assertSessionHasNoErrors();
        $this->post(route('cenobots-clients.store'), ['category' => 'x'])->assertSessionHasErrors('name');
        $this->post(route('cenobots-clients.store'), ['name' => 'Bad', 'photo_media_id' => 99999])->assertSessionHasErrors('photo_media_id');

        $this->get(route('cenobots-clients.index'))->assertOk()->assertSee('Acme')->assertSee('Diamond Mall');
        $this->get(route('cenobots-clients.create'))->assertOk()->assertSee('Choose / Upload photo');
        $this->get(route('cenobots-clients.edit', $project))->assertOk();

        $this->put(route('cenobots-clients.update', $project), ['name' => 'Acme 2', 'logo_media_id' => $logo->id, 'photo_media_id' => '', 'is_active' => 0])->assertRedirect();
        $this->assertSame('Acme 2', $project->fresh()->name);
        $this->assertFalse($project->fresh()->is_active);
        $this->assertNull($project->fresh()->photo_media_id, 'clearing the photo field removes it');

        $this->patchJson(route('cenobots-clients.toggle', $project))->assertJson(['is_active' => true]);

        $other = CenobotsClient::where('name', 'Plain')->firstOrFail();
        $this->postJson(route('cenobots-clients.reorder'), ['order' => [$other->id, $project->id]])->assertOk();
        $this->assertSame(1, $other->fresh()->sort_order);

        $this->delete(route('cenobots-clients.destroy', $project))->assertRedirect();
        $this->assertDatabaseMissing('cenobots_clients', ['id' => $project->id]);
        $this->assertDatabaseHas('media', ['id' => $logo->id]);
    }

    public function test_admin_screens_are_not_public(): void
    {
        $this->get(route('cenobots-clients.index'))->assertRedirect();
        $this->actingAs(new User(['name' => 'User', 'email' => 'u@example.test', 'user_role' => 1]))
            ->get(route('cenobots-clients.index'))->assertForbidden();
    }
}

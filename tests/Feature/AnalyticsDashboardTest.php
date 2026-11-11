<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AnalyticsUnavailableException;
use App\Services\GoogleAnalyticsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return new User(['name' => 'Admin', 'email' => 'admin@example.test', 'user_role' => 2]);
    }

    private function sampleReport(): array
    {
        return [
            'period' => ['start' => '2026-11-01', 'end' => '2026-11-04'],
            'generated_at' => now()->toIso8601String(),
            'summary' => ['users' => 10, 'pageViews' => 40, 'sessions' => 15, 'sessionsPerUser' => 1.5, 'avgSessionDuration' => 95, 'bounceRate' => 42.5],
            'timeline' => [['date' => '2026-11-01', 'users' => 10, 'pageViews' => 40, 'sessions' => 15]],
            'topPages' => [], 'sources' => [], 'countries' => [], 'cities' => [], 'devices' => [], 'browsers' => [], 'operatingSystems' => [], 'userTypes' => [],
        ];
    }

    /** A service with GA4 configured whose API call is replaced by $buildReport. */
    private function serviceWith(callable $buildReport): GoogleAnalyticsService
    {
        $service = Mockery::mock(GoogleAnalyticsService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('configurationProblem')->andReturn(null);
        $service->shouldReceive('buildReport')->andReturnUsing($buildReport);

        return $service;
    }

    public function test_guests_are_sent_to_login(): void
    {
        $this->get(route('analytics.index'))->assertRedirect();
        $this->getJson(route('analytics.data', ['range' => '7d']))->assertUnauthorized();
    }

    public function test_non_admins_are_forbidden(): void
    {
        $this->actingAs(new User(['name' => 'User', 'email' => 'u@example.test', 'user_role' => 1]));

        $this->get(route('analytics.index'))->assertForbidden();
        $this->getJson(route('analytics.data', ['range' => '7d']))->assertForbidden();
    }

    public function test_admin_sees_the_page_menu_link_and_missing_configuration_notice(): void
    {
        config(['analytics.property_id' => null]);

        $this->actingAs($this->admin())->get(route('analytics.index'))
            ->assertOk()
            ->assertSee('Analytics is not configured', false)
            ->assertSee('ANALYTICS_PROPERTY_ID')
            ->assertSee(route('analytics.index'), false)
            ->assertSee('active bg-gradient-dark text-white', false);
    }

    public function test_menu_link_is_not_highlighted_on_other_pages(): void
    {
        $this->actingAs($this->admin())->get(route('health-check'))
            ->assertOk()
            ->assertSee(route('analytics.index'), false)
            ->assertDontSee('active bg-gradient-dark text-white', false);
    }

    public function test_data_reports_missing_property_id_without_breaking(): void
    {
        config(['analytics.property_id' => null]);

        $this->actingAs($this->admin())->getJson(route('analytics.data', ['range' => '7d']))
            ->assertStatus(503)->assertJson(['reason' => 'not_configured'])
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'ANALYTICS_PROPERTY_ID'));
    }

    public function test_data_reports_missing_and_invalid_credentials_file_without_leaking_the_path(): void
    {
        config(['analytics.property_id' => '123456', 'analytics.service_account_credentials_json' => storage_path('app/analytics/missing-secret-name.json')]);

        $response = $this->actingAs($this->admin())->getJson(route('analytics.data', ['range' => '7d']))->assertStatus(503);
        $this->assertStringNotContainsString('missing-secret-name', $response->getContent());

        $file = tempnam(sys_get_temp_dir(), 'ga');
        file_put_contents($file, '{"not":"a service account"}');
        config(['analytics.service_account_credentials_json' => $file]);

        $this->getJson(route('analytics.data', ['range' => '7d']))->assertStatus(503)->assertJsonPath('message', 'The Google service account credentials file is not valid.');
        unlink($file);

        file_put_contents($file, json_encode(['client_email' => 'a@b.iam.gserviceaccount.com', 'private_key' => 'k']));
        config(['analytics.service_account_credentials_json' => $file]);
        $this->assertNull(app(GoogleAnalyticsService::class)->configurationProblem());
        unlink($file);
    }

    public function test_validation_rejects_bad_ranges(): void
    {
        $this->actingAs($this->admin());
        $today = Carbon::today()->toDateString();

        $this->getJson(route('analytics.data', ['range' => 'forever']))->assertStatus(422);
        $this->getJson(route('analytics.data', ['range' => 'custom']))->assertStatus(422)->assertJsonValidationErrors(['start', 'end']);
        $this->getJson(route('analytics.data', ['range' => 'custom', 'start' => $today, 'end' => Carbon::yesterday()->toDateString()]))->assertStatus(422)->assertJsonValidationErrors('end');
        $this->getJson(route('analytics.data', ['range' => 'custom', 'start' => '04/11/2026', 'end' => $today]))->assertStatus(422)->assertJsonValidationErrors('start');
        $this->getJson(route('analytics.data', ['range' => 'custom', 'start' => $today, 'end' => Carbon::tomorrow()->toDateString()]))->assertStatus(422)->assertJsonValidationErrors('end');
        $this->getJson(route('analytics.data', ['range' => 'custom', 'start' => Carbon::today()->subDays(400)->toDateString(), 'end' => $today]))->assertStatus(422);
    }

    public function test_every_date_filter_asks_for_the_right_period(): void
    {
        Cache::flush();
        $periods = [];
        $this->app->instance(GoogleAnalyticsService::class, $this->serviceWith(function ($period) use (&$periods) {
            $periods[] = [$period->startDate->format('Y-m-d'), $period->endDate->format('Y-m-d')];

            return $this->sampleReport();
        }));
        $this->actingAs($this->admin());

        $today = Carbon::today();
        $custom = ['range' => 'custom', 'start' => '2026-10-01', 'end' => '2026-10-15'];

        foreach ([['range' => 'today'], ['range' => 'yesterday'], ['range' => '7d'], ['range' => '30d'], ['range' => '90d'], $custom] as $query) {
            $this->getJson(route('analytics.data', $query))->assertOk()->assertJsonPath('summary.users', 10);
        }

        $this->assertSame([
            [$today->toDateString(), $today->toDateString()],
            [$today->copy()->subDay()->toDateString(), $today->copy()->subDay()->toDateString()],
            [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
            [$today->copy()->subDays(29)->toDateString(), $today->toDateString()],
            [$today->copy()->subDays(89)->toDateString(), $today->toDateString()],
            ['2026-10-01', '2026-10-15'],
        ], $periods);
    }

    public function test_reports_are_cached_and_fresh_bypasses_the_cache(): void
    {
        Cache::flush();
        $calls = 0;
        $this->app->instance(GoogleAnalyticsService::class, $this->serviceWith(function () use (&$calls) {
            $calls++;

            return $this->sampleReport();
        }));
        $this->actingAs($this->admin());

        $this->getJson(route('analytics.data', ['range' => '7d']))->assertOk();
        $this->getJson(route('analytics.data', ['range' => '7d']))->assertOk();
        $this->assertSame(1, $calls, 'second request must come from the cache');

        $this->getJson(route('analytics.data', ['range' => '30d']))->assertOk();
        $this->assertSame(2, $calls, 'a different range is a different cache entry');

        $this->getJson(route('analytics.data', ['range' => '7d', 'fresh' => 1]))->assertOk();
        $this->assertSame(3, $calls, 'refresh must query GA4 again');
    }

    public function test_google_errors_become_a_clean_message_and_are_not_cached(): void
    {
        Cache::flush();
        $fail = true;
        $calls = 0;
        $this->app->instance(GoogleAnalyticsService::class, $this->serviceWith(function () use (&$fail, &$calls) {
            $calls++;
            if ($fail) {
                throw new \RuntimeException('PERMISSION_DENIED: secret detail');
            }

            return $this->sampleReport();
        }));
        $this->actingAs($this->admin());

        $response = $this->getJson(route('analytics.data', ['range' => '7d']))->assertStatus(502)->assertJson(['reason' => 'api_error']);
        $this->assertStringNotContainsString('secret detail', $response->getContent());

        $fail = false;
        $this->getJson(route('analytics.data', ['range' => '7d']))->assertOk();
        $this->assertSame(2, $calls, 'a failure must not be cached');

        // The rest of the admin panel is unaffected.
        $this->get(route('home'))->assertOk();
    }

    public function test_report_is_shaped_from_ga4_rows(): void
    {
        $ga = Mockery::mock(\Spatie\Analytics\Analytics::class);
        $ga->shouldReceive('get')->andReturnUsing(function ($period, $metrics, $dimensions = []) {
            return match (true) {
                $dimensions === [] => collect([['activeUsers' => 12, 'screenPageViews' => '48', 'sessions' => '20', 'sessionsPerUser' => '1.6666', 'averageSessionDuration' => '125.4', 'bounceRate' => '0.4123']]),
                $dimensions === ['date'] => collect([['date' => Carbon::create(2026, 11, 1), 'activeUsers' => 5, 'screenPageViews' => '20', 'sessions' => '8']]),
                default => collect([array_merge(array_fill_keys($dimensions, 'x'), array_fill_keys($metrics, '3'))]),
            };
        });
        $service = Mockery::mock(GoogleAnalyticsService::class)->makePartial()->shouldAllowMockingProtectedMethods();
        $service->shouldReceive('analytics')->andReturn($ga);

        $report = $service->buildReport(\Spatie\Analytics\Period::create(Carbon::create(2026, 11, 1), Carbon::create(2026, 11, 4)));

        $this->assertSame(['users' => 12, 'pageViews' => 48, 'sessions' => 20, 'sessionsPerUser' => 1.67, 'avgSessionDuration' => 125, 'bounceRate' => 41.2], $report['summary']);
        $this->assertSame([['date' => '2026-11-01', 'users' => 5, 'pageViews' => 20, 'sessions' => 8]], $report['timeline']);
        $this->assertSame(3, $report['topPages'][0]['screenPageViews']);
        $this->assertSame('x', $report['devices'][0]['deviceCategory']);
        foreach (['topPages', 'sources', 'countries', 'cities', 'devices', 'browsers', 'operatingSystems', 'userTypes'] as $key) {
            $this->assertCount(1, $report[$key], $key);
        }
    }

    public function test_unavailable_exception_message_is_passed_through(): void
    {
        $service = Mockery::mock(GoogleAnalyticsService::class);
        $service->shouldReceive('configurationProblem')->andReturn(null);
        $service->shouldReceive('report')->andThrow(new AnalyticsUnavailableException('Nope.', 'api_error'));
        $this->app->instance(GoogleAnalyticsService::class, $service);

        $this->actingAs($this->admin())->getJson(route('analytics.data', ['range' => '7d']))->assertStatus(502)->assertJsonPath('message', 'Nope.');
    }
}

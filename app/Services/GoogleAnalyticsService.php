<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\AnalyticsClientFactory;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

/**
 * Talks to Google Analytics 4 through spatie/laravel-analytics and shapes the
 * results for the admin dashboard. Every failure surfaces as an
 * AnalyticsUnavailableException carrying a message that is safe to show.
 */
class GoogleAnalyticsService
{
    public function isConfigured(): bool
    {
        return $this->configurationProblem() === null;
    }

    /** A user-safe reason the integration is not usable, or null when it is. */
    public function configurationProblem(): ?string
    {
        if (blank(config('analytics.property_id'))) {
            return 'The GA4 property ID is missing. Set ANALYTICS_PROPERTY_ID in your .env file.';
        }

        $path = config('analytics.service_account_credentials_json');

        if (! is_string($path) || ! is_file($path) || ! is_readable($path)) {
            return 'The Google service account credentials file was not found. Place it at the path set in ANALYTICS_CREDENTIALS_PATH.';
        }

        $credentials = json_decode((string) file_get_contents($path), true);

        if (! is_array($credentials) || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            return 'The Google service account credentials file is not valid.';
        }

        return null;
    }

    /**
     * Full dashboard report for a period, cached for the configured lifetime.
     *
     * @throws AnalyticsUnavailableException
     */
    public function report(Carbon $start, Carbon $end, bool $fresh = false): array
    {
        if ($problem = $this->configurationProblem()) {
            throw new AnalyticsUnavailableException($problem, 'not_configured');
        }

        $key = sprintf('analytics:report:%s:%s:%s', config('analytics.property_id'), $start->toDateString(), $end->toDateString());

        if ($fresh) {
            Cache::forget($key);
        }

        $minutes = max(1, (int) config('analytics.cache_lifetime_in_minutes'));

        try {
            return Cache::remember($key, now()->addMinutes($minutes), fn () => $this->buildReport(Period::create($start, $end)));
        } catch (AnalyticsUnavailableException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::warning('Google Analytics request failed: ' . $e->getMessage(), ['exception' => $e::class]);

            throw new AnalyticsUnavailableException('Google Analytics could not be reached or rejected the request. Check the service account access and property ID, then try again.', 'api_error', $e);
        }
    }

    protected function buildReport(Period $period): array
    {
        $analytics = $this->analytics();

        $summary = $analytics->get($period, ['activeUsers', 'screenPageViews', 'sessions', 'sessionsPerUser', 'averageSessionDuration', 'bounceRate'])->first() ?? [];

        $byDate = $analytics->get(
            $period,
            ['activeUsers', 'screenPageViews', 'sessions'],
            ['date'],
            1000,
            [OrderBy::dimension('date')],
        )->map(fn ($row) => [
            'date' => $row['date']->format('Y-m-d'),
            'users' => (int) $row['activeUsers'],
            'pageViews' => (int) $row['screenPageViews'],
            'sessions' => (int) $row['sessions'],
        ])->values()->all();

        return [
            'period' => ['start' => $period->startDate->format('Y-m-d'), 'end' => $period->endDate->format('Y-m-d')],
            'generated_at' => now()->toIso8601String(),
            'summary' => [
                'users' => (int) ($summary['activeUsers'] ?? 0),
                'pageViews' => (int) ($summary['screenPageViews'] ?? 0),
                'sessions' => (int) ($summary['sessions'] ?? 0),
                'sessionsPerUser' => round((float) ($summary['sessionsPerUser'] ?? 0), 2),
                'avgSessionDuration' => (int) round((float) ($summary['averageSessionDuration'] ?? 0)),
                'bounceRate' => round((float) ($summary['bounceRate'] ?? 0) * 100, 1),
            ],
            'timeline' => $byDate,
            'topPages' => $this->breakdown($analytics, $period, ['pagePath', 'pageTitle'], ['screenPageViews', 'activeUsers'], 10, 'screenPageViews'),
            'sources' => $this->breakdown($analytics, $period, ['sessionDefaultChannelGroup'], ['sessions', 'activeUsers'], 10, 'sessions'),
            'countries' => $this->breakdown($analytics, $period, ['country'], ['activeUsers', 'sessions'], 10, 'activeUsers'),
            'cities' => $this->breakdown($analytics, $period, ['city'], ['activeUsers', 'sessions'], 10, 'activeUsers'),
            'devices' => $this->breakdown($analytics, $period, ['deviceCategory'], ['activeUsers', 'sessions'], 10, 'activeUsers'),
            'browsers' => $this->breakdown($analytics, $period, ['browser'], ['activeUsers', 'sessions'], 10, 'activeUsers'),
            'operatingSystems' => $this->breakdown($analytics, $period, ['operatingSystem'], ['activeUsers', 'sessions'], 10, 'activeUsers'),
            'userTypes' => $this->breakdown($analytics, $period, ['newVsReturning'], ['activeUsers', 'sessions'], 5, 'activeUsers'),
        ];
    }

    /** One GA4 report as a plain list of rows: dimension values plus integer metrics. */
    protected function breakdown(Analytics $analytics, Period $period, array $dimensions, array $metrics, int $limit, string $orderMetric): array
    {
        return $analytics->get($period, $metrics, $dimensions, $limit, [OrderBy::metric($orderMetric, true)])
            ->map(function (array $row) use ($metrics) {
                foreach ($metrics as $metric) {
                    $row[$metric] = (int) $row[$metric];
                }

                return $row;
            })
            ->values()
            ->all();
    }

    /** Analytics instance whose own cache is off; the report is cached as a whole. */
    protected function analytics(): Analytics
    {
        $config = config('analytics');
        $config['cache_lifetime_in_minutes'] = 0;

        return new Analytics(AnalyticsClientFactory::createForConfig($config), (string) $config['property_id']);
    }
}

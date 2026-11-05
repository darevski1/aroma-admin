<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsUnavailableException;
use App\Services\GoogleAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnalyticsController extends Controller
{
    public const RANGES = [
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        '7d' => 'Last 7 Days',
        '30d' => 'Last 30 Days',
        '90d' => 'Last 90 Days',
        'custom' => 'Custom Range',
    ];

    public function __construct(private GoogleAnalyticsService $analytics)
    {
    }

    public function index()
    {
        return view('admin_pages.analytics.index', [
            'ranges' => self::RANGES,
            'configurationProblem' => $this->analytics->configurationProblem(),
            'maxRangeDays' => (int) config('analytics.max_range_days'),
        ]);
    }

    /** JSON report for the selected date range. */
    public function data(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'range' => ['required', Rule::in(array_keys(self::RANGES))],
            'start' => ['required_if:range,custom', 'nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'end' => ['required_if:range,custom', 'nullable', 'date_format:Y-m-d', 'after_or_equal:start', 'before_or_equal:today'],
            'fresh' => ['nullable', 'boolean'],
        ]);

        [$start, $end] = $this->resolveRange($validated);

        if ($start->diffInDays($end) > (int) config('analytics.max_range_days')) {
            return response()->json([
                'message' => 'The date range cannot be longer than ' . config('analytics.max_range_days') . ' days.',
                'errors' => ['end' => ['The date range is too long.']],
            ], 422);
        }

        try {
            $report = $this->analytics->report($start, $end, $request->boolean('fresh'));
        } catch (AnalyticsUnavailableException $e) {
            return response()->json(['message' => $e->getMessage(), 'reason' => $e->reason], $e->reason === 'not_configured' ? 503 : 502);
        }

        return response()->json($report);
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function resolveRange(array $input): array
    {
        $today = Carbon::today();

        return match ($input['range']) {
            'today' => [$today, $today],
            'yesterday' => [$today->copy()->subDay(), $today->copy()->subDay()],
            '7d' => [$today->copy()->subDays(6), $today],
            '30d' => [$today->copy()->subDays(29), $today],
            '90d' => [$today->copy()->subDays(89), $today],
            'custom' => [Carbon::createFromFormat('Y-m-d', $input['start'])->startOfDay(), Carbon::createFromFormat('Y-m-d', $input['end'])->startOfDay()],
        };
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\Health\Commands\RunHealthChecksCommand;
use Spatie\Health\ResultStores\ResultStore;

class HealthController extends Controller
{
    public function index(Request $request, ResultStore $resultStore)
    {
        if ($request->has('fresh')) {
            Artisan::call(RunHealthChecksCommand::class);
        }

        $checkResults = $resultStore->latestResults();

        return view('admin_pages.health.index', [
            'checkResults' => $checkResults,
            'lastRanAt' => $checkResults?->finishedAt,
        ]);
    }
}

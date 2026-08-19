@extends('layouts.master')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Health Check</h3>
            <p class="mb-4">
                Monitor application health and system status.
            </p>
        </div>
    </div>

    {{-- Refresh Button --}}
    <div class="row mb-3">
        <div class="col-12 ms-3">
            <a href="{{ route('health-check', ['fresh' => 1]) }}" class="btn btn-sm btn-outline-dark">
                <i class="material-symbols-rounded me-1" style="font-size: 16px;">refresh</i>
                Refresh Checks
            </a>
            @if ($lastRanAt)
                <span class="ms-2 text-sm text-muted">
                    Last checked {{ $lastRanAt->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>

    {{-- Health Check Results --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h6>Check Results</h6>
                </div>
                <div class="card-body px-0 pb-2">
                    @if ($checkResults && count($checkResults->storedCheckResults ?? []))
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Check</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Summary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($checkResults->storedCheckResults as $result)
                                        <tr>
                                            <td class="align-middle">
                                                @if ($result->status === 'ok')
                                                    <span class="badge bg-gradient-success">OK</span>
                                                @elseif ($result->status === 'warning')
                                                    <span class="badge bg-gradient-warning text-dark">Warning</span>
                                                @else
                                                    <span class="badge bg-gradient-danger">Failed</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $result->label }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-xs">
                                                    @if (!empty($result->notificationMessage))
                                                        {{ $result->notificationMessage }}
                                                    @else
                                                        {{ $result->shortSummary }}
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No health check results yet.</p>
                            <a href="{{ route('health-check', ['fresh' => 1]) }}" class="btn btn-sm btn-dark">
                                Run Health Checks Now
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

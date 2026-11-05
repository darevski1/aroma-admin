@extends('layouts.master')

@section('title', 'Analytics')

@section('content')
<div class="container-fluid py-2">
    <div class="row">
        <div class="ms-3">
            <h3 class="mb-0 h4 font-weight-bolder">Analytics</h3>
            <p class="mb-4">Website traffic from Google Analytics 4.</p>
        </div>
    </div>

    {{-- Date filters --}}
    <div class="card mb-4">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center gap-2" id="analyticsFilters">
                @foreach ($ranges as $value => $label)
                    <button type="button" class="btn btn-sm mb-0 {{ $value === '30d' ? 'bg-gradient-dark' : 'btn-outline-dark' }}" data-range="{{ $value }}">{{ $label }}</button>
                @endforeach

                <form id="analyticsCustom" class="d-none flex-wrap align-items-center gap-2 ms-lg-2">
                    <input type="date" id="analyticsStart" class="form-control form-control-sm w-auto" style="border:1px solid #d2d6da;padding:.35rem .6rem;" max="{{ now()->toDateString() }}" required>
                    <span class="text-sm">to</span>
                    <input type="date" id="analyticsEnd" class="form-control form-control-sm w-auto" style="border:1px solid #d2d6da;padding:.35rem .6rem;" max="{{ now()->toDateString() }}" required>
                    <button type="submit" class="btn btn-sm bg-gradient-dark mb-0">Apply</button>
                </form>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <span class="text-xs text-muted" id="analyticsUpdated"></span>
                    <button type="button" class="btn btn-sm btn-outline-secondary mb-0" id="analyticsRefresh" title="Fetch fresh data from Google Analytics">
                        <i class="material-symbols-rounded me-1" style="font-size:16px;">refresh</i> Refresh
                    </button>
                </div>
            </div>
            <div class="text-danger text-xs mt-2 d-none" id="analyticsRangeError"></div>
        </div>
    </div>

    {{-- Configuration / API errors --}}
    <div class="alert alert-danger text-white text-sm d-none" id="analyticsError" role="alert"></div>
    @if ($configurationProblem)
        <div class="alert alert-warning text-white text-sm" role="alert" id="analyticsNotConfigured">
            <strong>Google Analytics is not configured.</strong> {{ $configurationProblem }}
        </div>
    @endif

    <div id="analyticsDashboard" class="{{ $configurationProblem ? 'd-none' : '' }}">
        {{-- Loading --}}
        <div class="text-center text-muted py-5 d-none" id="analyticsLoading">
            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Loading analytics…
        </div>

        <div id="analyticsContent">
            {{-- Overview cards --}}
            <div class="row" id="analyticsCards">
                @foreach ([
                    ['users', 'Total Users', 'group'],
                    ['pageViews', 'Page Views', 'visibility'],
                    ['sessions', 'Sessions', 'timeline'],
                    ['sessionsPerUser', 'Sessions per User', 'repeat'],
                    ['avgSessionDuration', 'Avg. Session Duration', 'schedule'],
                    ['bounceRate', 'Bounce Rate', 'logout'],
                ] as [$key, $label, $icon])
                    <div class="col-xl-2 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-sm mb-0">{{ $label }}</p>
                                        <h4 class="mb-0" data-card="{{ $key }}">—</h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">{{ $icon }}</i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Charts --}}
            <div class="row">
                @foreach ([['users', 'Visitors over time'], ['pageViews', 'Page views over time'], ['sessions', 'Sessions over time']] as [$key, $label])
                    <div class="col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="mb-3">{{ $label }}</h6>
                                <div style="position:relative;height:220px;"><canvas id="chart-{{ $key }}"></canvas></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-3">Traffic trends</h6>
                            <div style="position:relative;height:300px;"><canvas id="chart-trends"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Reports --}}
            <div class="row" id="analyticsReports">
                @foreach ([
                    ['topPages', 'Top Viewed Pages', 'col-lg-12'],
                    ['sources', 'Traffic Sources', 'col-lg-6'],
                    ['userTypes', 'New vs Returning Visitors', 'col-lg-6'],
                    ['countries', 'Countries', 'col-lg-6'],
                    ['cities', 'Cities', 'col-lg-6'],
                    ['devices', 'Devices', 'col-lg-4'],
                    ['browsers', 'Browsers', 'col-lg-4'],
                    ['operatingSystems', 'Operating Systems', 'col-lg-4'],
                ] as [$key, $label, $col])
                    <div class="{{ $col }} mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="mb-3">{{ $label }}</h6>
                                <div class="table-responsive" data-report="{{ $key }}"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var dataUrl = @json(route('analytics.data'));
    var maxDays = @json($maxRangeDays);
    var configured = @json($configurationProblem === null);
    var state = { range: '30d' };
    var charts = {};
    var requestToken = 0;

    var $ = function (id) { return document.getElementById(id); };
    var content = $('analyticsContent'), loading = $('analyticsLoading'), errorBox = $('analyticsError');
    var nf = new Intl.NumberFormat();

    function esc(v) { var d = document.createElement('div'); d.textContent = v == null ? '' : String(v); return d.innerHTML; }
    function duration(s) { return Math.floor(s / 60) + 'm ' + String(s % 60).padStart(2, '0') + 's'; }

    function setActive(range) {
        document.querySelectorAll('#analyticsFilters [data-range]').forEach(function (b) {
            var on = b.getAttribute('data-range') === range;
            b.classList.toggle('bg-gradient-dark', on);
            b.classList.toggle('btn-outline-dark', !on);
        });
        $('analyticsCustom').classList.toggle('d-none', range !== 'custom');
        $('analyticsCustom').classList.toggle('d-flex', range === 'custom');
    }

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('d-none');
        content.classList.add('d-none');
    }

    function drawChart(id, labels, datasets, opts) {
        if (charts[id]) charts[id].destroy();
        charts[id] = new Chart($(id).getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: Object.assign({
                responsive: true, maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: datasets.length > 1 } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            }, opts || {}),
        });
    }

    function series(label, color, values) {
        return { label: label, data: values, borderColor: color, backgroundColor: color + '22', fill: true, tension: .3, pointRadius: values.length > 45 ? 0 : 3 };
    }

    var columns = {
        topPages: [['pagePath', 'Page', 'pageTitle'], ['screenPageViews', 'Views'], ['activeUsers', 'Users']],
        sources: [['sessionDefaultChannelGroup', 'Channel'], ['sessions', 'Sessions'], ['activeUsers', 'Users']],
        userTypes: [['newVsReturning', 'Visitor type'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
        countries: [['country', 'Country'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
        cities: [['city', 'City'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
        devices: [['deviceCategory', 'Device'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
        browsers: [['browser', 'Browser'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
        operatingSystems: [['operatingSystem', 'OS'], ['activeUsers', 'Users'], ['sessions', 'Sessions']],
    };

    function renderTable(key, rows) {
        var host = document.querySelector('[data-report="' + key + '"]');
        if (!rows || !rows.length) {
            host.innerHTML = '<p class="text-sm text-muted mb-0 py-3">No data for this period.</p>';
            return;
        }
        var cols = columns[key];
        var html = '<table class="table align-items-center mb-0"><thead><tr>' + cols.map(function (c, i) {
            return '<th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7' + (i ? ' text-end' : '') + '">' + c[1] + '</th>';
        }).join('') + '</tr></thead><tbody>';
        rows.forEach(function (row) {
            html += '<tr>' + cols.map(function (c, i) {
                var v = row[c[0]];
                if (i) return '<td class="text-sm text-end">' + nf.format(v) + '</td>';
                var sub = c[2] && row[c[2]] && row[c[2]] !== '(not set)' ? '<div class="text-xs text-muted text-truncate" style="max-width:420px;">' + esc(row[c[2]]) + '</div>' : '';
                return '<td class="text-sm"><div class="text-truncate" style="max-width:420px;">' + esc(v || '(not set)') + '</div>' + sub + '</td>';
            }).join('') + '</tr>';
        });
        host.innerHTML = html + '</tbody></table>';
    }

    function render(data) {
        var s = data.summary;
        document.querySelector('[data-card="users"]').textContent = nf.format(s.users);
        document.querySelector('[data-card="pageViews"]').textContent = nf.format(s.pageViews);
        document.querySelector('[data-card="sessions"]').textContent = nf.format(s.sessions);
        document.querySelector('[data-card="sessionsPerUser"]').textContent = s.sessionsPerUser.toFixed(2);
        document.querySelector('[data-card="avgSessionDuration"]').textContent = duration(s.avgSessionDuration);
        document.querySelector('[data-card="bounceRate"]').textContent = s.bounceRate.toFixed(1) + '%';

        var labels = data.timeline.map(function (r) { return r.date; });
        var col = function (k) { return data.timeline.map(function (r) { return r[k]; }); };
        drawChart('chart-users', labels, [series('Visitors', '#344767', col('users'))]);
        drawChart('chart-pageViews', labels, [series('Page views', '#1A73E8', col('pageViews'))]);
        drawChart('chart-sessions', labels, [series('Sessions', '#4CAF50', col('sessions'))]);
        drawChart('chart-trends', labels, [
            series('Visitors', '#344767', col('users')),
            series('Page views', '#1A73E8', col('pageViews')),
            series('Sessions', '#4CAF50', col('sessions')),
        ]);

        Object.keys(columns).forEach(function (key) { renderTable(key, data[key]); });

        $('analyticsUpdated').textContent = data.period.start + ' → ' + data.period.end + ' · updated ' + new Date(data.generated_at).toLocaleTimeString();
    }

    function load(fresh) {
        if (!configured) return;
        var params = new URLSearchParams({ range: state.range });
        if (state.range === 'custom') { params.set('start', state.start); params.set('end', state.end); }
        if (fresh) params.set('fresh', '1');

        var token = ++requestToken;
        errorBox.classList.add('d-none');
        $('analyticsRangeError').classList.add('d-none');
        content.classList.add('d-none');
        loading.classList.remove('d-none');

        fetch(dataUrl + '?' + params, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json().catch(function () { return {}; }).then(function (body) { return { ok: r.ok, status: r.status, body: body }; }); })
            .then(function (res) {
                if (token !== requestToken) return;
                loading.classList.add('d-none');
                if (!res.ok) {
                    var msg = res.body.message || 'Analytics could not be loaded (HTTP ' + res.status + ').';
                    if (res.status === 422) { $('analyticsRangeError').textContent = msg; $('analyticsRangeError').classList.remove('d-none'); }
                    else showError(msg);
                    return;
                }
                content.classList.remove('d-none');
                render(res.body);
            })
            .catch(function () {
                if (token !== requestToken) return;
                loading.classList.add('d-none');
                showError('Analytics could not be loaded. Check your connection and try again.');
            });
    }

    document.querySelectorAll('#analyticsFilters [data-range]').forEach(function (b) {
        b.addEventListener('click', function () {
            state.range = b.getAttribute('data-range');
            setActive(state.range);
            if (state.range !== 'custom') load(false);
        });
    });

    $('analyticsCustom').addEventListener('submit', function (e) {
        e.preventDefault();
        var start = $('analyticsStart').value, end = $('analyticsEnd').value, err = $('analyticsRangeError');
        err.classList.add('d-none');
        if (!start || !end) return;
        if (start > end) { err.textContent = 'The start date must be before the end date.'; err.classList.remove('d-none'); return; }
        if ((new Date(end) - new Date(start)) / 86400000 > maxDays) { err.textContent = 'The date range cannot be longer than ' + maxDays + ' days.'; err.classList.remove('d-none'); return; }
        state.start = start; state.end = end;
        load(false);
    });

    $('analyticsRefresh').addEventListener('click', function () {
        if (state.range === 'custom' && !state.start) return;
        load(true);
    });

    setActive(state.range);
    load(false);
});
</script>
@endpush

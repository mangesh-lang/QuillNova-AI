@extends('layouts.app')
@section('title', 'Admin Overview')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">SaaS Admin Overview</h1>
        <p class="page-subtitle mb-0">Track platform users, compute generated words, monitor AI requests, and analyze logs.</p>
    </div>
    
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users') }}" class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;">
            <i class="bi bi-people"></i> Manage Users
        </a>
        <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary border-custom text-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2">
            <i class="bi bi-sliders"></i> Configuration
        </a>
    </div>
</div>

<!-- Admin Counter Widgets -->
<div class="row g-4 mb-4">
    <!-- Total Users -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="glass-card p-3.5 h-100 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);">
                <i class="bi bi-people fs-4"></i>
            </div>
            <div>
                <span class="text-muted-custom fw-semibold d-block" style="font-size: 0.8rem;">Total Users</span>
                <h4 class="fw-bold mb-0 text-primary">{{ $totalUsers }}</h4>
            </div>
        </div>
    </div>

    <!-- Active Users Today -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="glass-card p-3.5 h-100 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(16, 185, 129, 0.1); color: #10b981;">
                <i class="bi bi-person-check fs-4"></i>
            </div>
            <div>
                <span class="text-muted-custom fw-semibold d-block" style="font-size: 0.8rem;">Active Today</span>
                <h4 class="fw-bold mb-0 text-primary">{{ $activeUsersToday }}</h4>
            </div>
        </div>
    </div>

    <!-- Generated Words -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="glass-card p-3.5 h-100 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i class="bi bi-file-earmark-word fs-4"></i>
            </div>
            <div>
                <span class="text-muted-custom fw-semibold d-block" style="font-size: 0.8rem;">Total Words</span>
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalWords) }}</h4>
            </div>
        </div>
    </div>

    <!-- AI Requests -->
    <div class="col-12 col-sm-6 col-md-3">
        <div class="glass-card p-3.5 h-100 d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="bi bi-cpu fs-4"></i>
            </div>
            <div>
                <span class="text-muted-custom fw-semibold d-block" style="font-size: 0.8rem;">Total Calls</span>
                <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalRequests) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart Graph -->
    <div class="col-12 col-xl-8">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-graph-up text-primary me-2"></i> System Analytics (15 Days)</h5>
            <div style="position: relative; height: 320px; width: 100%;">
                <canvas id="systemChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Users List -->
    <div class="col-12 col-xl-4">
        <div class="glass-card p-4 h-100">
            <h5 class="fw-bold mb-3"><i class="bi bi-award text-warning me-2"></i> Top Active Users</h5>
            <div class="d-flex flex-column gap-3">
                @forelse($topUsers as $user)
                    <div class="p-3 border rounded-3 d-flex align-items-center justify-content-between border-custom bg-secondary-hover">
                        <div>
                            <h6 class="fw-bold mb-0 text-primary">{{ $user->name }}</h6>
                            <small class="text-muted-custom" style="font-size: 0.775rem;">{{ $user->email }}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-primary d-block" style="font-size: 0.9rem;">{{ number_format($user->total_words) }} w</span>
                            <small class="text-muted-custom" style="font-size: 0.75rem;">{{ $user->total_generations }} docs</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted-custom">No active usage logs.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('systemChart').getContext('2d');
        const labels = {!! json_encode($analytics['labels']) !!};
        const wordsData = {!! json_encode($analytics['words']) !!};
        const requestsData = {!! json_encode($analytics['requests']) !!};

        const isDark = document.documentElement.classList.contains('dark-theme');
        const gridColor = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.05)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Total Words',
                        data: wordsData,
                        borderColor: '#818cf8',
                        backgroundColor: 'rgba(129, 140, 248, 0.08)',
                        fill: true,
                        tension: 0.35,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Total Calls',
                        data: requestsData,
                        borderColor: '#10b981',
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: textColor } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: textColor } },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: gridColor },
                        ticks: { color: textColor },
                        title: { display: true, text: 'Words', color: textColor }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: textColor },
                        title: { display: true, text: 'Requests', color: textColor }
                    }
                }
            }
        });
    });
</script>
@endsection

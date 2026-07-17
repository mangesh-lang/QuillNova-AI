@extends('layouts.app')
@section('title', 'Dashboard')

@section('styles')
<style>
    /* Greeting Banner Container styling */
    .greeting-banner {
        background: linear-gradient(135deg, rgba(30, 27, 75, 0.9), rgba(15, 23, 42, 0.95));
        border: 1px solid rgba(129, 140, 248, 0.15);
        border-radius: 24px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
    }
    .greeting-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        pointer-events: none;
    }
    .greeting-robot {
        width: 130px;
        height: 130px;
        object-fit: contain;
        filter: drop-shadow(0 8px 16px rgba(99, 102, 241, 0.3));
        animation: float-mascot 4s infinite ease-in-out;
        user-select: none;
        pointer-events: none;
    }
    @keyframes float-mascot {
        0%, 100% { transform: translateY(0) rotate(0); }
        50% { transform: translateY(-8px) rotate(1deg); }
    }

    /* Stat Cards Styling wrappers */
    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .bg-indigo-glow { background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2); }
    .bg-purple-glow { background: rgba(139, 92, 246, 0.12); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.2); }
    .bg-blue-glow { background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .bg-pink-glow { background: rgba(236, 72, 153, 0.12); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.2); }
    .bg-emerald-glow { background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
    .bg-orange-glow { background: rgba(249, 115, 22, 0.12); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.2); }
    .bg-cyan-glow { background: rgba(6, 182, 212, 0.12); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.2); }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        font-weight: 500;
        display: block;
    }
    .stat-val {
        font-size: 1.2rem;
        font-weight: 800;
        color: var(--text-primary);
        margin: 2px 0;
        letter-spacing: -0.02em;
    }
    .stat-sub {
        font-size: 0.72rem;
        color: var(--text-secondary);
        display: block;
    }

    /* Quick Tools Grid styling */
    .quick-tool-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 16px 8px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--glass-border);
        transition: var(--transition-smooth);
        text-decoration: none !important;
    }
    .dark-theme .quick-tool-btn {
        background: rgba(255, 255, 255, 0.01);
    }
    .quick-tool-btn:hover {
        background: rgba(99, 102, 241, 0.08) !important;
        border-color: var(--accent-primary) !important;
        transform: translateY(-4px) scale(1.02);
    }
    .tool-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        margin-bottom: 8px;
    }
    .quick-tool-btn span {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.25;
        display: block;
    }

    .badge-blog { background: rgba(6, 182, 212, 0.12); color: #22d3ee; border: 1px solid rgba(6, 182, 212, 0.2); }
    .badge-email { background: rgba(16, 185, 129, 0.12); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-chat { background: rgba(139, 92, 246, 0.12); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.2); }
    .badge-general { background: rgba(99, 102, 241, 0.12); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2); }
</style>
@endsection

@section('content')
<!-- Time of day greeting calculator -->
@php
    $hour = date('H');
    if ($hour < 12) {
        $greeting = 'Good Morning';
    } elseif ($hour < 17) {
        $greeting = 'Good Afternoon';
    } else {
        $greeting = 'Good Evening';
    }
@endphp

<!-- Welcome Greeting Banner -->
<div class="greeting-banner d-flex justify-content-between align-items-center mb-4 p-4 text-white">
    <div class="z-1">
        <h2 class="fw-bold mb-1" style="letter-spacing: -0.02em;">{{ $greeting }}, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
        <p class="mb-3 text-white-50" style="font-size: 0.95rem;">Let's create something amazing with AI today.</p>
        <a href="{{ route('templates.index') }}" class="btn btn-primary d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold border-0">
            <i class="bi bi-grid-3x3-gap"></i> Browse All Templates
        </a>
    </div>
    
    <div class="d-none d-md-block z-1">
        <img src="{{ asset('images/dashboard-robot.png') }}" class="greeting-robot" alt="AI Mascot Robot">
    </div>
</div>

<!-- Limit Exceeded Alert message if necessary -->
@if($remainingRequests <= 0 || $remainingWords <= 0)
    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning rounded-4 p-4 mb-4 d-flex align-items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-3"></i>
        <div>
            <h5 class="fw-bold mb-1">Daily Limits Reached</h5>
            <p class="mb-0 text-muted-custom" style="font-size: 0.95rem;">
                You have reached your free account limits for today. Please wait for the auto-reset or upgrade to QuillNova Pro.
            </p>
        </div>
    </div>
@endif

<!-- Four Stat Metrics Row -->
<div class="row g-4 mb-4">
    <!-- Metric 1: Requests -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon-wrapper bg-indigo-glow">
                <i class="bi bi-lightning-charge-fill"></i>
            </div>
            <div>
                <span class="stat-label">Today's AI Requests</span>
                <h5 class="stat-val mb-0">{{ $todayRequests }} / {{ $maxRequests }}</h5>
                <span class="stat-sub">{{ $remainingRequests }} remaining</span>
            </div>
        </div>
    </div>

    <!-- Metric 2: Words -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon-wrapper bg-purple-glow">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div>
                <span class="stat-label">Words Generated</span>
                <h5 class="stat-val mb-0">{{ number_format($todayWords) }} / {{ number_format($maxWords) }}</h5>
                <span class="stat-sub">{{ number_format($remainingWords) }} remaining</span>
            </div>
        </div>
    </div>

    <!-- Metric 3: Output Documents count -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon-wrapper bg-blue-glow">
                <i class="bi bi-folder-fill"></i>
            </div>
            <div>
                <span class="stat-label">Documents Created</span>
                <h5 class="stat-val mb-0">{{ $totalGeneratedCount }}</h5>
                <span class="stat-sub">Total documents</span>
            </div>
        </div>
    </div>

    <!-- Metric 4: Star Favorites -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="glass-card p-3 d-flex align-items-center gap-3">
            <div class="stat-icon-wrapper bg-pink-glow">
                <i class="bi bi-star-fill"></i>
            </div>
            <div>
                <span class="stat-label">Favorite Templates</span>
                <h5 class="stat-val mb-0">{{ count($favoriteTemplates) }}</h5>
                <span class="stat-sub">Total favorites</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Usage Chart Line Graph -->
    <div class="col-12 col-xl-8">
        <div class="glass-card p-4 h-100">
            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-graph-up text-primary me-2"></i> Weekly Usage Overview</h6>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="usageChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Tools 6-grid Area -->
    <div class="col-12 col-xl-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-grid-fill text-accent-primary me-2"></i> Quick Tools</h6>
                <a href="{{ route('templates.index') }}" class="text-decoration-none fw-semibold text-muted-custom small">View All</a>
            </div>
            
            <div class="row g-2">
                <div class="col-4">
                    <a href="{{ route('templates.index') }}?category=seo-content" class="quick-tool-btn">
                        <div class="tool-icon bg-blue-glow"><i class="bi bi-file-earmark-post-fill"></i></div>
                        <span>Blog Generator</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('templates.index') }}?category=business-copywriting" class="quick-tool-btn">
                        <div class="tool-icon bg-emerald-glow"><i class="bi bi-envelope-fill"></i></div>
                        <span>Email Writer</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('chat.index') }}" class="quick-tool-btn">
                        <div class="tool-icon bg-purple-glow"><i class="bi bi-chat-left-text-fill"></i></div>
                        <span>AI Chat</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('templates.index') }}?category=programming-dev" class="quick-tool-btn">
                        <div class="tool-icon bg-orange-glow"><i class="bi bi-code-slash"></i></div>
                        <span>Code Generator</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('templates.index') }}?category=education-utilities" class="quick-tool-btn">
                        <div class="tool-icon bg-cyan-glow"><i class="bi bi-file-text-fill"></i></div>
                        <span>Text Summarizer</span>
                    </a>
                </div>
                <div class="col-4">
                    <a href="{{ route('templates.index') }}" class="quick-tool-btn">
                        <div class="tool-icon bg-pink-glow"><i class="bi bi-images"></i></div>
                        <span>Image Generator</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Three-Column Dashboard Grid Section -->
<div class="row g-4 mb-4">
    <!-- Col 1: Recent Documents -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-folder-fill me-2 text-primary"></i>Recent Documents</h6>
                <a href="{{ route('history.index') }}" class="text-decoration-none fw-semibold text-muted-custom small">View All</a>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($recentHistory as $doc)
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2.5">
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; border: 1px solid var(--glass-border);">
                                <i class="bi bi-file-earmark-text-fill text-muted-custom" style="font-size: 0.95rem;"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-primary text-truncate" style="font-size: 0.825rem; max-width: 120px;" title="{{ $doc->title }}">{{ $doc->title }}</div>
                                <span class="badge bg-secondary border border-custom text-primary py-0.5 px-1.5 rounded" style="font-size: 0.65rem;">
                                    {{ $doc->template ? $doc->template->name : ucfirst(str_replace('_', ' ', $doc->tool_type)) }}
                                </span>
                            </div>
                        </div>
                        <div class="d-flex gap-1.5 align-items-center">
                            <span class="text-muted-custom small" style="font-size: 0.72rem;">{{ $doc->created_at->diffForHumans(null, true) }} ago</span>
                            <a href="{{ route('history.download.pdf', $doc->id) }}" class="text-danger p-0 border-0 bg-transparent ms-1" title="Download PDF"><i class="bi bi-file-pdf"></i></a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted-custom small">No recent generations.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Col 2: Recent Activity List -->
    <div class="col-12 col-md-6 col-lg-4">
        <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-activity me-2 text-accent-primary"></i>Recent Activity</h6>
            </div>
            <div class="d-flex flex-column gap-3">
                @forelse($recentHistory as $act)
                    @php
                        $isBlog = str_contains(strtolower($act->tool_type), 'blog') || str_contains(strtolower($act->title), 'blog');
                        $isEmail = str_contains(strtolower($act->tool_type), 'email') || str_contains(strtolower($act->title), 'email');
                        $isChat = str_contains(strtolower($act->tool_type), 'chat') || str_contains(strtolower($act->title), 'chat');
                        
                        if ($isBlog) {
                            $actLabel = "Blog generated";
                            $actDesc = "\"".Str::limit($act->title, 20)."\"";
                            $actIcon = "bi-file-earmark-post text-info";
                        } elseif ($isEmail) {
                            $actLabel = "Email created";
                            $actDesc = "\"".Str::limit($act->title, 20)."\"";
                            $actIcon = "bi-envelope text-success";
                        } elseif ($isChat) {
                            $actLabel = "AI Chat conversation";
                            $actDesc = "New conversation session";
                            $actIcon = "bi-chat text-purple";
                        } else {
                            $actLabel = "Content generated";
                            $actDesc = "\"".Str::limit($act->title, 20)."\"";
                            $actIcon = "bi-cpu text-primary";
                        }
                    @endphp
                    <div class="d-flex align-items-start gap-2.5">
                        <div class="activity-icon-dot bg-secondary border border-custom mt-0.5 rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; flex-shrink: 0;">
                            <i class="bi {{ $actIcon }}" style="font-size: 0.8rem;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-baseline">
                                <div class="fw-bold text-primary" style="font-size: 0.8rem;">{{ $actLabel }}</div>
                                <small class="text-muted-custom" style="font-size: 0.7rem;">{{ $act->created_at->diffForHumans(null, true) }} ago</small>
                            </div>
                            <small class="text-muted-custom" style="font-size: 0.72rem;">{{ $actDesc }}</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted-custom small">No activity logs recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Col 3: Daily Limit Circular Gauge -->
    <div class="col-12 col-md-12 col-lg-4">
        <div class="glass-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-clock-history me-2 text-indigo"></i>Daily Limit</h6>
                
                <div class="d-flex justify-content-center align-items-center my-3 position-relative">
                    <svg width="120" height="120" viewBox="0 0 120 120" class="mx-auto d-block">
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="var(--border-color)" stroke-width="8"/>
                        <circle cx="60" cy="60" r="50" fill="transparent" stroke="url(#radialGaugeGrad)" stroke-width="8"
                                stroke-dasharray="314.16" stroke-dashoffset="{{ 314.16 * (1 - $remainingRequests / max(1, $maxRequests)) }}"
                                stroke-linecap="round" transform="rotate(-90 60 60)" style="transition: stroke-dashoffset 1s ease-out;"/>
                        <defs>
                            <linearGradient id="radialGaugeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#818cf8"/>
                                <stop offset="100%" stop-color="#10b981"/>
                            </linearGradient>
                        </defs>
                    </svg>
                    <div class="position-absolute text-center" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
                        <h4 class="fw-bold mb-0 text-primary" style="font-size: 1.3rem;">{{ round(($remainingRequests / max(1, $maxRequests)) * 100) }}%</h4>
                        <small class="text-muted-custom" style="font-size: 0.65rem; font-weight: 600;">Remaining</small>
                    </div>
                </div>
            </div>

            <div>
                <div class="row g-2 text-center border-top border-custom pt-3 mt-2">
                    <div class="col-6 border-end border-custom">
                        <span class="text-muted-custom" style="font-size: 0.72rem; display: block;">Requests</span>
                        <h6 class="fw-bold text-primary mb-0 mt-0.5" style="font-size: 0.85rem;">{{ $todayRequests }} / {{ $maxRequests }}</h6>
                    </div>
                    <div class="col-6">
                        <span class="text-muted-custom" style="font-size: 0.72rem; display: block;">Words</span>
                        <h6 class="fw-bold text-primary mb-0 mt-0.5" style="font-size: 0.85rem;">{{ number_format($todayWords) }} / {{ number_format($maxWords) }}</h6>
                    </div>
                </div>
                
                <div class="text-center mt-3 pt-2 text-muted-custom" style="font-size: 0.7rem;">
                    Limits reset in: <span id="limit-reset-timer" class="fw-bold text-primary">00:00:00</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js configuration & Reset timers -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('usageChart').getContext('2d');
        
        const labels = {!! json_encode($chartData['labels']) !!};
        const wordsData = {!! json_encode($chartData['words']) !!};
        const requestsData = {!! json_encode($chartData['requests']) !!};

        const isDark = document.documentElement.classList.contains('dark-theme');
        const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.03)';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        // Custom linear area gradient fill under smooth line
        const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
        gradientFill.addColorStop(0, 'rgba(129, 140, 248, 0.25)');
        gradientFill.addColorStop(1, 'rgba(129, 140, 248, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Words Generated',
                        data: wordsData,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradientFill,
                        borderColor: '#818cf8',
                        borderWidth: 3,
                        pointBackgroundColor: '#818cf8',
                        pointBorderColor: isDark ? '#0f1322' : '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y'
                    },
                    {
                        label: 'AI Requests',
                        data: requestsData,
                        tension: 0.4,
                        fill: false,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: isDark ? '#0f1322' : '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: { family: 'Plus Jakarta Sans', weight: '600', size: 11 }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 10 } }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 10 } },
                        title: { display: false }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: { color: textColor, font: { family: 'Plus Jakarta Sans', size: 10 } },
                        title: { display: false }
                    }
                }
            }
        });

        // Midnight Reset Countdown Timer calculation
        function updateResetTimer() {
            const now = new Date();
            const midnight = new Date();
            midnight.setHours(24, 0, 0, 0); // Target next midnight
            const diff = midnight - now;

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            const pad = (n) => n.toString().padStart(2, '0');
            const timerEl = document.getElementById('limit-reset-timer');
            if (timerEl) {
                timerEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        }
        setInterval(updateResetTimer, 1000);
        updateResetTimer();
    });
</script>
@endsection

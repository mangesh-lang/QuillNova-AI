@extends('layouts.app')
@section('title', 'My Notifications')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Notifications</h1>
        <p class="page-subtitle mb-0">Review system warnings, welcome messages, and usage limit indicators.</p>
    </div>

    @if(auth()->user()->unreadNotifications->isNotEmpty())
        <form action="{{ route('notifications.read_all') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary border-custom text-primary rounded-3 px-3 py-2 fw-semibold">
                <i class="bi bi-check2-all me-1"></i> Mark All as Read
            </button>
        </form>
    @endif
</div>

<div class="glass-card p-4">
    <div class="d-flex flex-column gap-3">
        @forelse($notifications as $n)
            @php
                $isUnread = is_null($n->read_at);
                $data = $n->data;
            @endphp
            <div class="p-3 border rounded-3 d-flex align-items-start justify-content-between border-custom transition" 
                 style="background: {{ $isUnread ? 'rgba(99, 102, 241, 0.04)' : 'transparent' }};"
                 id="notification-{{ $n->id }}">
                <div class="d-flex gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" 
                         style="width: 40px; height: 40px; background-color: {{ $isUnread ? 'rgba(99, 102, 241, 0.15)' : 'var(--border-color)' }}; color: {{ $isUnread ? 'var(--accent-primary)' : 'var(--text-secondary)' }}; flex-shrink: 0;">
                        <i class="bi {{ str_contains(strtolower($data['title'] ?? ''), 'welcome') ? 'bi-gift' : 'bi-exclamation-triangle-fill' }} fs-5"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <h6 class="fw-bold mb-0 text-primary">{{ $data['title'] ?? 'System Alert' }}</h6>
                            @if($isUnread)
                                <span class="badge bg-primary rounded-pill" style="font-size: 0.65rem;">New</span>
                            @endif
                        </div>
                        <p class="mb-2 text-muted-custom" style="font-size: 0.875rem;">{{ $data['message'] ?? '' }}</p>
                        <span class="text-muted-custom" style="font-size: 0.75rem;">{{ $n->created_at->diffForHumans() }}</span>
                    </div>
                </div>

                @if($isUnread)
                    <button class="btn btn-sm btn-outline-secondary border-custom text-primary px-3 rounded-pill" 
                            onclick="markAsRead('{{ $n->id }}', this)">
                        Mark read
                    </button>
                @endif
            </div>
        @empty
            <div class="text-center py-5 text-muted-custom">
                <i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-50"></i>
                <h6 class="fw-bold">No Notifications Logged</h6>
                <p class="mb-0 small text-muted-custom">We will keep you informed of daily limits and account activities.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $notifications->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    function markAsRead(id, btn) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Adjust visually
                const box = document.getElementById('notification-' + id);
                box.style.background = 'transparent';
                
                // Hide badge and button
                const badge = box.querySelector('.badge');
                if (badge) badge.remove();
                btn.remove();

                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Notification marked as read',
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        });
    }
</script>
@endsection

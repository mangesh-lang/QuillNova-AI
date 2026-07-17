@extends('layouts.app')
@section('title', 'System Activity Logs')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Activity Logs</h1>
        <p class="page-subtitle mb-0">Audit user actions, logins, registrations, and API calls.</p>
    </div>

    <!-- Search Form -->
    <form action="{{ route('admin.logs') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
        <input type="text" name="search" class="form-control border-custom text-primary" style="background: var(--bg-secondary); border-radius: 10px; width: 280px;" placeholder="Search actions or details..." value="{{ $search }}">
        <button type="submit" class="btn btn-primary rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0 text-primary border-custom" style="background: transparent;">
            <thead>
                <tr class="text-muted-custom" style="border-bottom: 1.5px solid var(--border-color); font-size: 0.85rem;">
                    <th scope="col">Timestamp</th>
                    <th scope="col">User</th>
                    <th scope="col">Action</th>
                    <th scope="col">Details</th>
                    <th scope="col">IP Address</th>
                    <th scope="col">User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.9rem;">
                        <td class="text-muted-custom" style="font-size: 0.85rem;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            @if($log->user)
                                <div class="fw-bold">{{ $log->user->name }}</div>
                                <small class="text-muted-custom">{{ $log->user->email }}</small>
                            @else
                                <span class="text-muted-custom">Guest User</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-secondary text-primary border border-custom px-2 py-1 rounded-3" style="font-size: 0.725rem;">
                                {{ strtoupper($log->action) }}
                            </span>
                        </td>
                        <td class="small" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->details }}">
                            {{ $log->details }}
                        </td>
                        <td style="font-size: 0.85rem;">{{ $log->ip_address }}</td>
                        <td class="text-muted-custom small text-truncate" style="max-width: 150px;" title="{{ $log->user_agent }}">
                            {{ $log->user_agent }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted-custom">
                            <i class="bi bi-shield-slash fs-1 d-block mb-3 opacity-50"></i>
                            No activity logs found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $logs->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection

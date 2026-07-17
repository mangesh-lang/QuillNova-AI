@extends('layouts.app')
@section('title', 'Manage Users')

@section('content')
<div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
    <div>
        <h1 class="page-title">Manage Users</h1>
        <p class="page-subtitle mb-0">Create new user accounts, update roles, suspend access, and reset daily limit metrics.</p>
    </div>

    <button class="btn btn-primary d-flex align-items-center gap-2 rounded-3 px-3 py-2 fw-semibold" style="background-color: var(--accent-primary); border: none;" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-person-plus-fill"></i> Add User Account
    </button>
</div>

<!-- Filters & Search -->
<div class="glass-card p-3 mb-4">
    <form action="{{ route('admin.users') }}" method="GET" class="row g-3 align-items-center">
        <div class="col-12 col-md-5">
            <div class="input-group">
                <span class="input-group-text border-custom text-muted-custom" style="background: var(--bg-secondary);"><i class="bi bi-search"></i></span>
                <input type="text" name="search" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="Search by name or email..." value="{{ $search }}">
            </div>
        </div>

        <div class="col-12 col-md-4">
            <select name="status" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                <option value="">All Statuses</option>
                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
            </select>
        </div>

        <div class="col-12 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold rounded-3 py-2" style="background-color: var(--accent-primary); border: none;">Filter</button>
            <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary border-custom text-primary px-3 rounded-3 py-2"><i class="bi bi-arrow-counterclockwise"></i></a>
        </div>
    </form>
</div>

<!-- Users List -->
<div class="glass-card p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0 text-primary border-custom" style="background: transparent;">
            <thead>
                <tr class="text-muted-custom" style="border-bottom: 1.5px solid var(--border-color); font-size: 0.85rem;">
                    <th scope="col">User</th>
                    <th scope="col">Role</th>
                    <th scope="col">Account Status</th>
                    <th scope="col">Today's Usage</th>
                    <th scope="col" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $todayUsage = $user->dailyLimits()->where('date', date('Y-m-d'))->first();
                        $requests = $todayUsage ? $todayUsage->request_count : 0;
                        $words = $todayUsage ? $todayUsage->word_count : 0;
                    @endphp
                    <tr style="border-bottom: 1px solid var(--border-color); font-size: 0.9rem;">
                        <td>
                            <div class="fw-bold">{{ $user->name }}</div>
                            <small class="text-muted-custom">{{ $user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-primary border border-custom px-2 py-1 rounded-3" style="font-size: 0.725rem;">
                                {{ $user->roles->pluck('name')->first() ?: 'None' }}
                            </span>
                        </td>
                        <td>
                            @if($user->isSuspended())
                                <span class="badge bg-danger bg-opacity-10 text-danger px-2.5 py-1.5 rounded-3 fw-semibold">Suspended</span>
                            @else
                                <span class="badge bg-success bg-opacity-10 text-success px-2.5 py-1.5 rounded-3 fw-semibold">Active</span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size: 0.825rem;">
                                <div><i class="bi bi-cpu text-muted-custom me-1"></i> Calls: <strong class="text-primary">{{ $requests }}</strong></div>
                                <div><i class="bi bi-file-earmark-word text-muted-custom me-1"></i> Words: <strong class="text-primary">{{ number_format($words) }}</strong></div>
                            </div>
                        </td>
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2">
                                <!-- Reset Limits -->
                                <form action="{{ route('admin.users.reset_limits', $user->id) }}" method="POST" onsubmit="return confirm('Reset daily limits count back to 0 for this user?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Reset Today's Limits"><i class="bi bi-arrow-clockwise"></i></button>
                                </form>

                                <!-- Suspend/Unsuspend -->
                                @if($user->isSuspended())
                                    <form action="{{ route('admin.users.unsuspend', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Unsuspend User"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST" onsubmit="return confirm('Suspend access for this user?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary text-primary border-custom" title="Suspend User"><i class="bi bi-slash-circle"></i></button>
                                    </form>
                                @endif

                                <!-- Edit Toggle -->
                                <button class="btn btn-sm btn-outline-secondary text-primary border-custom" data-bs-toggle="modal" data-bs-target="#editUserModal-{{ $user->id }}" title="Edit User"><i class="bi bi-pencil"></i></button>

                                <!-- Delete -->
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Delete this user account permanently?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal-{{ $user->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
                                <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header border-custom">
                                        <h5 class="modal-title fw-bold">Edit User Details</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body d-flex flex-column gap-3">
                                        <div>
                                            <label class="form-label fw-semibold text-primary">Name</label>
                                            <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $user->name }}" required>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold text-primary">Email Address</label>
                                            <input type="email" name="email" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" value="{{ $user->email }}" required>
                                        </div>
                                        <div>
                                            <label class="form-label fw-semibold text-primary">User Role</label>
                                            <select name="role" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-custom">
                                        <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Save Details</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted-custom">No users logged in DB.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-end">
        {{ $users->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card border-custom text-primary" style="background: var(--bg-secondary);">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="modal-header border-custom">
                    <h5 class="modal-title fw-bold">Create User Account</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body d-flex flex-column gap-3">
                    <div>
                        <label class="form-label fw-semibold text-primary">Full Name</label>
                        <input type="text" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="John Doe" required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Email Address</label>
                        <input type="email" name="email" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" placeholder="john@example.com" required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">Temporary Password</label>
                        <input type="password" name="password" class="form-control text-primary border-custom" style="background: var(--bg-secondary);" required>
                    </div>
                    <div>
                        <label class="form-label fw-semibold text-primary">User Role</label>
                        <select name="role" class="form-select text-primary border-custom" style="background: var(--bg-secondary);">
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-custom">
                    <button type="button" class="btn btn-secondary border-custom text-primary bg-transparent" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" style="background-color: var(--accent-primary); border: none;">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

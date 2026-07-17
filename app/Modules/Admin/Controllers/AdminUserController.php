<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\ActivityLog;
use App\Services\DailyLimitService;
use App\Modules\Auth\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    protected UserRepository $userRepo;
    protected DailyLimitService $limitService;

    public function __construct(UserRepository $userRepo, DailyLimitService $limitService)
    {
        $this->userRepo = $userRepo;
        $this->limitService = $limitService;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        
        $users = $this->userRepo->searchAndPaginate($search, $status, 10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles', 'search', 'status'));
    }

    /**
     * Create a new user.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', Rules\Password::defaults()],
            'role' => ['required', 'string'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        // Assign Role
        $user->assignRole($request->role);

        // Initialize Profile
        UserProfile::create([
            'user_id' => $user->id,
            'company' => '',
            'timezone' => 'UTC',
            'language' => 'en',
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_create',
            'details' => 'Admin created user ' . $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'User created successfully!');
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Sync Roles (User can only have one main role)
        $user->syncRoles([$request->role]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_update',
            'details' => 'Admin updated details for user #' . $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'User updated successfully!');
    }

    /**
     * Suspend a user.
     */
    public function suspend(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend your own account.');
        }

        $user->update(['status' => 'suspended']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_suspend',
            'details' => 'Admin suspended user #' . $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'User suspended successfully.');
    }

    /**
     * Unsuspend a user.
     */
    public function unsuspend(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $user->update(['status' => 'active']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_unsuspend',
            'details' => 'Admin activated user #' . $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'User activated successfully.');
    }

    /**
     * Reset a user's daily usage limits for today.
     */
    public function resetLimits(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $this->limitService->resetUserLimit($user);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_reset_limits',
            'details' => 'Admin reset daily limits for user #' . $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Daily limit usage count reset to 0 for ' . $user->name . '.');
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account from this panel.');
        }

        // Delete user's profile avatar if exists
        if ($user->profile && $user->profile->avatar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($user->profile->avatar);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'admin_user_delete',
            'details' => 'Admin deleted user #' . $user->id . ' (' . $user->email . ')',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}

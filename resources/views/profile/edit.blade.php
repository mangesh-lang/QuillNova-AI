@extends('layouts.app')
@section('title', 'My Profile Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Profile Settings</h1>
    <p class="page-subtitle">Update your profile parameters, upload avatars, change passwords, and manage your account.</p>
</div>

<div class="row g-4">
    <!-- Profile Info Form Column -->
    <div class="col-12 col-xl-8">
        <div class="glass-card p-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-person-circle text-primary me-2"></i> Update Profile Details</h5>
            <hr class="border-custom mb-4">

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="name" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Full Name</label>
                        <input type="text" id="name" name="name" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="email" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Email Address</label>
                        <input type="email" id="email" name="email" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="phone" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Phone Number</label>
                        <input type="text" id="phone" name="phone" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ old('phone', $user->profile->phone ?? '') }}">
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="company" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Company / Employer</label>
                        <input type="text" id="company" name="company" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" value="{{ old('company', $user->profile->company ?? '') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label for="timezone" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Timezone</label>
                        <select id="timezone" name="timezone" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                            @foreach($timezones as $tz)
                                <option value="{{ $tz }}" {{ old('timezone', $user->profile->timezone ?? 'UTC') === $tz ? 'selected' : '' }}>
                                    {{ $tz }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="language" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Default Language</label>
                        <select id="language" name="language" class="form-select text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                            <option value="en" {{ old('language', $user->profile->language ?? 'en') === 'en' ? 'selected' : '' }}>English (en)</option>
                            <option value="es" {{ old('language', $user->profile->language ?? 'en') === 'es' ? 'selected' : '' }}>Spanish (es)</option>
                            <option value="fr" {{ old('language', $user->profile->language ?? 'en') === 'fr' ? 'selected' : '' }}>French (fr)</option>
                            <option value="de" {{ old('language', $user->profile->language ?? 'en') === 'de' ? 'selected' : '' }}>German (de)</option>
                            <option value="hi" {{ old('language', $user->profile->language ?? 'en') === 'hi' ? 'selected' : '' }}>Hindi (hi)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="bio" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Short Biography</label>
                    <textarea id="bio" name="bio" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" rows="4" placeholder="Share a few words about yourself...">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                </div>

                <!-- Avatar Upload -->
                <div class="mb-4">
                    <label for="avatar" class="form-label fw-semibold text-primary" style="font-size: 0.875rem;">Upload Avatar Profile Image</label>
                    <input type="file" id="avatar" name="avatar" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;">
                    <div class="form-text text-muted-custom" style="font-size: 0.775rem;">Supported formats: JPG, PNG, WEBP. Max 2MB.</div>
                </div>

                <button type="submit" class="btn btn-custom px-4 fw-semibold py-2">Save Profile Updates</button>
            </form>
        </div>
    </div>

    <!-- Right Column (Avatar Card & Password Change) -->
    <div class="col-12 col-xl-4">
        <!-- Profile Overview Card -->
        <div class="glass-card p-4 text-center mb-4">
            <div class="d-inline-block position-relative mb-3">
                @if($user->profile && $user->profile->avatar)
                    <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="Avatar" width="100" height="100" class="rounded-circle border border-3 border-custom" style="object-fit: cover;">
                @else
                    <div class="bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center border border-3 border-custom" style="width: 100px; height: 100px; font-size: 36px; font-weight: bold; background-color: var(--accent-primary);">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                @endif
            </div>
            <h5 class="fw-bold mb-1 text-primary">{{ $user->name }}</h5>
            <p class="text-muted-custom mb-3 small">{{ $user->email }}</p>
            <span class="badge bg-secondary text-primary border border-custom px-3 py-1.5 rounded-pill" style="font-size: 0.75rem;">
                {{ $user->isSuperAdmin() ? 'Super Admin' : 'User Level Account' }}
            </span>
        </div>

        <!-- Change Password Card -->
        <div class="glass-card p-4 mb-4">
            <h5 class="fw-bold mb-3"><i class="bi bi-key text-primary me-2"></i> Change Password</h5>
            <hr class="border-custom mb-4">

            <form action="{{ route('profile.password') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="current_password" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">Current Password</label>
                    <input type="password" id="current_password" name="current_password" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" required>
                </div>
                
                <div class="mb-3">
                    <label for="password_new" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">New Password</label>
                    <input type="password" id="password_new" name="password" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" placeholder="Min 8 characters" required>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-semibold text-primary" style="font-size: 0.85rem;">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control text-primary border-custom" style="background: var(--bg-secondary); border-radius: 10px;" required>
                </div>

                <button type="submit" class="btn btn-outline-secondary border-custom text-primary bg-transparent w-100 py-2.5 fw-semibold">Update Password</button>
            </form>
        </div>

        <!-- Danger Zone Delete Account Card -->
        <div class="glass-card p-4 border border-danger border-opacity-25" style="background: rgba(239, 68, 68, 0.02);">
            <h5 class="fw-bold mb-1 text-danger"><i class="bi bi-shield-x me-2"></i> Danger Zone</h5>
            <p class="text-muted-custom mb-3 small">Permanently delete your QuillNova account and all generation data.</p>
            
            <button class="btn btn-outline-danger w-100 py-2.5 fw-semibold" 
                    onclick="triggerDeleteModal()">
                Delete Account
            </button>
        </div>
    </div>
</div>

<!-- Hidden Delete Modal triggered via Alpine/JS sweetalert -->
<script>
    function triggerDeleteModal() {
        Swal.fire({
            title: 'Are you absolutely sure?',
            text: 'This action is irreversible and will delete all templates, chat, and history logs.',
            icon: 'warning',
            input: 'password',
            inputPlaceholder: 'Enter your password to confirm',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Permanently Delete Account',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter your password to confirm!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                // Post form delete action
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route('profile.delete') }}';
                
                let csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '_token';
                csrf.value = '{{ csrf_token() }}';
                form.appendChild(csrf);

                let pass = document.createElement('input');
                pass.type = 'hidden';
                pass.name = 'password';
                pass.value = result.value;
                form.appendChild(pass);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection

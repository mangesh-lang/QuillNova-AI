@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
<h4 class="fw-bold mb-1">Set New Password</h4>
<p class="text-secondary-custom mb-4" style="font-size: 0.9rem;">Choose a strong new password to secure your account.</p>

@if ($errors->any())
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 py-3 mb-3">
        <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ $token }}">

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Email Address</label>
        <input id="email" type="email" name="email" class="form-control form-control-custom" placeholder="name@example.com" value="{{ $email ?? old('email') }}" required autofocus>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">New Password</label>
        <input id="password" type="password" name="password" class="form-control form-control-custom" placeholder="Min 8 characters" required autocomplete="new-password">
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Confirm New Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="Retype password" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-custom w-100 py-3">Reset Password</button>
    </div>
</form>
@endsection

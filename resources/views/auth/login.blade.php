@extends('layouts.auth')
@section('title', 'Log In')

@section('content')
<h4 class="fw-bold mb-1">Welcome back</h4>
<p class="text-secondary-custom mb-4" style="font-size: 0.9rem;">Enter your credentials to access your dashboard.</p>

@if ($errors->any())
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 py-3 mb-3">
        <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success rounded-4 py-3 mb-3" style="font-size: 0.85rem;">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Email Address</label>
        <input id="email" type="email" name="email" class="form-control form-control-custom" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-medium text-secondary-custom mb-0" style="font-size: 0.85rem;">Password</label>
            <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 0.8rem;">Forgot password?</a>
        </div>
        <input id="password" type="password" name="password" class="form-control form-control-custom" placeholder="Enter your password" required>
    </div>

    <!-- Remember Me -->
    <div class="form-check mb-4">
        <input id="remember_me" type="checkbox" name="remember" class="form-check-input bg-transparent border-secondary border-opacity-50">
        <label for="remember_me" class="form-check-label text-secondary-custom" style="font-size: 0.85rem;">Remember this device</label>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-custom w-100 py-3">Log In</button>
    </div>

    <p class="text-center text-secondary-custom mb-0" style="font-size: 0.85rem;">
        Don't have an account? <a href="{{ route('register') }}" class="auth-link">Create one free</a>
    </p>
</form>
@endsection

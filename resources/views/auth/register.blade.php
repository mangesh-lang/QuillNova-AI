@extends('layouts.auth')
@section('title', 'Sign Up')

@section('content')
<h4 class="fw-bold mb-1">Create free account</h4>
<p class="text-secondary-custom mb-4" style="font-size: 0.9rem;">Join QuillNova and generate content in seconds.</p>

@if ($errors->any())
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 py-3 mb-3">
        <ul class="mb-0 ps-3" style="font-size: 0.85rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('register') }}">
    @csrf

    <!-- Name -->
    <div class="mb-3">
        <label for="name" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Full Name</label>
        <input id="name" type="text" name="name" class="form-control form-control-custom" placeholder="John Doe" value="{{ old('name') }}" required autofocus>
    </div>

    <!-- Email Address -->
    <div class="mb-3">
        <label for="email" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Email Address</label>
        <input id="email" type="email" name="email" class="form-control form-control-custom" placeholder="john@example.com" value="{{ old('email') }}" required>
    </div>

    <!-- Password -->
    <div class="mb-3">
        <label for="password" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Password</label>
        <input id="password" type="password" name="password" class="form-control form-control-custom" placeholder="Create strong password" required autocomplete="new-password">
    </div>

    <!-- Confirm Password -->
    <div class="mb-4">
        <label for="password_confirmation" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Confirm Password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" class="form-control form-control-custom" placeholder="Retype password" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-custom w-100 py-3">Create Account</button>
    </div>

    <p class="text-center text-secondary-custom mb-0" style="font-size: 0.85rem;">
        Already have an account? <a href="{{ route('login') }}" class="auth-link">Log In</a>
    </p>
</form>
@endsection

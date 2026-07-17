@extends('layouts.auth')
@section('title', 'Forgot Password')

@section('content')
<h4 class="fw-bold mb-1">Reset Password</h4>
<p class="text-secondary-custom mb-4" style="font-size: 0.9rem;">Enter your email to receive a password reset link.</p>

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

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <!-- Email Address -->
    <div class="mb-4">
        <label for="email" class="form-label fw-medium text-secondary-custom" style="font-size: 0.85rem;">Email Address</label>
        <input id="email" type="email" name="email" class="form-control form-control-custom" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-custom w-100 py-3">Send Reset Link</button>
    </div>

    <p class="text-center text-secondary-custom mb-0" style="font-size: 0.85rem;">
        Remembered password? <a href="{{ route('login') }}" class="auth-link">Back to Log In</a>
    </p>
</form>
@endsection

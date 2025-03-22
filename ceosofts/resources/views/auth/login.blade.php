@extends('layouts.guest')

@section('content')
    <div class="mb-4 text-center">
        <h5>{{ __('Login to your account') }}</h5>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email/Username -->
        <div class="mb-3">
            <label for="login" class="form-label">{{ __('Email or Username') }}</label>
            <input id="login" type="text" class="form-control @error('login') is-invalid @enderror" 
                   name="login" value="{{ old('login') }}" required autofocus>
            @error('login')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" required autocomplete="current-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-3 form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
                {{ __('Remember me') }}
            </label>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-0">
            <button type="submit" class="btn btn-primary">
                {{ __('Log in') }}
            </button>

            @if (Route::has('password.request'))
                <a class="text-decoration-none" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
    </form>
@endsection
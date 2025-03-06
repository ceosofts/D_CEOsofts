<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h1 class="mb-3">Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

    {{-- Display admin controls if the user has the admin role --}}
    @if(Auth::check() && Auth::user()->hasRole('admin'))
        <div class="mb-3">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                <i class="bi bi-people-fill"></i> Manage Users
            </a>
        </div>
    @endif

    {{-- Additional dashboard widgets can be added here --}}
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Dashboard Widgets</h5>
            <p class="card-text">Add your custom widgets or statistics here to provide useful insights.</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('home') }}" class="btn btn-secondary">
            <i class="bi bi-house"></i> Back to Home
        </a>
    </div>
</div>
@endsection
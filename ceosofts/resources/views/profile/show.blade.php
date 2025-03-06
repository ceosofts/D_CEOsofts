@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container">
    <h1 class="mb-4">Profile Page</h1>
    <p>Manage your profile details below.</p>

    @if(Auth::check())
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                Your Profile Information
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                {{-- Add other details as needed --}}
                <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                    <i class="bi bi-pencil"></i> Edit Profile
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-warning mt-4">
            You are not logged in.
        </div>
    @endif
</div>
@endsection
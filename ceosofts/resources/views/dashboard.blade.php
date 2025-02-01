<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('content')
{{-- <div class="container">
    <h1>Dashboard</h1>
    <p>Welcome to the dashboard!</p>
</div> --}}

<div class="container">
    <h1>Dashboard</h1>

    {{-- แสดงเฉพาะ Admin เท่านั้น --}}
    {{-- @if (Auth::user()->role === 'admin')
        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">จัดการผู้ใช้</a>

    @endif

    <a href="{{ route('home') }}" class="btn btn-secondary">กลับหน้าหลัก</a> --}}
    
</div>

@endsection

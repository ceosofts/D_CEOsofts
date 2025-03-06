@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">ฝ่าย: {{ Auth::user()->department->name ?? 'ไม่มีแผนก' }}</h1>

        @php
            // Filter users that belong to the same department as the logged-in user.
            $departmentUsers = $users->filter(function($user) {
                return $user->department_id == Auth::user()->department_id;
            });
        @endphp

        @if($departmentUsers->isNotEmpty())
            <ul class="list-group">
                @foreach ($departmentUsers as $user)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $user->name }}
                        <span class="badge bg-primary rounded-pill">{{ $user->role }}</span>
                    </li>
                @endforeach
            </ul>
        @else
            <p>ไม่มีผู้ใช้ในแผนกของคุณ</p>
        @endif
    </div>
@endsection
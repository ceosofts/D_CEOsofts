@extends('layouts.app')

@section('content')
    <h1>ฝ่าย: {{ Auth::user()->department->name }}</h1>

    <ul>
        @foreach ($users as $user)
            @if ($user->department_id == Auth::user()->department_id)
                <li>{{ $user->name }} - {{ $user->role }}</li>
            @endif
        @endforeach
    </ul>
@endsection

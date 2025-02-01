@extends('layouts.app')

@section('content')
    <h1>แก้ไขแผนก</h1>

    {{-- <form action="{{ route('departments.update', $department) }}" method="POST"> --}}
        <form action="{{ route('admin.departments.update', $department) }}" method="POST">

        @csrf
        @method('PUT')
        <label for="name">ชื่อแผนก:</label>
        <input type="text" name="name" value="{{ $department->name }}" required>
        <button type="submit">อัปเดต</button>
    </form>
@endsection

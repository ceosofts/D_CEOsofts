@extends('layouts.app')

@section('content')
    <h1>เพิ่มแผนก</h1>

    {{-- <form action="{{ route('departments.store') }}" method="POST"> --}}
    <form action="{{ route('admin.departments.store') }}" method="POST">

        @csrf
        <label for="name">ชื่อแผนก:</label>
        <input type="text" name="name" required>
        <button type="submit">บันทึก</button>
    </form>
@endsection

@extends('layouts.app')

@section('title', 'แก้ไขแผนก')

@section('content')
<div class="container">
    <h1 class="mb-4">แก้ไขแผนก</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.departments.update', $department->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">ชื่อแผนก</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $department->name) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">อัปเดต</button>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection
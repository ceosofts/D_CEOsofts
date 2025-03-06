@extends('layouts.app')

@section('title', 'เพิ่มแผนก')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มแผนกใหม่</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.departments.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">ชื่อแผนก</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
        <a href="{{ route('admin.departments.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection
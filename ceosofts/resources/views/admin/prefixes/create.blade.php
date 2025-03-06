@extends('layouts.app')

@section('title', 'เพิ่มคำนำหน้าชื่อ')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มคำนำหน้าชื่อ</h1>

    {{-- แสดง error messages ถ้ามี --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.prefixes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">คำนำหน้าชื่อ</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> บันทึก
        </button>
        <a href="{{ route('admin.prefixes.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> ยกเลิก
        </a>
    </form>
</div>
@endsection
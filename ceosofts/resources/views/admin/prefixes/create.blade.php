@extends('layouts.app')

@section('title', 'เพิ่มคำนำหน้าชื่อ')

@section('content')
<div class="container">
    <h1>เพิ่มคำนำหน้าชื่อ</h1>
    <form action="{{ route('admin.prefixes.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">คำนำหน้าชื่อ</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="{{ route('admin.prefixes.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection

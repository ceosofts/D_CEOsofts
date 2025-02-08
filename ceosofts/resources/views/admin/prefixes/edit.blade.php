@extends('layouts.app')

@section('title', 'แก้ไขคำนำหน้าชื่อ')

@section('content')
<div class="container">
    <h1>แก้ไขคำนำหน้าชื่อ</h1>
    <form action="{{ route('admin.prefixes.update', $prefix->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">คำนำหน้าชื่อ</label>
            <input type="text" name="name" class="form-control" value="{{ $prefix->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
        <a href="{{ route('admin.prefixes.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection

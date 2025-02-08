@extends('layouts.app')

@section('title', 'เพิ่มตำแหน่งพนักงาน')

@section('content')
<div class="container">
    <h1>เพิ่มตำแหน่งพนักงาน</h1>
    <form action="{{ route('admin.positions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">ชื่อตำแหน่ง</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection

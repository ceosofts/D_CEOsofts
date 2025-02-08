@extends('layouts.app')

@section('title', 'แก้ไขตำแหน่ง')

@section('content')
<div class="container">
    <h1>แก้ไขตำแหน่ง</h1>
    <form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">ชื่อตำแหน่ง</label>
            <input type="text" name="name" class="form-control" value="{{ $position->name }}" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection

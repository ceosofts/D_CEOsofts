@extends('layouts.app')

@section('title', 'แก้ไขตำแหน่ง')

@section('content')
<div class="container">
    <h1 class="mb-4">แก้ไขตำแหน่ง</h1>
    
    <!-- แสดงข้อความ error จากการ validation (ถ้ามี) -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.positions.update', $position->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อตำแหน่ง</label>
            <input type="text" id="name" name="name" class="form-control" 
                   value="{{ old('name', $position->name) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">บันทึก</button>
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection
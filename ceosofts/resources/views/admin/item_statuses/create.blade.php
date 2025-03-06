@extends('layouts.app')

@section('title', 'เพิ่มสถานะสินค้า')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มสถานะสินค้า</h1>
    
    {{-- แสดงข้อความ error หากมี --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.item_statuses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสถานะ</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">บันทึก</button>
            <a href="{{ route('admin.item_statuses.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </div>
    </form>
</div>
@endsection
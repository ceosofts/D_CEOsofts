@extends('layouts.app')

@section('title', 'เพิ่มตำแหน่งพนักงาน')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มตำแหน่งพนักงาน</h1>

    <!-- แสดง error message ถ้ามี -->
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.positions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อตำแหน่ง</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ old('name') }}" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
        <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection
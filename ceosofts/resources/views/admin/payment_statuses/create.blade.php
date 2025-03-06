@extends('layouts.app')

@section('title', 'เพิ่มสถานะการจ่ายเงิน')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มสถานะการจ่ายเงิน</h1>

    {{-- แสดง error messages หากมี --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.payment_statuses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสถานะ</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="d-flex">
            <button type="submit" class="btn btn-success me-2">บันทึก</button>
            <a href="{{ route('admin.payment_statuses.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </div>
    </form>
</div>
@endsection
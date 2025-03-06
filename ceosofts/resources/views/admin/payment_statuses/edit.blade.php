@extends('layouts.app')

@section('title', 'แก้ไขสถานะการจ่ายเงิน')

@section('content')
<div class="container">
    <h1 class="mb-4">แก้ไขสถานะการจ่ายเงิน</h1>

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

    <form action="{{ route('admin.payment_statuses.update', $paymentStatus->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสถานะ</label>
            <input type="text" name="name" id="name" class="form-control" 
                   value="{{ old('name', $paymentStatus->name) }}" required>
        </div>

        <div class="d-flex">
            <button type="submit" class="btn btn-primary me-2">อัปเดต</button>
            <a href="{{ route('admin.payment_statuses.index') }}" class="btn btn-secondary">ยกเลิก</a>
        </div>
    </form>
</div>
@endsection
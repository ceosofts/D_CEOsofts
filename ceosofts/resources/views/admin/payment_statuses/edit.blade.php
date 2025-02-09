@extends('layouts.app')

@section('content')
<div class="container">
    <h1>แก้ไขสถานะการจ่ายเงิน</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
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
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $paymentStatus->name) }}" required>
        </div>
        
        <button type="submit" class="btn btn-primary">อัปเดต</button>
        <a href="{{ route('admin.payment_statuses.index') }}" class="btn btn-secondary">ยกเลิก</a>
    </form>
</div>
@endsection

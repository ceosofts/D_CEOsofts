@extends('layouts.app')

@section('content')
<div class="container">
    <h1>แก้ไขสถานะสินค้า</h1>
    
    <form action="{{ route('admin.item_statuses.update', $status->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสถานะ</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $status->name }}" required>
        </div>
        <button type="submit" class="btn btn-primary">บันทึก</button>
    </form>
</div>
@endsection

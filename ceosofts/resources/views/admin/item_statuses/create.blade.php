@extends('layouts.app')

@section('content')
<div class="container">
    <h1>เพิ่มสถานะสินค้า</h1>
    
    <form action="{{ route('admin.item_statuses.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">ชื่อสถานะ</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <button type="submit" class="btn btn-success">บันทึก</button>
    </form>
</div>
@endsection

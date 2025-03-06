@extends('layouts.app')

@section('title', 'Add Company Holiday')

@section('content')
<div class="container">
    <h1 class="mb-4">เพิ่มวันหยุด</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('company-holidays.store') }}" method="POST">
                @csrf

                <!-- Date Field -->
                <div class="mb-3">
                    <label for="date" class="form-label">วันที่</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>

                <!-- Holiday Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อวันหยุด</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <!-- Form Buttons -->
                <button type="submit" class="btn btn-success">บันทึกข้อมูล</button>
                <a href="{{ route('company-holidays.index') }}" class="btn btn-secondary">ย้อนกลับ</a>
            </form>
        </div>
    </div>
</div>
@endsection
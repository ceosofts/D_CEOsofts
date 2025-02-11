@extends('layouts.app')

@section('title', 'Edit Company Holiday')

@section('content')
<div class="container">
    <h1 class="mb-4">แก้ไขวันหยุด</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('company-holidays.update', $companyHoliday->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="date" class="form-label">วันที่</label>
                    <input type="date" class="form-control" id="date" name="date" value="{{ $companyHoliday->date }}" required>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อวันหยุด</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $companyHoliday->name }}" required>
                </div>

                <button type="submit" class="btn btn-warning">บันทึกการแก้ไข</button>
                <a href="{{ route('company-holidays.index') }}" class="btn btn-secondary">🔙 ย้อนกลับ</a>
            </form>
        </div>
    </div>
</div>
@endsection

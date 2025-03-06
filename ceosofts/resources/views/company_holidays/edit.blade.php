@extends('layouts.app')

@section('title', 'แก้ไขวันหยุดบริษัท')

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
                    <input 
                        type="date" 
                        name="date" 
                        id="date" 
                        class="form-control @error('date') is-invalid @enderror" 
                        value="{{ old('date', $companyHoliday->date) }}" 
                        required>
                    @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">ชื่อวันหยุด</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $companyHoliday->name) }}" 
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-check-lg"></i> บันทึกการแก้ไข
                </button>
                <a href="{{ route('company-holidays.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> ย้อนกลับ
                </a>
            </form>
        </div>
    </div>
</div>
@endsection
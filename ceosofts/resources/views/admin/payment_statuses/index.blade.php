@extends('layouts.app')

@section('title', 'จัดการสถานะการจ่ายเงิน')

@section('content')
<div class="container">
    <h1>จัดการสถานะการจ่ายเงิน</h1>

    {{-- <a href="{{ route('admin.payment_statuses.create') }}" class="btn btn-primary">เพิ่มสถานะ</a> --}}


    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.payment_statuses.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มสถานะ
        </a>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>ชื่อสถานะ</th>
            <th>การจัดการ</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($statuses as $status)
        <tr>
            <td>{{ $status->id }}</td>
            <td>{{ $status->name }}</td>
            <td>
                <a href="{{ route('admin.payment_statuses.edit', $status) }}" class="btn btn-warning">แก้ไข</a>
                <form action="{{ route('admin.payment_statuses.destroy', $status) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger">ลบ</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
    
    </table>
</div>
@endsection

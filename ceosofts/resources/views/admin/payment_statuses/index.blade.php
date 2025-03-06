@extends('layouts.app')

@section('title', 'จัดการสถานะการจ่ายเงิน')

@section('content')
<div class="container">
    <h1 class="mb-4">จัดการสถานะการจ่ายเงิน</h1>

    {{-- ปุ่มเพิ่มสถานะ --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.payment_statuses.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มสถานะ
        </a>
    </div>

    {{-- แสดงข้อความสำเร็จ --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- แสดงข้อความ error หากมี --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if($statuses->count())
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>ชื่อสถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($statuses as $status)
                    <tr class="text-center">
                        <td>{{ $status->id }}</td>
                        <td>{{ $status->name }}</td>
                        <td>
                            <a href="{{ route('admin.payment_statuses.edit', $status) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> แก้ไข
                            </a>
                            <form action="{{ route('admin.payment_statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสถานะนี้?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- หากมีการแบ่งหน้า (pagination) --}}
        <div class="d-flex justify-content-center">
            {{ $statuses->links() }}
        </div>
    @else
        <div class="alert alert-info">ไม่มีข้อมูลสถานะการจ่ายเงิน</div>
    @endif
</div>
@endsection
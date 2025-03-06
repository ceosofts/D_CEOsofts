@extends('layouts.app')

@section('title', 'จัดการคำนำหน้าชื่อ')

@section('content')
<div class="container">
    <h1 class="mb-4">จัดการคำนำหน้าชื่อ</h1>

    {{-- แสดงข้อความ success ถ้ามี --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- แสดง error messages ถ้ามี --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ปุ่มเพิ่มคำนำหน้า --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.prefixes.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มคำนำหน้า
        </a>
    </div>

    {{-- ตารางแสดงรายการคำนำหน้าชื่อ --}}
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>คำนำหน้า</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prefixes as $prefix)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $prefix->name }}</td>
                    <td>
                        <a href="{{ route('admin.prefixes.edit', $prefix->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> แก้ไข
                        </a>
                        <form action="{{ route('admin.prefixes.destroy', $prefix->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่ที่จะลบข้อมูลนี้?')">
                                <i class="bi bi-trash"></i> ลบ
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">ไม่พบข้อมูล</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- แสดงลิงค์สำหรับแบ่งหน้า ถ้ามี --}}
    @if($prefixes->hasPages())
        <div class="d-flex justify-content-center">
            {{ $prefixes->links() }}
        </div>
    @endif
</div>
@endsection
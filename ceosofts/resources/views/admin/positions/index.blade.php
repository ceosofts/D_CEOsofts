@extends('layouts.app')

@section('title', 'ตำแหน่งพนักงาน')

@section('content')
<div class="container">
    <h1 class="mb-4">จัดการตำแหน่งพนักงาน</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.positions.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มตำแหน่ง
        </a>
    </div>

    {{-- แสดงข้อความ success หากมี --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>ชื่อตำแหน่ง</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($positions as $position)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $position->name }}</td>
                    <td>
                        <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> แก้ไข
                        </a>
                        <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบตำแหน่งนี้?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
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
</div>
@endsection
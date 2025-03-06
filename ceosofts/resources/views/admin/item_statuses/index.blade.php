@extends('layouts.app')

@section('title', 'จัดการสถานะสินค้า')

@section('content')
<div class="container">
    <h1 class="mb-4">จัดการสถานะสินค้า</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.item_statuses.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มสถานะ
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>ชื่อสถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($statuses as $status)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $status->name }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.item_statuses.edit', $status) }}" class="btn btn-warning btn-sm">
                                <i class="bi bi-pencil"></i> แก้ไข
                            </a>
                            <form action="{{ route('admin.item_statuses.destroy', $status) }}" method="POST" class="d-inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสถานะนี้?');">
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

    {{-- หากต้องการแสดง pagination ให้เพิ่มด้านล่าง --}}
    <div class="d-flex justify-content-center">
        {{ $statuses->links() }}
    </div>
</div>
@endsection
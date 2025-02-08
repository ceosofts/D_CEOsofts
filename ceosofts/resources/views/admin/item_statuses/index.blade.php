@extends('layouts.app')

@section('content')
<div class="container">
    <h1>จัดการสถานะสินค้า</h1>
    <a href="{{ route('admin.item_statuses.create') }}" class="btn btn-success">เพิ่มสถานะ</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>#</th>
                <th>ชื่อสถานะ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($statuses as $status)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $status->name }}</td>
                    <td>
                        <a href="{{ route('admin.item_statuses.edit', $status) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                        <form action="{{ route('admin.item_statuses.destroy', $status) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

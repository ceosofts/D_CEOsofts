@extends('layouts.app')

@section('title', 'จัดการคำนำหน้าชื่อ')

@section('content')
<div class="container">
    <h1>จัดการคำนำหน้าชื่อ</h1>
    <a href="{{ route('admin.prefixes.create') }}" class="btn btn-success mb-3">เพิ่มคำนำหน้า</a>
    

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>คำนำหน้า</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prefixes as $prefix)
                <tr>
                    <td>{{ $prefix->id }}</td>
                    <td>{{ $prefix->name }}</td>
                    <td>
                        <a href="{{ route('admin.prefixes.edit', $prefix->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>

                        <form action="{{ route('admin.prefixes.destroy', $prefix->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.app')

@section('title', 'จัดการตำแหน่งพนักงาน')

@section('content')
<div class="container">
    <h1>จัดการตำแหน่งพนักงาน</h1>

    <a href="{{ route('admin.positions.create') }}" class="btn btn-success mb-3">เพิ่มตำแหน่ง</a>



    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่อตำแหน่ง</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($positions as $position)
                <tr>
                    <td>{{ $position->id }}</td>
                    <td>{{ $position->name }}</td>
                    <td>
                        <a href="{{ route('admin.positions.edit', $position->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>

                        <form action="{{ route('admin.positions.destroy', $position->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('คุณแน่ใจหรือไม่?');">
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

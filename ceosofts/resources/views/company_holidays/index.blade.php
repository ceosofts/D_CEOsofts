@extends('layouts.app')

@section('title', 'Company Holidays')

@section('content')
<div class="container">
    <h1 class="mb-4">ปฏิทินวันหยุดของบริษัท</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <a href="{{ route('company-holidays.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> เพิ่มวันหยุด
            </a>

            <div class="btn-group ms-2">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    ปี {{ $year }}
                </button>
                <ul class="dropdown-menu">
                    @foreach($years as $y)
                        <li>
                            <a class="dropdown-item {{ $y == $year ? 'active' : '' }}" 
                               href="{{ route('company-holidays.index', ['year' => $y]) }}">
                               {{ $y }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>วันที่</th>
                <th>ชื่อวันหยุด</th>
                <th class="text-center">การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($holidays as $key => $holiday)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ \Carbon\Carbon::parse($holiday->date)->translatedFormat('d M Y') }}</td>
                <td>{{ $holiday->name }}</td>
                <td class="text-center">
                    <a href="{{ route('company-holidays.edit', $holiday->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> แก้ไข
                    </a>
                    <form action="{{ route('company-holidays.destroy', $holiday->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณต้องการลบวันหยุดนี้ใช่หรือไม่?');">
                            <i class="fas fa-trash-alt"></i> ลบ
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $holidays->links() }}
    </div>
</div>
@endsection

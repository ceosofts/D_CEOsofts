@extends('layouts.app')

@section('title', 'Company Holidays')

@section('content')
<div class="container">
    <h1 class="mb-4">
        <i class="fas fa-calendar-day"></i> ปฏิทินวันหยุดของบริษัท
    </h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Add Holiday Button -->
        <div>
            <a href="{{ route('company-holidays.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> เพิ่มวันหยุด
            </a>
        </div>

        <!-- Dropdown for selecting year -->
        <div class="btn-group">
            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                ปี {{ $year }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
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

    <!-- Holiday Table -->
    <div class="table-responsive">
        @if($holidays->count())
            <table class="table table-sm table-striped table-hover">
                <thead class="table-dark">
                    <tr class="text-center">
                        <th>#</th>
                        <th>วันที่</th>
                        <th>ชื่อวันหยุด</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($holidays as $holiday)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{ optional(\Carbon\Carbon::parse($holiday->date))->translatedFormat('d F Y') }}
                            </td>
                            <td>{{ $holiday->name }}</td>
                            <td>
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
        @else
            <div class="alert alert-info text-center">ไม่มีข้อมูลวันหยุดสำหรับปี {{ $year }}</div>
        @endif
    </div>

    <!-- Pagination -->
    @if ($holidays->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $holidays->links() }}
        </div>
    @endif
</div>
@endsection
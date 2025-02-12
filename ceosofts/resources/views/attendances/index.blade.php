@extends('layouts.app')

@section('title', 'Attendance Records')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-user-clock"></i> บันทึกเวลาทำงาน</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('attendances.create') }}" class="btn btn-success btn-sm">
            <i class="fas fa-plus"></i> เพิ่มบันทึก
        </a>
    </div>

    {{-- ✅ ทำให้ตาราง Responsive --}}
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>พนักงาน</th>
                    <th>วันที่</th>
                    <th>เวลาเข้า</th>
                    <th>เวลาออก</th>
                    <th>ชั่วโมงทำงาน</th>
                    <th>ครบ 8 ชม.?</th>
                    <th>OT</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr class="text-center">
                        <td class="text-start">{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i') }}</td>
                        <td>{{ \Carbon\Carbon::parse($attendance->check_out)->format('H:i') }}</td>
                        <td>{{ number_format($attendance->work_hours, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $attendance->work_hours_completed ? 'success' : 'danger' }}">
                                {{ $attendance->work_hours_completed ? '✅ Yes' : '❌ No' }}
                            </span>
                        </td>
                        <td>{{ $attendance->overtime_hours > 0 ? number_format($attendance->overtime_hours, 2) : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $attendance->work_hours >= 8 ? 'success' : 'warning' }}">
                                {{ $attendance->work_hours >= 8 ? 'Normal' : 'Incomplete' }}
                            </span>
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('attendances.edit', $attendance->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> แก้ไข
                            </a>
                            <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('คุณต้องการลบข้อมูลนี้ใช่หรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> ลบ
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">ไม่มีข้อมูลการเข้างาน</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ แก้ปัญหา Pagination ซ้ำ และลดขนาด --}}
    @if ($attendances->hasPages())
        <div class="d-flex justify-content-center mt-2">
            {{ $attendances->links('vendor.pagination.bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

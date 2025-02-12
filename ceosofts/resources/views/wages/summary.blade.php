@extends('layouts.app')

@section('title', 'สรุปค่าแรงพนักงาน')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-hand-holding-usd"></i> สรุปค่าแรงพนักงาน</h1>

    {{-- ✅ ฟอร์มเลือกเดือน / ปี --}}
    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">เลือกเดือน</label>
            <select class="form-control" name="month">
                @foreach(range(1, 12) as $m)
                    <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" 
                        {{ $month == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">เลือกปี</label>
            <select class="form-control" name="year">
                @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> ค้นหา</button>
        </div>
    </form>

    {{-- ✅ ตารางสรุปค่าแรง --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>รหัสพนักงาน</th>  {{-- ✅ เพิ่มคอลัมน์ --}}
                    <th>พนักงาน</th>
                    <th>วันทำงาน</th>
                    <th>ค่าแรงรายวัน</th>
                    <th>ค่าแรงรวม</th>
                    <th>ชั่วโมง OT</th>
                    <th>ค่า OT</th>
                    <th>ยอดรวม</th>
                </tr>
            </thead>
<tbody>
    @foreach($wageSummaries as $wage)
        <tr class="text-center">
            <td>{{ $wage->employee->employee_code }}</td> {{-- ✅ แสดง employee_code --}}
            <td class="text-start">{{ $wage->employee->first_name }} {{ $wage->employee->last_name }}</td>
            <td>{{ $wage->work_days ?? 0 }}</td>
            <td>{{ number_format($wage->daily_wage ?? 0, 2) }}</td>
            <td>{{ number_format($wage->total_wage ?? 0, 2) }}</td>
            <td>{{ number_format($wage->ot_hours ?? 0, 2) }}</td>
            <td>{{ number_format($wage->ot_pay ?? 0, 2) }}</td>
            <td class="fw-bold">{{ number_format($wage->grand_total ?? 0, 2) }}</td>
        </tr>
    @endforeach
</tbody>

        </table>
    </div>

    {{-- ✅ ปุ่มบันทึกค่าแรงเดือนนี้ --}}
    <form action="{{ route('wages.storeMonthly') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> บันทึกค่าแรงเดือนนี้
        </button>
    </form>
</div>
@endsection

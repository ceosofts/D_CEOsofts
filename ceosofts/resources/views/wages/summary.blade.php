@extends('layouts.app')

@section('title','สรุปค่าแรงพนักงาน')

@section('content')
<div class="container">
    <h1 class="mb-4">
        <i class="fas fa-hand-holding-usd"></i> สรุปค่าแรงพนักงาน
    </h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ฟอร์มเลือกเดือน/ปี (GET) --}}
    <form method="GET" class="row mb-4">
        <div class="col-md-4">
            <label class="form-label">เลือกเดือน</label>
            <select class="form-control" name="month" onchange="this.form.submit()">
                @for($m=1; $m<=12; $m++)
                    @php
                        $mm = str_pad($m, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <option value="{{ $mm }}"
                        {{ $month == $mm ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">เลือกปี</label>
            <select class="form-control" name="year" onchange="this.form.submit()">
                @php
                    $currentYear = now()->year;
                @endphp
                @for($y=$currentYear-5; $y<=$currentYear+1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <!--
                ถ้าคุณต้องการ “ปุ่มค้นหา” ก็เก็บไว้ได้
                หรือจะลบออกไปเลยก็ได้
            -->
            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-filter"></i> ค้นหา
            </button>
        </div>
    </form>

    {{-- ตารางสรุปค่าแรง --}}
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>รหัสพนักงาน</th>
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
                    @php
                        $emp = $wage->employee;
                    @endphp
                    <tr class="text-center">
                        <td>{{ $emp->employee_code }}</td>
                        <td class="text-start">{{ $emp->first_name }} {{ $emp->last_name }}</td>
                        <td>{{ number_format($wage->work_days ?? 0, 2) }}</td>
                        <td>{{ number_format($wage->daily_wage ?? 0, 2) }}</td>
                        <td>{{ number_format($wage->total_wage ?? 0, 2) }}</td>
                        <td>{{ number_format($wage->ot_hours ?? 0, 2) }}</td>
                        <td>{{ number_format($wage->ot_pay ?? 0, 2) }}</td>
                        <td class="fw-bold">{{ number_format($wage->grand_total ?? 0, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <th colspan="3" class="text-end">รวมทั้งเดือน:</th>
                    <th>{{ number_format($wageSummaries->sum('daily_wage'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('total_wage'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('ot_hours'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('ot_pay'), 2) }}</th>
                    <th><strong>{{ number_format($wageSummaries->sum('grand_total'), 2) }}</strong></th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- ปุ่มบันทึกค่าแรงเดือนนี้ --}}
    <form action="{{ route('wages.storeMonthly') }}" method="POST" class="row">
        @csrf
        <!-- ส่ง month, year ไปด้วย -->
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year"  value="{{ $year }}">

        <div class="col-md-12">
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> บันทึกค่าแรงเดือนนี้
            </button>
        </div>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('title', 'สรุปค่าแรงพนักงาน')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-hand-holding-usd"></i> สรุปค่าแรงพนักงาน</h1>

    {{-- Display success messages --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('wages.summary') }}" class="row gy-3 gx-3 mb-4">
    {{-- <form method="GET" action="{{ route('payroll.index') }}" class="row gy-3 gx-3 mb-4"> --}}

        {{-- Month Filter --}}
        <div class="col-md-4">
            <label for="month" class="form-label">เลือกเดือน</label>
            <select class="form-select" name="month" id="month" onchange="this.form.submit()">
                @for($m = 1; $m <= 12; $m++)
                    @php
                        $mm = str_pad($m, 2, '0', STR_PAD_LEFT);
                    @endphp
                    <option value="{{ $mm }}" {{ ($month ?? '') == $mm ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- Year Filter --}}
        <div class="col-md-4">
            <label for="year" class="form-label">เลือกปี</label>
            @php $currentYear = now()->year; @endphp
            <select class="form-select" name="year" id="year" onchange="this.form.submit()">
                @for($y = $currentYear - 5; $y <= $currentYear + 1; $y++)
                    <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- Search Input --}}
        <div class="col-md-4">
            <label for="search" class="form-label">ค้นหา</label>
            <div class="input-group">
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Search by Employee, Code, or Month"
                       value="{{ old('search', $search) }}">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>
    </form>

    {{-- Payroll Summary Table --}}
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
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
                @forelse($wageSummaries as $wage)
                    @php $emp = $wage->employee; @endphp
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
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">ไม่พบข้อมูลค่าแรง</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <th colspan="3" class="text-end">รวมทั้งเดือน:</th>
                    <th>{{ number_format($wageSummaries->sum('daily_wage'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('total_wage'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('ot_hours'), 2) }}</th>
                    <th>{{ number_format($wageSummaries->sum('ot_pay'), 2) }}</th>
                    <th class="fw-bold">{{ number_format($wageSummaries->sum('grand_total'), 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Save Monthly Wages Button --}}
    <form action="{{ route('wages.storeMonthly') }}" method="POST" class="row mt-4">
        @csrf
        <input type="hidden" name="month" value="{{ $month }}">
        <input type="hidden" name="year" value="{{ $year }}">
        <div class="col-12">
            <button type="submit" class="btn btn-success w-100">
                <i class="fas fa-save"></i> บันทึกค่าแรงเดือนนี้
            </button>
        </div>
    </form>
</div>
@endsection
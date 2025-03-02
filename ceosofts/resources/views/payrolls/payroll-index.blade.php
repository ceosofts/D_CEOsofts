@extends('layouts.app')

@section('title', 'Payroll Slips')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-file-invoice-dollar"></i> Payroll Slips</h1>

    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <!-- ปุ่ม Create Payroll Slip -->
            <a href="{{ route('payroll.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Create Payroll Slip
            </a>
        </div>
        <div class="col-md-6">
            <!-- ฟอร์มค้นหา + เลือกเดือน/ปี (GET) -->
            <form method="GET" action="{{ route('payroll.index') }}" class="row gx-2 gy-2 align-items-center">
                <div class="col-auto">
                    <input type="text" name="search" class="form-control" placeholder="Search by Employee, Code, or Month" value="{{ old('search', $search) }}">
                </div>
                <div class="col-auto">
                    <select class="form-select" name="month" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @for($m = 1; $m <= 12; $m++)
                            @php
                                $mm = str_pad($m, 2, '0', STR_PAD_LEFT);
                                $monthName = \Carbon\Carbon::create()->month($m)->format('F');
                            @endphp
                            <option value="{{ $mm }}" {{ ($month ?? '') == $mm ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    @php $currentYear = now()->year; @endphp
                    <select class="form-select" name="year" onchange="this.form.submit()">
                        <option value="">All Years</option>
                        @for($y = $currentYear - 5; $y <= $currentYear + 1; $y++)
                            <option value="{{ $y }}" {{ ($year ?? '') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </div>
                <!-- ปุ่ม Filter ยังคงมีอยู่ สำหรับกรณีที่มีการค้นหาใน search box -->
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ตาราง Payroll Slips --}}
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
            <thead class="table-dark">
                <tr class="text-center">
                    <th>#</th>
                    <th>Employee Code</th>
                    <th>Employee Name</th>
                    <th>Month/Year</th>
                    <th>Total Income (THB)</th>
                    <th>Total Deductions (THB)</th>
                    <th>Net Income (THB)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payrolls as $payroll)
                    <tr>
                        <td>{{ $loop->iteration + ($payrolls->firstItem() - 1) }}</td>
                        <td>{{ $payroll->employee->employee_code ?? '-' }}</td>
                        <td>{{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</td>
                        <td>
                            {{-- แปลงจาก "YYYY-MM" เป็น "F Y" (เช่น "2025-02" -> "February 2025") --}}
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month_year)->format('F Y') }}
                        </td>
                        <td>{{ number_format($payroll->total_income, 2) }}</td>
                        <td>{{ number_format($payroll->total_deductions, 2) }}</td>
                        <td>{{ number_format($payroll->net_income, 2) }}</td>
                        <td>
                            <a href="{{ route('payroll.slip', $payroll->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('payroll.slip.pdf', $payroll->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-download"></i> PDF
                            </a>
                            <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('payroll.destroy', $payroll->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No payroll slips found.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="text-center">
                    <th colspan="3" class="text-end">รวมทั้งเดือน:</th>
                    <th>{{ number_format($payrolls->sum('total_income'), 2) }}</th>
                    <th>{{ number_format($payrolls->sum('total_deductions'), 2) }}</th>
                    <th>{{ number_format($payrolls->sum('net_income'), 2) }}</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($payrolls->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $payrolls->links() }}
        </div>
    @endif
</div>
@endsection
@extends('layouts.app')

@section('title', 'Payroll Slip')

@section('content')
<div class="container my-4">
    {{-- Action Buttons --}}
    <div class="mb-3 d-flex justify-content-between">
        <div>
            <a href="{{ route('payroll.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div>
            <a href="{{ route('payroll.edit', $payroll->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('payroll.pdf', $payroll->id) }}" class="btn btn-danger" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header text-center">
            <h3>PAYROLL SLIP</h3>
            @php
                $displayMonthYear = $payroll->month_year;
                if (preg_match('/^\d{4}-\d{2}$/', $payroll->month_year)) {
                    try {
                        $displayMonthYear = \Carbon\Carbon::createFromFormat('Y-m', $payroll->month_year)
                                            ->format('F Y');
                    } catch(\Exception $e) {
                        $displayMonthYear = $payroll->month_year;
                    }
                }
            @endphp
            <p class="mb-0">{{ $displayMonthYear }}</p>
        </div>

        <div class="card-body">
            {{-- Employee & Company Information --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Employee Name:</strong> 
                        {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}<br>
                    <strong>Join Date:</strong> 
                        {{ $payroll->employee->hire_date ? \Carbon\Carbon::parse($payroll->employee->hire_date)->format('d/m/Y') : '-' }}<br>
                    <strong>Employee Code:</strong> 
                        {{ $payroll->employee->employee_code }}<br>
                    <strong>Department:</strong> 
                        {{ $payroll->employee->department->name ?? '-' }}<br>
                    <strong>Position:</strong>
                        {{ $payroll->employee->position->name ?? '-' }}
                </div>
                <div class="col-md-6 text-end">
                    <strong>Company:</strong> {{ $company->name }}<br>
                    <strong>Tax ID:</strong> {{ $company->tax_id ?? '-' }}<br>
                    <strong>Address:</strong> {{ $company->address ?? '-' }}<br>
                    <strong>Phone:</strong> {{ $company->phone ?? '-' }}
                </div>
            </div>

            <hr>

            {{-- Income Section --}}
            <div class="row">
                <div class="col-md-6">
                    <h5>DESCRIPTION INCOME (THB)</h5>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td>Salary</td>
                            <td class="text-end">{{ number_format($payroll->salary, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Allowance</td>
                            <td class="text-end">{{ number_format($payroll->allowance, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Bonus</td>
                            <td class="text-end">{{ number_format($payroll->bonus, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Overtime</td>
                            <td class="text-end">{{ number_format($payroll->overtime, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Commission</td>
                            <td class="text-end">{{ number_format($payroll->commission, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Transport</td>
                            <td class="text-end">{{ number_format($payroll->transport, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Special Severance Pay</td>
                            <td class="text-end">{{ number_format($payroll->special_severance_pay, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Other Income</td>
                            <td class="text-end">{{ number_format($payroll->other_income, 2) }}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Total Income</th>
                            <th class="text-end">{{ number_format($payroll->total_income, 2) }}</th>
                        </tr>
                    </table>
                </div>

                {{-- Deductions Section --}}
                <div class="col-md-6">
                    <h5>DESCRIPTION DEDUCTIONS (THB)</h5>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td>Tax</td>
                            <td class="text-end">{{ number_format($payroll->tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Social Fund</td>
                            <td class="text-end">{{ number_format($payroll->social_fund, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Provident Fund</td>
                            <td class="text-end">{{ number_format($payroll->provident_fund, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Telephone Bill</td>
                            <td class="text-end">{{ number_format($payroll->telephone_bill, 2) }}</td>
                        </tr>
                        <tr>
                            <td>House Rental</td>
                            <td class="text-end">{{ number_format($payroll->house_rental, 2) }}</td>
                        </tr>
                        <tr>
                            <td>No Pay Leave</td>
                            <td class="text-end">{{ number_format($payroll->no_pay_leave, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Other Deductions</td>
                            <td class="text-end">{{ number_format($payroll->other_deductions, 2) }}</td>
                        </tr>
                        <tr class="table-primary">
                            <th>Total Deductions</th>
                            <th class="text-end">{{ number_format($payroll->total_deductions, 2) }}</th>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Net Income --}}
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <h4>Net Income: {{ number_format($payroll->net_income, 2) }} THB</h4>
                </div>
            </div>

            {{-- YTD Section --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5>YTD (THB)</h5>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td>YTD Income</td>
                            <td class="text-end">{{ number_format($payroll->ytd_income, 2) }}</td>
                        </tr>
                        <tr>
                            <td>YTD Tax</td>
                            <td class="text-end">{{ number_format($payroll->ytd_tax, 2) }}</td>
                        </tr>
                        <tr>
                            <td>YTD Social Fund</td>
                            <td class="text-end">{{ number_format($payroll->ytd_social_fund, 2) }}</td>
                        </tr>
                        <tr>
                            <td>YTD Provident Fund</td>
                            <td class="text-end">{{ number_format($payroll->ytd_provident_fund, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Accumulate Funds Section --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <h5>Accumulate Funds (THB)</h5>
                    <table class="table table-bordered table-sm">
                        <tr>
                            <td>Accumulate Provident Fund</td>
                            <td class="text-end">{{ number_format($payroll->accumulate_provident_fund ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Accumulate Social Fund</td>
                            <td class="text-end">{{ number_format($payroll->accumulate_social_fund ?? 0, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Remarks --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <strong>Remarks:</strong>
                    <p>{{ $payroll->remarks }}</p>
                </div>
            </div>

            {{-- Update Prepared By Section --}}
            <div class="row mt-4">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Created by: {{ $payroll->creator ? $payroll->creator->name : auth()->user()->name }}<br>
                        Date: {{ $payroll->created_at->format('d/m/Y H:i') }}
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted mb-0">
                        Last updated by: {{ $payroll->updater ? $payroll->updater->name : ($payroll->updated_at != $payroll->created_at ? auth()->user()->name : '-') }}<br>
                        Date: {{ $payroll->updated_at != $payroll->created_at ? $payroll->updated_at->format('d/m/Y H:i') : '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }
    .card-header h3 {
        margin-bottom: 0.5rem;
    }
    .text-end {
        text-align: right;
    }
</style>
@endpush
@endsection
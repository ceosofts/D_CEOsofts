@extends('layouts.app')

@section('title', 'Payroll Slip')

@section('content')
<div class="container my-4">
    <div class="card">
        <div class="card-header text-center">
            <h3>PAYROLL SLIP</h3>
            @php
                // แปลงจาก "YYYY-MM" เป็น "F Y" หากเป็นรูปแบบที่ถูกต้อง
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
            <p>{{ $displayMonthYear }}</p>
        </div>
        <div class="card-body">
            {{-- Employee & Company Information --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Employee Name:</strong> 
                        {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}<br>
                    <strong>Join Date:</strong> 
                        {{ $payroll->employee->hire_date ? $payroll->employee->hire_date->format('Y-m-d') : '-' }}<br>
                    <strong>Employee Code:</strong> 
                        {{ $payroll->employee->employee_code }}<br>
                    <strong>Department:</strong> 
                        {{ $payroll->employee->department->name ?? '-' }}<br>
                </div>
                <div class="col-md-6 text-end">
                    <strong>Company:</strong> {{ $company->name }}
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

            {{-- Prepared By --}}
            <div class="row">
                <div class="col-md-12 text-end">
                    <p>Prepared by: {{ $payroll->prepared_by ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
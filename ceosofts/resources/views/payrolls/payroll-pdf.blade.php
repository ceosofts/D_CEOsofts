<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            src: url({{ storage_path('fonts/THSarabunNew.ttf') }}) format('truetype');
        }

        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 13px;
            line-height: 1.2;
            margin: 0;
            padding: 10px;
        }

        .container { 
            width: 100%; 
            max-width: 800px;
            margin: 0 auto;
        }

        .card { 
            border: 1px solid #ddd; 
            padding: 10px;
            background-color: #fff;
        }

        /* Header Styles */
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        /* Layout Helpers */
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .clear { clear: both; }

        /* Grid System */
        .row { 
            clear: both; 
            margin-bottom: 10px;
            overflow: hidden;
        }
        .col-6 { 
            width: 48%;
            float: left; 
            margin-right: 2%;
        }
        .col-6:last-child { margin-right: 0; }

        /* Table Styles */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 4px 6px;
            font-size: 12px;
        }
        .table-primary { 
            background-color: #f8f9fa;
            font-weight: bold;
        }

        /* Summary Section */
        .summary-section {
            width: 48%;
            float: left;
            margin-right: 2%;
            margin-bottom: 10px;
        }
        .summary-section:last-child { margin-right: 0; }
        
        /* Footer Section */
        .footer-section {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-top: 15px;
            font-size: 11px;
        }

        /* Net Income Highlight */
        .net-income {
            background-color: #f8f9fa;
            padding: 5px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: inline-block;
            margin: 10px 0;
        }

        /* Signature Section */
        .signature {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dotted #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header text-center">
                <h3 style="margin: 0;">PAYROLL SLIP</h3>
                <p>{{ \Carbon\Carbon::createFromFormat('Y-m', $payroll->month_year)->format('F Y') }}</p>
            </div>

            {{-- Employee & Company Information --}}
            <div class="row mb-3">
                <div class="col-6">
                    <p><strong>Employee Name:</strong> {{ $payroll->employee->first_name }} {{ $payroll->employee->last_name }}</p>
                    <p><strong>Employee Code:</strong> {{ $payroll->employee->employee_code }}</p>
                    <p><strong>Department:</strong> {{ $payroll->employee->department->name ?? '-' }}</p>
                </div>
                <div class="col-6 text-end">
                    <p><strong>Company:</strong> {{ $company->name }}</p>
                </div>
            </div>

            {{-- Income & Deductions --}}
            <div class="row">
                <div class="col-6">
                    <h5>DESCRIPTION INCOME (THB)</h5>
                    <table class="table">
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

                <div class="col-6">
                    <h5>DESCRIPTION DEDUCTIONS (THB)</h5>
                    <table class="table">
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
            <div class="text-end net-income">
                <h4 style="font-size: 12px;">Net Income: {{ number_format($payroll->net_income, 2) }} THB</h4>
            </div>

            {{-- ปรับโครงสร้าง YTD และ Accumulate Funds ให้อยู่ในแถวเดียวกัน --}}
            <div class="row">
                <div class="summary-section">
                    <h5>YTD (THB)</h5>
                    <table class="table">
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

                <div class="summary-section">
                    <h5>Accumulate Funds (THB)</h5>
                    <table class="table">
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

            <div class="clear"></div>

            {{-- Updated Footer Section --}}
            <div class="footer-section">
                @if($payroll->remarks)
                <div class="remarks">
                    <strong>Remarks:</strong> {{ $payroll->remarks }}
                </div>
                @endif
                
                <div class="signature">
                    <div class="text-end">
                        <p>
                            Prepared by: {{ $payroll->creator ? $payroll->creator->name : ($payroll->prepared_by ?? auth()->user()->name) }}
                            <br>
                            Date: {{ $payroll->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
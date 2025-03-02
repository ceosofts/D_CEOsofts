{{-- resources/views/payrolls/payroll-edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Payroll Slip')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Payroll Slip</h1>

    @if($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
        // แยก month/year จาก $payroll->month_year = "YYYY-MM"
        $yearStr  = substr($payroll->month_year, 0, 4);
        $monthStr = substr($payroll->month_year, 5, 2);
    @endphp

    <form action="{{ route('payroll.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Employee -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-select" required>
                <option value="">-- Select Employee --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                      {{ $payroll->employee_id == $emp->id ? 'selected' : '' }}>
                      {{ $emp->first_name }} {{ $emp->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Month / Year -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="month" class="form-label">Month</label>
            <input type="text" name="month" id="month" class="form-control"
                   value="{{ old('month', $monthStr) }}" required>
          </div>
          <div class="col-md-6 mb-3">
            <label for="year" class="form-label">Year</label>
            <input type="text" name="year" id="year" class="form-control"
                   value="{{ old('year', $yearStr) }}" required>
          </div>
        </div>

        <!-- Salary -->
        <div class="mb-3">
            <label for="salary" class="form-label">Salary (THB)</label>
            <input type="number" step="0.01" name="salary" id="salary" class="form-control"
                   value="{{ old('salary', $payroll->salary) }}" oninput="calcPayroll()">
        </div>

        <!-- Overtime -->
        <div class="mb-3">
            <label for="overtime" class="form-label">Overtime (THB)</label>
            <input type="number" step="0.01" name="overtime" id="overtime" class="form-control"
                   value="{{ old('overtime', $payroll->overtime) }}" oninput="calcPayroll()">
        </div>

        <!-- Bonus -->
        <div class="mb-3">
            <label for="bonus" class="form-label">Bonus (THB)</label>
            <input type="number" step="0.01" name="bonus" id="bonus" class="form-control"
                   value="{{ old('bonus', $payroll->bonus) }}" oninput="calcPayroll()">
        </div>

        <!-- Commission -->
        <div class="mb-3">
            <label for="commission" class="form-label">Commission (THB)</label>
            <input type="number" step="0.01" name="commission" id="commission" class="form-control"
                   value="{{ old('commission', $payroll->commission) }}" oninput="calcPayroll()">
        </div>

        <!-- Transport -->
        <div class="mb-3">
            <label for="transport" class="form-label">Transport (THB)</label>
            <input type="number" step="0.01" name="transport" id="transport" class="form-control"
                   value="{{ old('transport', $payroll->transport) }}" oninput="calcPayroll()">
        </div>

        <!-- Special Severance Pay -->
        <div class="mb-3">
            <label for="special_severance_pay" class="form-label">Special Severance Pay (THB)</label>
            <input type="number" step="0.01" name="special_severance_pay" id="special_severance_pay" class="form-control"
                   value="{{ old('special_severance_pay', $payroll->special_severance_pay) }}" oninput="calcPayroll()">
        </div>

        <!-- Other Income -->
        <div class="mb-3">
            <label for="other_income" class="form-label">Other Income (THB)</label>
            <input type="number" step="0.01" name="other_income" id="other_income" class="form-control"
                   value="{{ old('other_income', $payroll->other_income) }}" oninput="calcPayroll()">
        </div>

        <!-- Tax -->
        <div class="mb-3">
            <label for="tax" class="form-label">Tax (THB)</label>
            <input type="number" step="0.01" name="tax" id="tax" class="form-control"
                   value="{{ old('tax', $payroll->tax) }}" oninput="calcPayroll()">
        </div>

        <!-- Social Fund -->
        <div class="mb-3">
            <label for="social_fund" class="form-label">Social Fund (THB)</label>
            <input type="number" step="0.01" name="social_fund" id="social_fund" class="form-control"
                   value="{{ old('social_fund', $payroll->social_fund) }}" oninput="calcPayroll()">
        </div>

        <!-- Provident Fund -->
        <div class="mb-3">
            <label for="provident_fund" class="form-label">Provident Fund (THB)</label>
            <input type="number" step="0.01" name="provident_fund" id="provident_fund" class="form-control"
                   value="{{ old('provident_fund', $payroll->provident_fund) }}" oninput="calcPayroll()">
        </div>

        <!-- Telephone Bill -->
        <div class="mb-3">
            <label for="telephone_bill" class="form-label">Telephone Bill (THB)</label>
            <input type="number" step="0.01" name="telephone_bill" id="telephone_bill" class="form-control"
                   value="{{ old('telephone_bill', $payroll->telephone_bill) }}" oninput="calcPayroll()">
        </div>

        <!-- House Rental -->
        <div class="mb-3">
            <label for="house_rental" class="form-label">House Rental (THB)</label>
            <input type="number" step="0.01" name="house_rental" id="house_rental" class="form-control"
                   value="{{ old('house_rental', $payroll->house_rental) }}" oninput="calcPayroll()">
        </div>

        <!-- No Pay Leave -->
        <div class="mb-3">
            <label for="no_pay_leave" class="form-label">No Pay Leave (THB)</label>
            <input type="number" step="0.01" name="no_pay_leave" id="no_pay_leave" class="form-control"
                   value="{{ old('no_pay_leave', $payroll->no_pay_leave) }}" oninput="calcPayroll()">
        </div>

        <!-- Other Deductions -->
        <div class="mb-3">
            <label for="other_deductions" class="form-label">Other Deductions (THB)</label>
            <input type="number" step="0.01" name="other_deductions" id="other_deductions" class="form-control"
                   value="{{ old('other_deductions', $payroll->other_deductions) }}" oninput="calcPayroll()">
        </div>

        <!-- สรุป: total_income, total_deductions, net_income -->
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="total_income" class="form-label">Total Income (THB)</label>
            <input type="number" step="0.01" name="total_income" id="total_income" class="form-control"
                   value="{{ old('total_income', $payroll->total_income) }}" readonly>
          </div>
          <div class="col-md-4 mb-3">
            <label for="total_deductions" class="form-label">Total Deductions (THB)</label>
            <input type="number" step="0.01" name="total_deductions" id="total_deductions" class="form-control"
                   value="{{ old('total_deductions', $payroll->total_deductions) }}" readonly>
          </div>
          <div class="col-md-4 mb-3">
            <label for="net_income" class="form-label">Net Income (THB)</label>
            <input type="number" step="0.01" name="net_income" id="net_income" class="form-control"
                   value="{{ old('net_income', $payroll->net_income) }}" readonly>
          </div>
        </div>

        <!-- YTD Income, Tax, Social Fund, Provident Fund -->
        <div class="mb-3">
          <label for="ytd_income" class="form-label">YTD Income</label>
          <input type="number" step="0.01" name="ytd_income" id="ytd_income" class="form-control"
                 value="{{ old('ytd_income', $payroll->ytd_income) }}">
        </div>
        <div class="mb-3">
          <label for="ytd_tax" class="form-label">YTD Tax</label>
          <input type="number" step="0.01" name="ytd_tax" id="ytd_tax" class="form-control"
                 value="{{ old('ytd_tax', $payroll->ytd_tax) }}">
        </div>
        <div class="mb-3">
          <label for="ytd_social_fund" class="form-label">YTD Social Fund</label>
          <input type="number" step="0.01" name="ytd_social_fund" id="ytd_social_fund" class="form-control"
                 value="{{ old('ytd_social_fund', $payroll->ytd_social_fund) }}">
        </div>
        <div class="mb-3">
          <label for="ytd_provident_fund" class="form-label">YTD Provident Fund</label>
          <input type="number" step="0.01" name="ytd_provident_fund" id="ytd_provident_fund" class="form-control"
                 value="{{ old('ytd_provident_fund', $payroll->ytd_provident_fund) }}">
        </div>

        <!-- Accumulate Funds (ถ้ามี) -->
        <div class="mb-3">
          <label for="accumulate_provident_fund" class="form-label">Accumulate Provident Fund</label>
          <input type="number" step="0.01" name="accumulate_provident_fund" id="accumulate_provident_fund" class="form-control"
                 value="{{ old('accumulate_provident_fund', $payroll->accumulate_provident_fund ?? 0) }}">
        </div>
        <div class="mb-3">
          <label for="accumulate_social_fund" class="form-label">Accumulate Social Fund</label>
          <input type="number" step="0.01" name="accumulate_social_fund" id="accumulate_social_fund" class="form-control"
                 value="{{ old('accumulate_social_fund', $payroll->accumulate_social_fund ?? 0) }}">
        </div>

        <!-- Remarks -->
        <div class="mb-3">
          <label for="remarks" class="form-label">Remarks</label>
          <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks', $payroll->remarks) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('payroll.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
function getNum(id) {
  let val = parseFloat(document.getElementById(id).value);
  return isNaN(val) ? 0 : val;
}

function calcPayroll() {
  // รายได้
  let salary   = getNum('salary');
  let overtime = getNum('overtime');
  let bonus    = getNum('bonus');
  let comm     = getNum('commission');
  let trans    = getNum('transport');
  let ssp      = getNum('special_severance_pay');
  let otherInc = getNum('other_income');

  let totalIncome = salary + overtime + bonus + comm + trans + ssp + otherInc;
  document.getElementById('total_income').value = totalIncome.toFixed(2);

  // รายการหัก
  let tax  = getNum('tax');
  let sf   = getNum('social_fund');
  let pf   = getNum('provident_fund');
  let tel  = getNum('telephone_bill');
  let hr   = getNum('house_rental');
  let npl  = getNum('no_pay_leave');
  let od   = getNum('other_deductions');

  let totalDeduc = tax + sf + pf + tel + hr + npl + od;
  document.getElementById('total_deductions').value = totalDeduc.toFixed(2);

  // Net Income
  let net = totalIncome - totalDeduc;
  document.getElementById('net_income').value = net.toFixed(2);
}
</script>
@endsection
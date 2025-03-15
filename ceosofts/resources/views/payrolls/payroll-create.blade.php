@extends('layouts.app')

@section('title', 'Create New Payroll Slip')

@section('content')
<div class="container">
    <h1 class="mb-4">Create New Payroll Slip</h1>

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('payroll.store') }}" method="POST" onsubmit="return validateForm()">
        @csrf
        
        {{-- Add hidden fields for tracking --}}
        <input type="hidden" name="created_by" value="{{ auth()->id() }}">
        <input type="hidden" name="updated_by" value="{{ auth()->id() }}">

        <!-- เลือก Employee -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-select" onchange="autoRedirect()">
                <option value="">-- Select Employee --</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                        {{ old('employee_id', $selectedEmployeeId) == $emp->id ? 'selected' : '' }}>
                        {{ $emp->first_name }} {{ $emp->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- เลือก Month/Year -->
        <div class="mb-3">
            <label for="month" class="form-label">Month</label>
            <select name="month" id="month" class="form-select" onchange="autoRedirect()">
                <option value="">-- Select Month --</option>
                @for($m=1; $m<=12; $m++)
                    @php
                      $mm = str_pad($m,2,'0',STR_PAD_LEFT);
                      $monthName = \Carbon\Carbon::create()->month($m)->format('F');
                    @endphp
                    <option value="{{ $mm }}"
                        {{ old('month', $selectedMonth) == $mm ? 'selected' : '' }}>
                        {{ $monthName }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="mb-3">
            <label for="year" class="form-label">Year</label>
            <select name="year" id="year" class="form-select" onchange="autoRedirect()">
                <option value="">-- Select Year --</option>
                @php
                  $currentYear = now()->year;
                @endphp
                @for($y=$currentYear-5; $y<=$currentYear+1; $y++)
                    <option value="{{ $y }}"
                        {{ old('year', $selectedYear) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

        <!-- กลุ่มฟิลด์รายได้ -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="salary" class="form-label">Salary (THB)</label>
            <input type="number" step="0.01" name="salary" id="salary" class="form-control"
                   value="{{ old('salary', $autoFill['salary'] ?? 0) }}" oninput="calcPayroll()">
          </div>
          <div class="col-md-6 mb-3">
            <label for="overtime" class="form-label">Overtime (THB)</label>
            <input type="number" step="0.01" name="overtime" id="overtime" class="form-control"
                   value="{{ old('overtime', $autoFill['overtime'] ?? 0) }}" oninput="calcPayroll()">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="bonus" class="form-label">Bonus (THB)</label>
            <input type="number" step="0.01" name="bonus" id="bonus" class="form-control"
                   value="{{ old('bonus', $autoFill['bonus'] ?? 0) }}" oninput="calcPayroll()">
          </div>
          <div class="col-md-6 mb-3">
            <label for="commission" class="form-label">Commission (THB)</label>
            <input type="number" step="0.01" name="commission" id="commission" class="form-control"
                   value="{{ old('commission', $autoFill['commission'] ?? 0) }}" oninput="calcPayroll()">
          </div>
        </div>

        <div class="mb-3">
          <label for="transport" class="form-label">Transport (THB)</label>
          <input type="number" step="0.01" name="transport" id="transport" class="form-control"
                 value="{{ old('transport', $autoFill['transport'] ?? 0) }}" oninput="calcPayroll()">
        </div>

        <div class="mb-3">
          <label for="special_severance_pay" class="form-label">Special Severance Pay (THB)</label>
          <input type="number" step="0.01" name="special_severance_pay" id="special_severance_pay" class="form-control"
                 value="{{ old('special_severance_pay', $autoFill['special_severance_pay'] ?? 0) }}" oninput="calcPayroll()">
        </div>

        <div class="mb-3">
          <label for="other_income" class="form-label">Other Income (THB)</label>
          <input type="number" step="0.01" name="other_income" id="other_income" class="form-control"
                 value="{{ old('other_income', $autoFill['other_income'] ?? 0) }}" oninput="calcPayroll()">
        </div>

        <!-- กลุ่มฟิลด์รายการหัก -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="tax" class="form-label">Tax (THB)</label>
            <input type="number" step="0.01" name="tax" id="tax" class="form-control"
                   value="{{ old('tax', $autoFill['tax'] ?? 0) }}" oninput="calcPayroll()">
          </div>
          <div class="col-md-6 mb-3">
            <label for="social_fund" class="form-label">Social Fund (THB)</label>
            <input type="number" step="0.01" name="social_fund" id="social_fund" class="form-control"
                   value="{{ old('social_fund', $autoFill['social_fund'] ?? 0) }}" oninput="calcPayroll()">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="provident_fund" class="form-label">Provident Fund (THB)</label>
            <input type="number" step="0.01" name="provident_fund" id="provident_fund" class="form-control"
                   value="{{ old('provident_fund', $autoFill['provident_fund'] ?? 0) }}" oninput="calcPayroll()">
          </div>
          <div class="col-md-6 mb-3">
            <label for="telephone_bill" class="form-label">Telephone Bill (THB)</label>
            <input type="number" step="0.01" name="telephone_bill" id="telephone_bill" class="form-control"
                   value="{{ old('telephone_bill', $autoFill['telephone_bill'] ?? 0) }}" oninput="calcPayroll()">
          </div>
        </div>

        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="house_rental" class="form-label">House Rental (THB)</label>
            <input type="number" step="0.01" name="house_rental" id="house_rental" class="form-control"
                   value="{{ old('house_rental', $autoFill['house_rental'] ?? 0) }}" oninput="calcPayroll()">
          </div>
          <div class="col-md-6 mb-3">
            <label for="no_pay_leave" class="form-label">No Pay Leave (THB)</label>
            <input type="number" step="0.01" name="no_pay_leave" id="no_pay_leave" class="form-control"
                   value="{{ old('no_pay_leave', $autoFill['no_pay_leave'] ?? 0) }}" oninput="calcPayroll()">
          </div>
        </div>

        <div class="mb-3">
          <label for="other_deductions" class="form-label">Other Deductions (THB)</label>
          <input type="number" step="0.01" name="other_deductions" id="other_deductions" class="form-control"
                 value="{{ old('other_deductions', $autoFill['other_deductions'] ?? 0) }}" oninput="calcPayroll()">
        </div>

        <!-- สรุปผล -->
        <div class="row">
          <div class="col-md-4 mb-3">
            <label for="total_income" class="form-label">Total Income (THB)</label>
            <input type="number" step="0.01" name="total_income" id="total_income" class="form-control"
                   value="{{ old('total_income', $autoFill['total_income'] ?? 0) }}" readonly>
          </div>
          <div class="col-md-4 mb-3">
            <label for="total_deductions" class="form-label">Total Deductions (THB)</label>
            <input type="number" step="0.01" name="total_deductions" id="total_deductions" class="form-control"
                   value="{{ old('total_deductions', $autoFill['total_deductions'] ?? 0) }}" readonly>
          </div>
          <div class="col-md-4 mb-3">
            <label for="net_income" class="form-label">Net Income (THB)</label>
            <input type="number" step="0.01" name="net_income" id="net_income" class="form-control"
                   value="{{ old('net_income', $autoFill['net_income'] ?? 0) }}" readonly>
          </div>
        </div>

        <!-- เงินสะสม -->
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="accumulate_provident_fund" class="form-label">Accumulate Provident Fund (THB)</label>
            <input type="number" step="0.01" name="accumulate_provident_fund" id="accumulate_provident_fund" class="form-control"
                   value="{{ old('accumulate_provident_fund', $autoFill['accumulate_provident_fund'] ?? 0) }}">
          </div>
          <div class="col-md-6 mb-3">
            <label for="accumulate_social_fund" class="form-label">Accumulate Social Fund (THB)</label>
            <input type="number" step="0.01" name="accumulate_social_fund" id="accumulate_social_fund" class="form-control"
                   value="{{ old('accumulate_social_fund', $autoFill['accumulate_social_fund'] ?? 0) }}">
          </div>
        </div>

        <div class="mb-3">
          <label for="remarks" class="form-label">Remarks</label>
          <textarea name="remarks" id="remarks" class="form-control" rows="3">{{ old('remarks', $autoFill['remarks'] ?? '') }}</textarea>
        </div>

        {{-- Add creator info display --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <p class="text-muted">
                    Creating by: {{ auth()->user()->name }}
                    <br>
                    Date: {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('payroll.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
// ฟังก์ชัน parse float หรือคืนค่า 0 ถ้าไม่ใช่ตัวเลข
function getNum(id) {
  let val = parseFloat(document.getElementById(id).value);
  return isNaN(val) ? 0 : val;
}

// คำนวณเมื่อแก้ไขตัวเลข
function calcPayroll() {
    // รายได้
    let salary = getNum('salary');
    let overtime = getNum('overtime');
    let bonus = getNum('bonus');
    let comm = getNum('commission');
    let transport = getNum('transport');
    let ssp = getNum('special_severance_pay');
    let otherInc = getNum('other_income');

    let totalIncome = salary + overtime + bonus + comm + transport + ssp + otherInc;
    document.getElementById('total_income').value = totalIncome.toFixed(2);

    // รายการหัก
    let tax = getNum('tax');
    let sf = getNum('social_fund');
    let pf = getNum('provident_fund');
    let tel = getNum('telephone_bill');
    let house = getNum('house_rental');
    let npl = getNum('no_pay_leave');
    let otherDed = getNum('other_deductions');

    let totalDeductions = tax + sf + pf + tel + house + npl + otherDed;
    document.getElementById('total_deductions').value = totalDeductions.toFixed(2);

    // Net Income = Total Income - Total Deductions
    let netIncome = totalIncome - totalDeductions;
    document.getElementById('net_income').value = netIncome.toFixed(2);
}

function autoRedirect() {
    // autoRedirect เมื่อเปลี่ยน employee, month, year
    let empId = document.getElementById('employee_id').value;
    let m = document.getElementById('month').value;
    let y = document.getElementById('year').value;

    if(empId && m && y) {
        // เพิ่ม loading indicator
        document.body.style.cursor = 'wait';
        
        let url = "{{ route('payroll.create') }}"
                + "?employee_id=" + empId
                + "&month=" + m
                + "&year=" + y;
        
        // ตรวจสอบว่ามี payroll อยู่แล้วหรือไม่
        fetch("{{ route('payroll.check') }}?employee_id=" + empId + "&month=" + m + "&year=" + y)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    if (confirm('Payroll slip already exists for this period. Do you want to view it?')) {
                        window.location = "{{ url('payroll-slip') }}/" + data.payroll.id;
                    }
                } else {
                    window.location = url;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                window.location = url;
            })
            .finally(() => {
                document.body.style.cursor = 'default';
            });
    }
}

// Add form validation before submit
document.querySelector('form').onsubmit = function(e) {
    return validateForm();
};

// Calculate initial values
calcPayroll();

// เพิ่มฟังก์ชันตรวจสอบข้อมูลก่อน submit
function validateForm() {
    let empId = document.getElementById('employee_id').value;
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let salary = document.getElementById('salary').value;

    if (!empId || !month || !year) {
        alert('Please select Employee, Month and Year');
        return false;
    }

    if (!salary || parseFloat(salary) <= 0) {
        alert('Please enter valid salary amount');
        return false;
    }

    return true;
}

// อัพเดทฟังก์ชัน validateForm
function validateForm() {
    let empId = document.getElementById('employee_id').value;
    let month = document.getElementById('month').value;
    let year = document.getElementById('year').value;
    let salary = document.getElementById('salary').value;

    if (!empId || !month || !year) {
        alert('กรุณาเลือกพนักงาน เดือน และปี');
        return false;
    }

    if (!salary || parseFloat(salary) <= 0) {
        alert('กรุณาใส่เงินเดือนให้ถูกต้อง');
        return false;
    }

    // แสดง loading
    document.body.style.cursor = 'wait';
    document.querySelector('button[type="submit"]').disabled = true;
    
    return true;
}

// เพิ่มการตรวจจับ error จาก fetch
function handleFetchError(error) {
    console.error('Error:', error);
    document.body.style.cursor = 'default';
    alert('เกิดข้อผิดพลาดในการตรวจสอบข้อมูล กรุณาลองใหม่อีกครั้ง');
}

// อัพเดทฟังก์ชัน autoRedirect
function autoRedirect() {
    let empId = document.getElementById('employee_id').value;
    let m = document.getElementById('month').value;
    let y = document.getElementById('year').value;

    if(empId && m && y) {
        document.body.style.cursor = 'wait';
        
        fetch(`{{ route('payroll.check') }}?employee_id=${empId}&month=${m}&year=${y}`)
            .then(response => response.json())
            .then(data => {
                if (data.found) {
                    if (confirm('พบข้อมูล Payroll ในช่วงเวลานี้แล้ว ต้องการดูข้อมูลหรือไม่?')) {
                        window.location = "{{ url('payroll-slip') }}/" + data.payroll.id;
                    }
                } else {
                    window.location = `{{ route('payroll.create') }}?employee_id=${empId}&month=${m}&year=${y}`;
                }
            })
            .catch(handleFetchError)
            .finally(() => {
                document.body.style.cursor = 'default';
            });
    }
}

// Calculate initial values
calcPayroll();
</script>
@endsection
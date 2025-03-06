@extends('layouts.app')

@section('title', 'Record Attendance')

@section('content')
<div class="container">
    <h1 class="mb-4">Record Attendance</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attendances.store') }}" method="POST">
        @csrf

        <!-- Employee Selection -->
        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select name="employee_id" id="employee_id" class="form-control" required>
                <option value="" disabled {{ old('employee_id') ? '' : 'selected' }}>-- Select Employee --</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" value="{{ old('date') }}" required>
        </div>

        <!-- Check-in Time -->
        <div class="mb-3">
            <label for="check_in" class="form-label">Check-in Time</label>
            <input type="datetime-local" name="check_in" id="check_in" class="form-control" value="{{ old('check_in') }}" required>
        </div>

        <!-- Check-out Time -->
        <div class="mb-3">
            <label for="check_out" class="form-label">Check-out Time</label>
            <input type="datetime-local" name="check_out" id="check_out" class="form-control" value="{{ old('check_out') }}" required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>

<script>
// Example: log form data on submit (for debugging purposes)
document.querySelector('form').addEventListener('submit', function(event) {
    const formData = Object.fromEntries(new FormData(this).entries());
    console.log("Form Submitted", formData);
});
</script>
@endsection
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
            <select class="form-control" name="employee_id" required>
                <option value="" disabled selected>-- Select Employee --</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" required>
        </div>

<div class="mb-3">
    <label for="check_in" class="form-label">Check-in Time</label>
    <input type="datetime-local" class="form-control" name="check_in" required>
</div>

<div class="mb-3">
    <label for="check_out" class="form-label">Check-out Time</label>
    <input type="datetime-local" class="form-control" name="check_out" required>
</div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(event) {
    console.log("Form Submitted", new FormData(this));
});
</script>

@endsection

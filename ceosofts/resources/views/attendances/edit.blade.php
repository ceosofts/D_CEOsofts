@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Attendance</h2>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('attendances.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="employee_id" class="form-label">Employee</label>
            <select class="form-control" name="employee_id" required>
                @foreach ($employees as $employee)
                    <option value="{{ $employee->id }}" {{ $employee->id == $attendance->employee_id ? 'selected' : '' }}>
                        {{ $employee->first_name }} {{ $employee->last_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" class="form-control" name="date" value="{{ $attendance->date }}" required>
        </div>

        <div class="mb-3">
            <label for="check_in" class="form-label">Check-in Time</label>
            <input type="datetime-local" class="form-control" name="check_in" value="{{ \Carbon\Carbon::parse($attendance->check_in)->format('Y-m-d\TH:i') }}" required>
        </div>

        <div class="mb-3">
            <label for="check_out" class="form-label">Check-out Time</label>
            <input type="datetime-local" class="form-control" name="check_out" value="{{ \Carbon\Carbon::parse($attendance->check_out)->format('Y-m-d\TH:i') }}" required>
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('attendances.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

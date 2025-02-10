@extends('layouts.app')

@section('title', 'Employees')

@section('content')
<div class="container">
    <h1>Employee Management</h1>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="{{ route('employees.create') }}" class="btn btn-success mb-3">Add Employee</a>

        <form method="GET" action="{{ route('employees.index') }}" class="d-flex flex-grow-1 ms-3">
            <input type="text" name="search" class="form-control me-2"  placeholder="Search by name, id, phonenumber" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary" >Filters</button>
        </form>
        
    </div>


    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Position</th>
                <th>Age</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($employees as $key => $employee)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                    <td>{{ $employee->email }}</td>
                    <td>{{ $employee->department->name ?? '-' }}</td>
                    <td>{{ $employee->position->name ?? '-' }}</td>
                    <td>{{ $employee->age }}</td>
                    <td>
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Are you sure you want to delete this employee?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
    {{ $employees->links() }}
    </div>
</div>
@endsection

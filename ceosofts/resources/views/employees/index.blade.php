@extends('layouts.app')

@section('title', 'Employees')

@section('content')

<div class="container">
    <h1><i class="fas fa-users"></i> Employee Management</h1>

    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <a href="{{ route('employees.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Employee
            </a>
        </div>

        <div class="col-md-6">
            <form method="GET" action="{{ route('employees.index') }}" class="d-flex">
                <input type="text" name="search" class="form-control me-2"
                       placeholder="Search by name, ID, phone number"
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-filter"></i> Filters
                </button>
            </form>
        </div>
    </div>

    {{-- ✅ ทำให้ตาราง Responsive --}}
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Employee Code</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Age</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $loop->iteration + ($employees->firstItem() - 1) }}</td>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>{{ $employee->email ?: '-' }}</td>
                        <td>{{ $employee->department->name ?? '-' }}</td>
                        <td>{{ $employee->position->name ?? '-' }}</td>
                        <td>{{ $employee->age ?: '-' }}</td>
                        <td>
                            {{-- 🔹 เอาโค้ดปุ่มกลับมาใส่ตรงนี้แทน Partial View --}}
                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No employees found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ ใช้ @isset เพื่อซ่อน Pagination ถ้าไม่มีหลายหน้า --}}
    @isset($employees)
        @if ($employees->hasPages())
            <div class="d-flex justify-content-center mt-3">
                {{ $employees->links() }}
            </div>
        @endif
    @endisset
</div>

@endsection

@extends('layouts.app')

@section('title', 'Departments')

@section('content')
<div class="container">
    <h1>Department List</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.departments.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Add Department
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($departments as $department)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $department->name }}</td>
                    <td>
                        <a href="{{ route('admin.departments.edit', $department->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.departments.destroy', $department->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No departments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Units')

@section('content')
<div class="container">
    <h1 class="mb-4">Unit List</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.units.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Add Unit
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Unit Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($units as $unit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $unit->name }}</td>
                    <td>
                        <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this unit?')">
                                <i class="bi bi-trash"></i> Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center">No units found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- แสดง Pagination ถ้ามี --}}
    <div class="d-flex justify-content-center">
        {{ $units->links() }}
    </div>
</div>
@endsection
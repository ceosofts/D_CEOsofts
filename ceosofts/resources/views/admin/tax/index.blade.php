@extends('layouts.app')

@section('title', 'Tax Settings')

@section('content')
<div class="container">
    <h1>Tax Settings</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.tax.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> Add Tax
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tax Name</th>
                <th>Rate (%)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($taxes as $tax)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $tax->name }}</td>
                    <td>{{ $tax->rate }}</td>
                    <td>
                        <a href="{{ route('admin.tax.edit', $tax->id) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.tax.destroy', $tax->id) }}" method="POST" style="display:inline;">
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
                    <td colspan="4" class="text-center">No tax settings found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

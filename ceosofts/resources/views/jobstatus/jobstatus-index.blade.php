@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Job Statuses</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('admin.job-statuses.create') }}" class="btn btn-primary mb-3">Create Status</a>

    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Active</th>
                        <th>Sort Order</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($statuses as $status)
                    <tr>
                        <td>
                            <span class="badge" style="background-color: {{ $status->color }}">
                                {{ $status->name }}
                            </span>
                        </td>
                        <td>{{ $status->color }}</td>
                        <td>{{ $status->is_active ? 'Yes' : 'No' }}</td>
                        <td>{{ $status->sort_order }}</td>
                        <td>
                            <a href="{{ route('admin.job-statuses.edit', $status) }}" 
                               class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.job-statuses.destroy', $status) }}" 
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container">
    <h1>Customer List</h1>
    <a href="{{ route('customers.create') }}" class="btn btn-primary mb-3">Add Customer</a>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Tax ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr>
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->address }}</td>
                    <td>{{ $customer->taxid }}</td>
                    <td>
                        <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No customers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

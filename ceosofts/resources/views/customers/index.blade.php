@extends('layouts.app')

@section('title', 'Customers')

@section('content')
<div class="container">
    <h1 class="mb-4"><i class="fas fa-users"></i> Customer List</h1>

    <div class="row align-items-center mb-3">
        <!-- Add Customer Button -->
        <div class="col-md-6">
            <a href="{{ route('customers.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Customer
            </a>
        </div>

        <!-- Search Form -->
        <div class="col-md-6">
            <form method="GET" action="{{ route('customers.index') }}" class="d-flex">
                <input type="text" name="search" class="form-control me-2"
                       placeholder="Search by Code, Company, Email" value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Responsive Table -->
    <div class="table-responsive">
        <table class="table table-sm table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Code</th>
                    <th>Company</th>
                    <th>Contact Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    {{-- <th>Tax ID</th> --}}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customers as $customer)
                    <tr>
                        <td>{{ $customer->code }}</td>
                        <!-- เปลี่ยนจาก $customer->name เป็น $customer->companyname -->
                        <td>{{ $customer->companyname }}</td>
                        <td>{{ $customer->contact_name ?: '-' }}</td>
                        <td>{{ $customer->email ?: '-' }}</td>
                        <td>{{ $customer->phone ?: '-' }}</td>
                        <td>{{ $customer->address ?: '-' }}</td>
                        {{-- <td>{{ $customer->taxid ?: '-' }}</td> --}}
                        <td>
                            <a href="{{ route('customers.show', $customer->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" 
                                  onsubmit="return confirm('Are you sure you want to delete this customer?');">
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
                        <td colspan="7" class="text-center text-muted">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="d-flex justify-content-center mt-3">
        {{ $customers->links() }}
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Product Management</h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('products.create') }}" class="btn btn-success px-4 py-2" style="height: 40px;">Add Product</a>
        <form method="GET" action="{{ route('products.index') }}" class="d-flex flex-grow-1 ms-3">
            <input type="text" name="search" class="form-control me-2" style="height: 40px;" placeholder="Search by Code, Name, SKU, Status" value="{{ request('search') }}">
            <button type="submit" class="btn btn-primary" style="height: 40px;">Filters</button>
        </form>
    </div>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Stock</th>
                <th>SKU</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td>{{ $product->code }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->description }}</td>
                    <td>${{ number_format($product->price, 2) }}</td>
                    <td>{{ $product->stock_quantity }}</td>
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <div class="btn-group d-flex justify-content-center" role="group">
                            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-warning btn-sm me-1" style="width: 1px; border-radius: 3px;">Edit</a>
                            <form action="{{ route('products.destroy', $product->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="width: 80px; border-radius: 3px;" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-3">
        {{ $products->links() }}
    </div>
</div>
@endsection

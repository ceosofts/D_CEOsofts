@extends('layouts.app')

@section('title', 'Add New Product')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Product</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        
        <!-- Product Code (Auto-generated) -->
        <div class="form-group mb-3">
            <label for="code" class="form-label">Product Code</label>
            <input type="text" name="code" id="code" class="form-control"
                value="{{ old('code', $generatedCode ?? '') }}" readonly>
        </div>
        
        <!-- Product Name -->
        <div class="form-group mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <!-- Description -->
        <div class="form-group mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <!-- Price -->
        <div class="form-group mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ old('price') }}" required>
        </div>

        <!-- Stock Quantity -->
        <div class="form-group mb-3">
            <label for="stock_quantity" class="form-label">Stock Quantity</label>
            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control" value="{{ old('stock_quantity') }}" required>
        </div>

        <!-- Unit Dropdown -->
        <div class="form-group mb-3">
            <label for="unit_id" class="form-label">Unit</label>
            <select name="unit_id" id="unit_id" class="form-control selectpicker" required>
                <option value="">-- Select Unit --</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- SKU -->
        <div class="form-group mb-3">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" name="sku" id="sku" class="form-control" value="{{ old('sku') }}" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Product</button>
    </form>
</div>
@endsection
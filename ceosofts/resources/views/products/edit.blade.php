@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Product</h1>
    <form action="{{ route('products.update', $product->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="code">Product Code</label>
            <input type="text" name="code" class="form-control" value="{{ $product->code }}" readonly>
        </div>

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" value="{{ $product->name }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control">{{ $product->description }}</textarea>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" value="{{ $product->stock_quantity }}" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="sku">SKU</label>
            <select name="sku" class="form-control" required>
                <option value="Products" {{ $product->sku == 'Products' ? 'selected' : '' }}>Products</option>
                <option value="Parts" {{ $product->sku == 'Parts' ? 'selected' : '' }}>Parts</option>
                <option value="Material" {{ $product->sku == 'Material' ? 'selected' : '' }}>Material</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add Product</h1>
    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" name="sku" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
@endsection

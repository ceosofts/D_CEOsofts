@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add New Product</h1>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        
        {{-- <div class="form-group">
            <label for="code_prefix">Code Prefix</label>
            <input type="text" name="code_prefix" class="form-control" placeholder="Enter prefix (e.g., P)" maxlength="3" required>
        </div> --}}

        <div class="form-group">
            <label for="code">Product Code</label>
            <input type="text" name="code" id="code" class="form-control"
                value="{{ old('code', $generatedCode ?? '') }}" readonly>
        </div>
        
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
            <input type="number" name="price" class="form-control" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="stock_quantity">Stock Quantity</label>
            <input type="number" name="stock_quantity" class="form-control" required>
        </div>

        {{-- <div class="form-group">
            <label>หน่วยสินค้า:</label>
            <select name="unit_id" required>
                <option value="">-- เลือกหน่วยสินค้า --</option>
                @foreach(App\Models\Unit::all() as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div> --}}

        <div class="form-group">
            <label for="unit_id">หน่วยสินค้า:</label>
            <select name="unit_id" id="unit_id" class="form-control selectpicker" required>
                <option value="">-- เลือกหน่วยสินค้า --</option>
                @foreach(App\Models\Unit::all() as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" name="sku" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Save Product</button>
    </form>
</div>
@endsection

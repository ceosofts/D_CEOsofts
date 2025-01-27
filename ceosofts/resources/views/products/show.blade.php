@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $product->name }}</h1>
    <p><strong>Description:</strong> {{ $product->description }}</p>
    <p><strong>Price:</strong> {{ $product->price }}</p>
    <p><strong>Stock:</strong> {{ $product->stock_quantity }}</p>
    <p><strong>SKU:</strong> {{ $product->sku }}</p>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection

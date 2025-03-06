@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h1 class="h3 mb-0">{{ $product->name }}</h1>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Description:</strong> {{ $product->description }}</p>
            <p class="mb-2"><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
            <p class="mb-2"><strong>Stock Quantity:</strong> {{ $product->stock_quantity }}</p>
            <p class="mb-2"><strong>SKU:</strong> {{ $product->sku }}</p>
            <p class="mb-2"><strong>Unit:</strong> {{ $product->unit->name ?? 'N/A' }}</p>
            <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
</div>
@endsection
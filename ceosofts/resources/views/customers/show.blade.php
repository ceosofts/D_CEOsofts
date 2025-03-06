@extends('layouts.app')

@section('title', 'Customer Details')

@section('content')
<div class="container">
    <h1 class="mb-4">Customer Details</h1>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title">{{ $customer->name }}</h5>
            <p class="card-text"><strong>Email:</strong> {{ $customer->email ?: '-' }}</p>
            <p class="card-text"><strong>Phone:</strong> {{ $customer->phone ?: '-' }}</p>
            <p class="card-text"><strong>Address:</strong> {{ $customer->address ?: '-' }}</p>
            <p class="card-text"><strong>Tax ID:</strong> {{ $customer->taxid ?: '-' }}</p>
            <p class="card-text">
                <strong>Created At:</strong> 
                {{ \Carbon\Carbon::parse($customer->created_at)->format('d M Y, H:i:s') }}
            </p>
            <p class="card-text">
                <strong>Updated At:</strong> 
                {{ \Carbon\Carbon::parse($customer->updated_at)->format('d M Y, H:i:s') }}
            </p>
            <a href="{{ route('customers.index') }}" class="btn btn-primary">Back to Customers</a>
        </div>
    </div>
</div>
@endsection
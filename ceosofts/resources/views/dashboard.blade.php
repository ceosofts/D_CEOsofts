<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <h4>Welcome, {{ Auth::user()->name }}!</h4>
                    
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{ __('You are logged in!') }}</p>
                    
                    <div class="mt-4">
                        <h5>Quick Links</h5>
                        <div class="list-group">
                            <a href="{{ route('customers.index') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-people"></i> Manage Customers
                            </a>
                            <a href="{{ route('products.index') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-box"></i> Manage Products
                            </a>
                            <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action">
                                <i class="bi bi-cart"></i> Manage Orders
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
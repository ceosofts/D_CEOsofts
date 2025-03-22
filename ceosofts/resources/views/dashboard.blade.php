<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h5 class="mb-4">{{ __('Welcome to your dashboard!') }}</h5>

                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Customers') }}</h5>
                                    <p class="card-text">{{ __('Manage your customers') }}</p>
                                    <a href="{{ route('customers.index') }}" class="btn btn-light">{{ __('View') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Products') }}</h5>
                                    <p class="card-text">{{ __('Manage your products') }}</p>
                                    <a href="{{ route('products.index') }}" class="btn btn-light">{{ __('View') }}</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">{{ __('Orders') }}</h5>
                                    <p class="card-text">{{ __('Manage your orders') }}</p>
                                    <a href="{{ route('orders.index') }}" class="btn btn-light">{{ __('View') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick actions -->
                    <h5 class="mt-4 mb-3">{{ __('Quick Actions') }}</h5>
                    <div class="list-group">
                        <a href="{{ route('customers.create') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ __('Add New Customer') }}
                            <span class="badge bg-primary rounded-pill">→</span>
                        </a>
                        <a href="{{ route('products.create') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ __('Add New Product') }}
                            <span class="badge bg-primary rounded-pill">→</span>
                        </a>
                        <a href="{{ route('orders.create') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            {{ __('Create New Order') }}
                            <span class="badge bg-primary rounded-pill">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
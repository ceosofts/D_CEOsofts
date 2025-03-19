@extends('layouts.app')

@section('title', $title ?? 'Admin Dashboard')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h1>Admin Dashboard</h1>
                <span class="badge bg-primary">Admin</span>
            </div>
            <p class="text-muted">Welcome to the administrator dashboard</p>
        </div>
    </div>
    
    <!-- Admin Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Users</h5>
                            <h2 class="display-6">{{ $userCount }}</h2>
                        </div>
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.users.index') }}" class="text-white text-decoration-none">View Users</a>
                    <i class="bi bi-arrow-right-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Departments</h5>
                            <h2 class="display-6">{{ \App\Models\Department::count() ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-diagram-3-fill fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.departments.index') }}" class="text-white text-decoration-none">View Departments</a>
                    <i class="bi bi-arrow-right-circle text-white"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Companies</h5>
                            <h2 class="display-6">{{ \App\Models\Company::count() ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-building fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.companies.index') }}" class="text-dark text-decoration-none">View Companies</a>
                    <i class="bi bi-arrow-right-circle text-dark"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Positions</h5>
                            <h2 class="display-6">{{ \App\Models\Position::count() ?? 0 }}</h2>
                        </div>
                        <i class="bi bi-person-badge-fill fs-1"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a href="{{ route('admin.positions.index') }}" class="text-white text-decoration-none">View Positions</a>
                    <i class="bi bi-arrow-right-circle text-white"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Quick Links -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Links</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action active">
                                    <i class="bi bi-people"></i> User Management
                                </div>
                                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-person-plus"></i> Manage Users
                                </a>
                                <a href="{{ route('admin.departments.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-diagram-3"></i> Manage Departments
                                </a>
                                <a href="{{ route('admin.positions.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-briefcase"></i> Manage Positions
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action active">
                                    <i class="bi bi-gear"></i> System Settings
                                </div>
                                <a href="{{ route('admin.companies.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-building"></i> Companies
                                </a>
                                <a href="{{ route('admin.units.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-rulers"></i> Units
                                </a>
                                <a href="{{ route('admin.prefixes.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-type"></i> Prefixes
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="list-group">
                                <div class="list-group-item list-group-item-action active">
                                    <i class="bi bi-shop"></i> Business Settings
                                </div>
                                <a href="{{ route('admin.tax.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-percent"></i> Tax Settings
                                </a>
                                <a href="{{ route('admin.item_statuses.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-list-check"></i> Item Statuses
                                </a>
                                <a href="{{ route('admin.payment_statuses.index') }}" class="list-group-item list-group-item-action">
                                    <i class="bi bi-cash-coin"></i> Payment Statuses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Users Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Users</h5>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Department</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentUsers = \App\Models\User::with('department')->latest()->take(5)->get();
                                @endphp
                                
                                @forelse($recentUsers as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>{{ $user->department->name ?? 'N/A' }}</td>
                                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No users found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card-footer {
        border-top: 1px solid rgba(255,255,255,0.15);
        padding: 0.75rem 1.25rem;
        background: rgba(0,0,0,0.03);
    }
    
    .list-group-item i {
        margin-right: 8px;
    }
</style>
@endpush

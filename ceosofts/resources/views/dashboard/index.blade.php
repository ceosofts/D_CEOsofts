@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-8">
                            <h3 class="mb-0"><i class="fa fa-dashboard"></i> Dashboard</h3>
                        </div>
                        <div class="col-4 text-right">
                            <span class="badge badge-success">Email Verified</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="welcome-message">
                        <h4>Welcome to CEOSOFTS</h4>
                        <p>Your email has been verified successfully. You now have full access to the system.</p>
                    </div>
                    
                    <!-- Quick action buttons similar to invoice-show -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <a href="{{ url('/profile') }}" class="btn btn-primary btn-block">
                                <i class="fa fa-user"></i> My Profile
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('/invoices') }}" class="btn btn-info btn-block">
                                <i class="fa fa-file-invoice"></i> Invoices
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('/settings') }}" class="btn btn-secondary btn-block">
                                <i class="fa fa-cog"></i> Settings
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ url('/help') }}" class="btn btn-outline-dark btn-block">
                                <i class="fa fa-question-circle"></i> Help
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

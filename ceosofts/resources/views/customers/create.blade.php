@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Customer</h1>
    
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <!-- Name -->
        <div class="form-group mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <!-- Email -->
        <div class="form-group mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <!-- Phone -->
        <div class="form-group mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="text" name="phone" id="phone" 
                   class="form-control @error('phone') is-invalid @enderror" 
                   value="{{ old('phone') }}">
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <!-- Address -->
        <div class="form-group mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" 
                      class="form-control @error('address') is-invalid @enderror" 
                      rows="3">{{ old('address') }}</textarea>
            @error('address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <!-- Tax ID -->
        <div class="form-group mb-3">
            <label for="taxid" class="form-label">Tax ID</label>
            <input type="text" name="taxid" id="taxid" 
                   class="form-control @error('taxid') is-invalid @enderror" 
                   value="{{ old('taxid') }}">
            @error('taxid')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Save
        </button>
        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
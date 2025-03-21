@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit Customer</h1>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Hidden fields for ID and Code -->
                <input type="hidden" name="id" value="{{ $customer->id }}">
                <input type="hidden" name="code" value="{{ $customer->code }}">

                <!-- Customer Code (Read-Only) -->
                <div class="mb-3">
                    <label for="code" class="form-label">Customer Code</label>
                    <input type="text" name="code" id="code" class="form-control" 
                           value="{{ old('code', $customer->code) }}" readonly>
                </div>

                <!-- Company Name -->
                <div class="mb-3">
                    <label for="companyname" class="form-label">Company Name</label>
                    <input type="text" name="companyname" id="companyname"
                           class="form-control @error('companyname') is-invalid @enderror"
                           value="{{ old('companyname', $customer->companyname) }}" required>
                    @error('companyname')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contact Name -->
                <div class="mb-3">
                    <label for="contact_name" class="form-label">Contact Name</label>
                    <input type="text" name="contact_name" id="contact_name"
                           class="form-control @error('contact_name') is-invalid @enderror"
                           value="{{ old('contact_name', $customer->contact_name) }}" required>
                    @error('contact_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $customer->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" id="phone"
                           class="form-control @error('phone') is-invalid @enderror"
                           value="{{ old('phone', $customer->phone) }}">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Address -->
                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea name="address" id="address"
                              class="form-control @error('address') is-invalid @enderror"
                              rows="3">{{ old('address', $customer->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tax ID -->
                <div class="mb-3">
                    <label for="taxid" class="form-label">Tax ID</label>
                    <input type="text" name="taxid" id="taxid"
                           class="form-control @error('taxid') is-invalid @enderror"
                           value="{{ old('taxid', $customer->taxid) }}">
                    @error('taxid')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Branch -->
                <div class="mb-3">
                    <label for="branch" class="form-label">Branch</label>
                    <input type="text" name="branch" id="branch"
                           class="form-control @error('branch') is-invalid @enderror"
                           value="{{ old('branch', $customer->branch) }}">
                    @error('branch')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
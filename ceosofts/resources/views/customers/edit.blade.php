
@extends('layouts.app')

@section('title', 'Edit Customer')

@section('content')
<div class="container">
    <h1>Edit Customer</h1>
    <form action="{{ route('customers.update', $customer->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ $customer->name }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ $customer->email }}" required>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control" value="{{ $customer->phone }}">
        </div>
        <div class="form-group mb-3">
            <label for="address">Address</label>
            <textarea name="address" id="address" class="form-control">{{ $customer->address }}</textarea>
        </div>
        <div class="form-group mb-3">
            <label for="taxid">Tax ID</label>
            <input type="text" name="taxid" id="taxid" class="form-control" value="{{ $customer->taxid }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
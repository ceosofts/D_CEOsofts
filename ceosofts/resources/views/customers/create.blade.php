
@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="container">
    <h1>Add New Customer</h1>
    <form action="{{ route('customers.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" class="form-control">
        </div>
        <div class="form-group mb-3">
            <label for="address">Address</label>
            <textarea name="address" id="address" class="form-control"></textarea>
        </div>
        <div class="form-group mb-3">
            <label for="taxid">Tax ID</label>
            <input type="text" name="taxid" id="taxid" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Save</button>
    </form>
</div>
@endsection

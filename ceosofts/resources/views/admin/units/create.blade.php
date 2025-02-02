@extends('layouts.app')

@section('title', 'Add Unit')

@section('content')
<div class="container">
    <h1>Add New Unit</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Unit Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3"><i class="bi bi-check-lg"></i> Save</button>
                <a href="{{ route('admin.units.index') }}" class="btn btn-secondary mt-3"><i class="bi bi-arrow-left"></i> Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

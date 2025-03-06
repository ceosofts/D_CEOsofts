@extends('layouts.app')

@section('title', 'Add Unit')

@section('content')
<div class="container">
    <h1 class="mb-4">Add New Unit</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Unit Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="bi bi-check-lg"></i> Save
                    </button>
                    <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Edit Tax')

@section('content')
<div class="container">
    <h1>Edit Tax</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.tax.update', $tax->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Tax Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $tax->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="rate">Tax Rate (%)</label>
                    <input type="number" id="rate" name="rate" class="form-control @error('rate') is-invalid @enderror"
                        value="{{ old('rate', $tax->rate) }}" step="0.01" min="0" required>
                    @error('rate')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success mt-3">
                    <i class="bi bi-check-lg"></i> Update
                </button>
                <a href="{{ route('admin.tax.index') }}" class="btn btn-secondary mt-3">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </form>
        </div>
    </div>
</div>
@endsection

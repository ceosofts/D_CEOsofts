@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h1 class="mb-4">Edit User</h1>

    {{-- แสดง Error ถ้ามี --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $user->name) }}" 
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="form-control @error('email') is-invalid @enderror" 
                        value="{{ old('email', $user->email) }}" 
                        required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role --}}
                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select 
                        name="role" 
                        id="role" 
                        class="form-select @error('role') is-invalid @enderror">
                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="leader" {{ old('role', $user->role) == 'leader' ? 'selected' : '' }}>Leader</option>
                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Department --}}
                <div class="mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select 
                        name="department_id" 
                        id="department_id" 
                        class="form-select @error('department_id') is-invalid @enderror">
                        @foreach ($departments as $department)
                            <option 
                                value="{{ $department->id }}" 
                                {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-lg"></i> Update
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
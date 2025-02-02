@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container">
    <h1>Edit User</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ $user->role == 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="leader" {{ $user->role == 'leader' ? 'selected' : '' }}>Leader</option>
                        <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" class="form-control">
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ $user->department_id == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

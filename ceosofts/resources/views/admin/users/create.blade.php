@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container">
    <h1>Add New User</h1>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                {{-- <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="manager">Manager</option>
                        <option value="leader">Leader</option>
                        <option value="user">User</option>
                    </select>
                </div> --}}

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" class="form-control">
                        <option value="">No Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Save</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Back</a>
            </form>
        </div>
    </div>
</div>
@endsection

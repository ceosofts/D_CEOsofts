@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>เพิ่มผู้ใช้</h1>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label">ชื่อ</label>
                <input type="text" class="form-control" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน</label>
                <input type="password" class="form-control" name="password" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="manager">Manager</option>
                    <option value="leader">Leader</option>
                    <option value="user">User</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="department_id" class="form-label">แผนก</label>
                <select name="department_id" class="form-control">
                    <option value="">ไม่มีแผนก</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-success">บันทึก</button>
        </form>
    </div>
@endsection

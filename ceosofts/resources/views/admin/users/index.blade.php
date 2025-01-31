@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>จัดการผู้ใช้</h1>
        {{-- <a href="{{ route('users.create') }}" class="btn btn-primary">เพิ่มผู้ใช้</a> --}}
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">เพิ่มผู้ใช้</a>
        


        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ชื่อ</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>แผนก</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>{{ $user->department->name ?? 'ไม่มีแผนก' }}</td>
                        <td>
                            {{-- <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">แก้ไข</a> --}}
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>

                            {{-- <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่?')">ลบ</button>
                            </form> --}}

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่?')">ลบ</button>
                            </form>
                            
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $users->links() }}
    </div>
@endsection

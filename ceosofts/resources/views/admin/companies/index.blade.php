@extends('layouts.app')

@section('title', 'Company Management')

@section('content')
<div class="container">
    <h1 class="mb-4">รายการบริษัท</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.companies.create') }}" class="btn btn-success">
            <i class="bi bi-plus-lg"></i> เพิ่มบริษัทใหม่
        </a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ชื่อบริษัท</th>
                <th>สาขา</th> <!-- ✅ เพิ่มคอลัมน์ใหม่ -->
                <th>ที่อยู่</th>
                <th>เบอร์โทร</th>
                <th>อีเมล</th>
                <th>ผู้ติดต่อ</th>
                <th>จัดการ</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td>{{ $company->company_name }}</td>
                    <td>{{ $company->branch_description ?? 'ไม่ระบุ' }}</td> <!-- ✅ แสดง branch_description -->
                    <td>{{ $company->address }}</td>
                    <td>{{ $company->phone }}</td>
                    <td>{{ $company->email }}</td>
                    <td>{{ $company->contact_person }}</td>
                    <td>
                        <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                        <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('คุณแน่ใจหรือไม่?')">ลบ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">ไม่มีข้อมูลบริษัท</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

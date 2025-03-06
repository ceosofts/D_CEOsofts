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

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ชื่อบริษัท</th>
                    <th>สาขา</th>
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
                        <td>{{ $company->branch_description ?? 'ไม่ระบุ' }}</td>
                        <td>{{ $company->address }}</td>
                        <td>{{ $company->phone }}</td>
                        <td>{{ $company->email }}</td>
                        <td>{{ $company->contact_person }}</td>
                        <td>
                            <a href="{{ route('admin.companies.edit', $company->id) }}" class="btn btn-warning btn-sm">แก้ไข</a>
                            <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="d-inline" onsubmit="return confirm('คุณแน่ใจหรือไม่?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">ลบ</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">ไม่มีข้อมูลบริษัท</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- แสดง Pagination ถ้ามี -->
    <div class="d-flex justify-content-center">
        {{ $companies->links() }}
    </div>
</div>
@endsection